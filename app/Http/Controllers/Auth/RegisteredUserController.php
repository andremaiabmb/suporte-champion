<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tela de registro
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Processa o cadastro
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => ['required','string','max:255'],
            'city'              => ['required','string','max:120'],
            'country_of_origin' => ['required','string','max:100'],
            'date_of_birth'     => ['required','date','before:today','after:1900-01-01'],
            'email'             => [
                'required','string','email','max:255','unique:'.User::class,
                // Domínio @champion.edu estrito
                function($attr, $value, $fail) {
                    if (!preg_match('/@champion\.edu\z/i', $value)) {
                        $fail('O e-mail deve ser do domínio @champion.edu.');
                    }
                },
            ],
            'password'          => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'city'              => $validated['city'],
            'country_of_origin' => $validated['country_of_origin'],
            'date_of_birth'     => $validated['date_of_birth'],
            'role'              => 'aluno', // padrão ao se auto-registrar
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Se usa verificação de e-mail, o middleware 'verified' vai exigir confirmação no acesso ao dashboard
        return redirect()->intended(route('home'));
    }
}
