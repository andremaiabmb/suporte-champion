<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /** Perfil com bloco “Meu QR Code” (usa SEMPRE users.qr_token) */
    public function show(Request $request)
    {
        $user = $request->user();

        // Se ainda não houver qr_token, gera e salva uma única vez
        if (empty($user->qr_token)) {
            $base = 'U'.$user->id.'-'.substr(hash('crc32b', $user->email.$user->id), 0, 8);
            $token = $base; $i = 0;
            while (DB::table('users')->where('qr_token', $token)->where('id','!=',$user->id)->exists()) {
                $i++; $token = $base.'-'.$i;
            }
            $user->qr_token = $token;
            $user->save();
        }

        return view('profile.show', [
            'user'   => $user,
            'qrCode' => $user->qr_token,   // <- vem do banco
            'qrImg'  => $user->qr_image,   // <- se você quiser exibir imagem pronta
        ]);
    }

    /** Página limpa só com o QR (para imprimir/mostrar) */
    public function qrPage(Request $request)
    {
        $user = $request->user();

        if (empty($user->qr_token)) {
            return redirect()->route('profile.show')->with('status', 'QR do perfil foi inicializado.');
        }

        return view('profile.qr', [
            'user'   => $user,
            'qrCode' => $user->qr_token,
            'qrImg'  => $user->qr_image,
        ]);
    }
}
