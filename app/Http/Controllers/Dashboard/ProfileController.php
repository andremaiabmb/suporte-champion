<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // Código “estável” do QR:
        // - se houver coluna registration_code na tabela users, usa ela;
        // - senão gera um slug previsível com id + email (hash curto).
        $code = $user->registration_code
            ?? ('U'.$user->id.'-'.substr(hash('crc32b', $user->email.$user->id), 0, 8));

        return view('profile.show', [
            'user' => $user,
            'qrCode' => $code,
        ]);
    }

    // Tela “limpa” só com o QR para imprimir/mostrar
    public function qrPage(Request $request)
    {
        $user = $request->user();
        $code = $user->registration_code
            ?? ('U'.$user->id.'-'.substr(hash('crc32b', $user->email.$user->id), 0, 8));

        return view('profile.qr', [
            'user' => $user,
            'qrCode' => $code,
        ]);
    }
}
