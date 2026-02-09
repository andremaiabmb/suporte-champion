<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class EventAttendanceController extends Controller
{
    /** Tela de credenciamento (mostra scanner/entrada de texto) */
    public function attendance(Request $request, $eventId)
    {
        $event = DB::table('events')->where('id', $eventId)->first();
        abort_unless($event, 404);

        $date = $request->date ?: now()->toDateString();

        // ---------- MÉTRICAS ----------
        $totalRegs = 0;
        foreach (['event_registrations','registrations','event_users'] as $tbl) {
            if (Schema::hasTable($tbl)) {
                $totalRegs = (int) DB::table($tbl)->where('event_id', $event->id)->count();
                break;
            }
        }

        $hasAtt = Schema::hasTable('event_attendances');

        $checkins = $hasAtt
            ? (int) DB::table('event_attendances')
                ->where('event_id', $event->id)
                ->where('date', $date)
                ->whereNotNull('checkin_at')->count()
            : 0;

        $checkouts = $hasAtt
            ? (int) DB::table('event_attendances')
                ->where('event_id', $event->id)
                ->where('date', $date)
                ->whereNotNull('checkout_at')->count()
            : 0;

        $metrics = [
            'total_regs' => $totalRegs,
            'checkins'   => $checkins,
            'checkouts'  => $checkouts,
        ];

        // ---------- HISTÓRICO DO DIA (PERSISTENTE) ----------
        $history = [];
        if ($hasAtt) {
            $rows = DB::table('event_attendances AS ea')
                ->leftJoin('users AS u', 'u.id', '=', 'ea.user_id')
                ->select(
                    'ea.checkin_at',
                    'ea.checkout_at',
                    'ea.date',
                    'u.name',
                    'u.email',
                    'u.country_of_origin',
                    'u.locale'
                )
                ->where('ea.event_id', $event->id)
                ->where('ea.date', $date)
                ->orderByDesc(DB::raw("COALESCE(ea.checkout_at, ea.checkin_at)"))
                ->limit(1000)
                ->get();

            foreach ($rows as $r) {
                if ($r->checkin_at) {
                    $history[] = [
                        'action'    => 'checkin',
                        'timestamp' => $r->checkin_at,
                        'name'      => $r->name ?? '—',
                        'email'     => $r->email ?? '—',
                    ];
                }
                if ($r->checkout_at) {
                    $history[] = [
                        'action'    => 'checkout',
                        'timestamp' => $r->checkout_at,
                        'name'      => $r->name ?? '—',
                        'email'     => $r->email ?? '—',
                    ];
                }
            }

            usort($history, function($a,$b){
                return strcmp($b['timestamp'] ?? '', $a['timestamp'] ?? '');
            });
        }

        return view('dashboard.admin.events.attendance', [
            'event'   => $event,
            'date'    => $date,
            'metrics' => $metrics,
            'history' => $history,
        ]);
    }

    /** Recebe o scan (Eyoyo, câmera, manual) e registra presença diária */
    public function scan(Request $request, $eventId)
    {
        $payload = trim((string)$request->input('payload', ''));
        $mode    = $request->input('mode', 'auto');        // 'auto' | 'checkin' | 'checkout'
        $date    = $request->input('date', now()->toDateString());
        $method  = $request->input('method', 'eyoyo');     // 'eyoyo' | 'camera' | 'manual'

        if ($payload === '') {
            return response()->json(['ok' => false, 'error' => 'EMPTY_PAYLOAD'], 422);
        }

        // 1) Usuário pelo QR do banco (users.qr_token) ou e-mail (fallback manual)
        $user = DB::table('users')->where('qr_token', $payload)->first();

        if (!$user) {
            $last = trim(Str::afterLast($payload, '/'));
            if ($last && $last !== $payload) {
                $user = DB::table('users')->where('qr_token', $last)->first();
            }
        }

        if (!$user && filter_var($payload, FILTER_VALIDATE_EMAIL)) {
            $user = DB::table('users')->where('email', $payload)->first();
        }

        if (!$user) {
            return response()->json(['ok' => false, 'error' => 'USER_NOT_FOUND'], 404);
        }

        // 2) Evento
        $event = DB::table('events')->where('id', $eventId)->first();
        if (!$event) {
            return response()->json(['ok' => false, 'error' => 'EVENT_NOT_FOUND'], 404);
        }

        // 3) Validar se o usuário está inscrito no evento
        $registered = $this->userIsRegisteredInEvent($event->id, $user);
        if (!$registered) {
            return response()->json(['ok' => false, 'error' => 'NOT_REGISTERED'], 422);
        }

        // 4) Tabela de presenças diárias (cria se não existir)
        $this->ensureAttendanceTable();

        $row = DB::table('event_attendances')
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('date', $date)
            ->first();

        $now = now()->toDateTimeString();

        // Regras: 1 check-in e 1 check-out por dia (sem novo ciclo)
        if ($row) {
            $hasCheckin  = !empty($row->checkin_at);
            $hasCheckout = !empty($row->checkout_at);

            // Tentativa de 2º check-in no mesmo dia
            if ($mode === 'checkin' && $hasCheckin) {
                return response()->json([
                    'ok' => false,
                    'error' => 'ALREADY_CHECKED_IN',
                    'message' => 'Este participante já realizou check-in hoje.',
                    'user' => ['name' => $user->name, 'email' => $user->email],
                    'date' => $date,
                ], 422);
            }

            // Tentativa de 2º check-out no mesmo dia
            if ($mode === 'checkout' && $hasCheckout) {
                return response()->json([
                    'ok' => false,
                    'error' => 'ALREADY_CHECKED_OUT',
                    'message' => 'Este participante já realizou check-out hoje.',
                    'user' => ['name' => $user->name, 'email' => $user->email],
                    'date' => $date,
                ], 422);
            }

            // Modo auto:
            // - se tem check-in e não tem check-out => faz check-out
            // - se tem check-in e tem check-out => já completou o dia -> bloqueia novo ciclo
            // - se não tem check-in => faz check-in
            if ($mode === 'auto') {
                if ($hasCheckin && !$hasCheckout) {
                    DB::table('event_attendances')->where('id', $row->id)->update([
                        'checkout_at' => $now,
                        'method'      => $method,
                    ]);
                    return $this->jsonSuccess('checkout', $user, $date, $now);
                } elseif ($hasCheckin && $hasCheckout) {
                    return response()->json([
                        'ok' => false,
                        'error' => 'ALREADY_COMPLETED',
                        'message' => 'Check-in e check-out do dia já foram realizados.',
                        'user' => ['name' => $user->name, 'email' => $user->email],
                        'date' => $date,
                    ], 422);
                } else {
                    // sem check-in ainda
                    DB::table('event_attendances')->where('id', $row->id)->update([
                        'checkin_at' => $now,
                        'method'     => $method,
                    ]);
                    return $this->jsonSuccess('checkin', $user, $date, $now);
                }
            }

            // Modo explícito
            if ($mode === 'checkin') {
                DB::table('event_attendances')->where('id', $row->id)->update([
                    'checkin_at' => $row->checkin_at ?: $now,
                    'method'     => $method,
                ]);
                return $this->jsonSuccess('checkin', $user, $date, $now);
            }

            if ($mode === 'checkout') {
                // precisa ter check-in antes de permitir check-out
                if (!$hasCheckin) {
                    return response()->json([
                        'ok' => false,
                        'error' => 'CHECKIN_REQUIRED',
                        'message' => 'É necessário realizar o check-in antes do check-out.',
                        'user' => ['name' => $user->name, 'email' => $user->email],
                        'date' => $date,
                    ], 422);
                }
                DB::table('event_attendances')->where('id', $row->id)->update([
                    'checkout_at' => $now,
                    'method'      => $method,
                ]);
                return $this->jsonSuccess('checkout', $user, $date, $now);
            }
        } else {
            // Sem registro do dia ainda -> apenas check-in é válido
            if ($mode === 'checkout') {
                return response()->json([
                    'ok' => false,
                    'error' => 'CHECKIN_REQUIRED',
                    'message' => 'É necessário realizar o check-in antes do check-out.',
                    'user' => ['name' => $user->name, 'email' => $user->email],
                    'date' => $date,
                ], 422);
            }
            // auto ou checkin => cria check-in
            DB::table('event_attendances')->insert([
                'event_id'   => $event->id,
                'user_id'    => $user->id,
                'date'       => $date,
                'checkin_at' => $now,
                'checkout_at'=> null,
                'method'     => $method,
            ]);
            return $this->jsonSuccess('checkin', $user, $date, $now);
        }

        // Fallback (em teoria não chega aqui)
        return response()->json(['ok' => false, 'error' => 'UNKNOWN'], 500);
    }

    /* ===================== helpers ===================== */

    private function jsonSuccess(string $action, object $user, string $date, string $now)
    {
        $lang = $user->locale ?: 'pt';
        $hello = [
            'pt' => 'Bem-vindo(a)',
            'en' => 'Welcome',
            'es' => 'Bienvenido(a)',
            'fr' => 'Bienvenue',
        ][$lang] ?? 'Welcome';

        $cc = $this->normalizeCountryCode($user->country_of_origin);
        $flagUrl = $cc ? "https://flagcdn.com/w80/".strtolower($cc).".png" : null;

        return response()->json([
            'ok'      => true,
            'action'  => $action, // 'checkin' | 'checkout'
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'greet'   => $hello.' '.$user->name.'!',
            'flag'    => $flagUrl,
            'date'    => $date,
            'now'     => $now,
        ]);
    }

    /** Verifica se existe tabela de presenças; se não, cria (útil no dev). */
    private function ensureAttendanceTable(): void
    {
        if (!Schema::hasTable('event_attendances')) {
            DB::statement("
                CREATE TABLE IF NOT EXISTS event_attendances (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    event_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    date TEXT NOT NULL,
                    checkin_at TEXT NULL,
                    checkout_at TEXT NULL,
                    method TEXT NULL
                )
            ");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_event_att ON event_attendances(event_id, user_id, date)");
        }
    }

    /** Retorna true se o usuário estiver inscrito no evento. */
    private function userIsRegisteredInEvent(int $eventId, object $user): bool
    {
        $tables = ['event_registrations', 'registrations', 'event_users', 'event_participants'];

        foreach ($tables as $tbl) {
            if (!Schema::hasTable($tbl)) continue;

            $cols = $this->columns($tbl);
            $q = DB::table($tbl);

            if (in_array('event_id', $cols, true)) {
                $q->where('event_id', $eventId);
            } elseif (in_array('event', $cols, true)) {
                $q->where('event', $eventId);
            } else {
                continue;
            }

            if (in_array('user_id', $cols, true)) {
                $q->where('user_id', $user->id);
            } elseif (in_array('email', $cols, true)) {
                $q->where('email', $user->email);
            } else {
                continue;
            }

            if (in_array('status', $cols, true)) {
                $q->whereIn('status', ['confirmed', 'confirmado', 'active', 'ativo', 'paid', 'pago']);
            }

            if ($q->exists()) return true;
        }

        return false;
    }

    /** Lista nomes de colunas de uma tabela (lowercase) */
    private function columns(string $table): array
    {
        try {
            return array_map('strtolower', Schema::getColumnListing($table));
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** Converte nomes comuns de país -> ISO-3166 alpha-2 */
    private function normalizeCountryCode(?string $val): ?string
    {
        if (!$val) return null;
        $v = trim($val);

        if (preg_match('/^[A-Za-z]{2}$/', $v)) {
            return strtoupper($v);
        }

        $map = [
            'brasil' => 'BR', 'portugal' => 'PT', 'angola' => 'AO', 'moçambique' => 'MZ',
            'brazil' => 'BR', 'united states' => 'US', 'usa' => 'US', 'france' => 'FR', 'spain' => 'ES',
            'argentina' => 'AR', 'mexico' => 'MX', 'canada' => 'CA', 'italy' => 'IT', 'germany' => 'DE',
        ];

        $slug = mb_strtolower($v);
        return $map[$slug] ?? null;
    }
}
