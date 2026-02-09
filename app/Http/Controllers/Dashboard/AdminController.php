<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Janela de 30 dias para métricas "recentes"
        $since = Carbon::now()->subDays(30);

        // ===== USERS =====
        $total_users = $this->countIfTable('users');
        $active_users_30d = 0;

        if ($this->tableHas('users', 'last_login_at')) {
            $active_users_30d = DB::table('users')
                ->whereNotNull('last_login_at')
                ->where('last_login_at', '>=', $since)
                ->count();
        } elseif ($this->tableHas('users', 'updated_at')) {
            // fallback: "atividade" aproximada por updated_at
            $active_users_30d = DB::table('users')
                ->whereNotNull('updated_at')
                ->where('updated_at', '>=', $since)
                ->count();
        }

        $latest_users = [];
        if ($this->tableExists('users')) {
            $latest_users = DB::table('users')
                ->select(['id', 'name', 'email', 'created_at'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        // ===== EVENTS =====
        $total_events = 0;
        if ($this->tableExists('events')) {
            $total_events = DB::table('events')->count();
        } elseif ($this->tableExists('dashboard_events')) {
            $total_events = DB::table('dashboard_events')->count();
        }

        // ===== POSTS =====
        $total_posts = 0;
        $latest_posts = [];

        if ($this->tableExists('posts')) {
            $total_posts = DB::table('posts')->count();

            // published_at (se existir) para status na view
            $columns = $this->columns('posts');
            $select = ['id', 'title', 'updated_at'];
            if (in_array('published_at', $columns)) {
                $select[] = 'published_at';
            }

            $latest_posts = DB::table('posts')
                ->select($select)
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get();
        }

        // ===== FAQS =====
        $total_faqs = $this->countIfTable('faqs');

        // ===== STORE / ORDERS =====
        [$ordersTable, $amountCol, $statusCol, $customerCol, $createdCol] = $this->detectOrdersTable();

        $store_orders_count = 0;
        $store_revenue_30d = 0.0;
        $latest_orders = [];

        if ($ordersTable) {
            $store_orders_count = DB::table($ordersTable)->count();

            // soma receita 30d
            if ($amountCol) {
                $store_revenue_30d = DB::table($ordersTable)
                    ->where($createdCol, '>=', $since)
                    ->sum($amountCol);
            }

            // pedidos recentes
            $select = ['id'];
            if ($customerCol) $select[] = $customerCol;
            if ($amountCol)   $select[] = $amountCol;
            if ($statusCol)   $select[] = $statusCol;
            if ($createdCol)  $select[] = $createdCol;

            $latest_orders = DB::table($ordersTable)
                ->select($select)
                ->when($createdCol, fn($q) => $q->orderByDesc($createdCol), fn($q) => $q->orderByDesc('id'))
                ->limit(5)
                ->get()
                ->map(function ($row) use ($customerCol, $amountCol, $statusCol) {
                    // normaliza para os nomes que a view espera
                    $row->customer_name = $customerCol ? ($row->{$customerCol} ?? null) : null;
                    $row->total         = $amountCol ? ($row->{$amountCol} ?? null) : null;
                    $row->status        = $statusCol ? ($row->{$statusCol} ?? null) : null;
                    return $row;
                });
        }

        return view('dashboard.admin.index', [
            'total_users'         => $total_users,
            'total_events'        => $total_events,
            'total_posts'         => $total_posts,
            'total_faqs'          => $total_faqs,
            'active_users_30d'    => $active_users_30d,
            'store_orders_count'  => $store_orders_count,
            'store_revenue_30d'   => $store_revenue_30d,
            'latest_users'        => $latest_users,
            'latest_posts'        => $latest_posts,
            'latest_orders'       => $latest_orders,
        ]);
    }

    /* ======================== HELPERS ======================== */

    private function tableExists(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function tableHas(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function countIfTable(string $table): int
    {
        return $this->tableExists($table) ? (int) DB::table($table)->count() : 0;
    }

    private function columns(string $table): array
    {
        try {
            return Schema::getColumnListing($table);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Detecta a tabela de pedidos e colunas mais prováveis.
     * Prioriza: orders > store_orders
     * amount/total: amount_total, total, amount
     * status: status, state
     * cliente: customer_name, customer, name, buyer_name
     * created: created_at, ordered_at
     *
     * @return array [table, amountCol, statusCol, customerCol, createdCol]
     */
    private function detectOrdersTable(): array
    {
        $candidates = ['orders', 'store_orders'];
        $table = null;

        foreach ($candidates as $t) {
            if ($this->tableExists($t)) {
                $table = $t;
                break;
            }
        }

        if (!$table) {
            return [null, null, null, null, null];
        }

        $cols = $this->columns($table);

        $amountCol  = $this->firstExisting($cols, ['amount_total', 'total', 'amount', 'grand_total', 'price_total']);
        $statusCol  = $this->firstExisting($cols, ['status', 'state', 'order_status']);
        $customerCol= $this->firstExisting($cols, ['customer_name', 'customer', 'name', 'buyer_name']);
        $createdCol = $this->firstExisting($cols, ['created_at', 'ordered_at', 'date']);

        return [$table, $amountCol, $statusCol, $customerCol, $createdCol];
    }

    private function firstExisting(array $columns, array $options): ?string
    {
        foreach ($options as $opt) {
            if (in_array($opt, $columns)) return $opt;
        }
        return null;
    }
}
