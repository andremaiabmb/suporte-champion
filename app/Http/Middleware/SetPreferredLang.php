<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetPreferredLang
{
    /** Idiomas permitidos */
    private array $allowed = ['pt_BR', 'en', 'es', 'fr'];

    /** Mapeamento de normalização */
    private array $map = [
        'pt'    => 'pt_BR',
        'pt-br' => 'pt_BR',
        'pt-BR' => 'pt_BR',
        'pt_br' => 'pt_BR',
        'en-US' => 'en',
        'en_GB' => 'en',
    ];

    private function normalize(?string $v): ?string
    {
        if ($v === null) return null;
        $v = trim($v);
        if ($v === '') return null;
        return $this->map[$v] ?? $v;
    }

    public function handle(Request $request, Closure $next): Response
    {
        // 0) Sessão primeiro (compatível com seu fluxo anterior)
        $sessionLocale = $this->normalize($request->session()->get('locale'));

        // 1) Usuário logado
        $locale = $sessionLocale ?: (Auth::check() ? $this->normalize(Auth::user()->preferred_lang) : null);

        // 2) Cookie
        if (!$locale) {
            $locale = $this->normalize($request->cookie('preferred_lang'));
        }

        // 3) Padrão do app
        if (!$locale) {
            $locale = config('app.locale', 'pt_BR');
        }

        // Valida
        if (!in_array($locale, $this->allowed, true)) {
            $locale = config('app.locale', 'pt_BR');
        }

        // Aplica
        App::setLocale($locale);
        try { \Carbon\Carbon::setLocale($locale); } catch (\Throwable $e) {}

        // Compartilha na view p/ navbar
        view()->share('currentLocale', $locale);

        return $next($request);
    }
}
