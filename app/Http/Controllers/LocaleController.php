<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    /**
     * Idiomas permitidos no sistema.
     */
    private array $allowed = ['pt_BR', 'en', 'es', 'fr'];

    /**
     * Mapa de normalização de códigos de idioma de entrada.
     */
    private array $map = [
        'pt'    => 'pt_BR',
        'pt-br' => 'pt_BR',
        'pt-BR' => 'pt_BR',
        'pt_br' => 'pt_BR',
        'en-US' => 'en',
        'en_GB' => 'en',
    ];

    private function normalize(string $value): string
    {
        $v = trim($value);
        return $this->map[$v] ?? $v;
    }

    /**
     * Salva a preferência de idioma:
     * - Sempre grava cookie "preferred_lang" (1 ano)
     * - Grava também na sessão (compat c/ seu fluxo atual)
     * - Se logado, salva em users.preferred_lang
     *
     * Espera JSON: { "locale": "pt" | "pt_BR" | "en" | "es" | "fr" }
     * Responde JSON: { ok: true, locale: "pt_BR" }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'locale' => ['required', 'string', 'max:10'],
        ]);

        $locale = $this->normalize($data['locale']);

        if (!in_array($locale, $this->allowed, true)) {
            return response()->json([
                'ok'      => false,
                'message' => 'Idioma inválido.',
            ], 422);
        }

        // 1) Cookie (1 ano)
        $cookie = cookie(
            'preferred_lang',
            $locale,
            60 * 24 * 365,
            path: '/',
            secure: false,
            httpOnly: false,
            sameSite: 'lax'
        );

        // 2) Sessão (compat com fluxo anterior)
        session(['locale' => $locale]);

        // 3) Usuário logado → salvar no perfil
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->preferred_lang !== $locale) {
                $user->preferred_lang = $locale;
                $user->save();
            }
        }

        return response()
            ->json(['ok' => true, 'locale' => $locale])
            ->withCookie($cookie);
    }
}
