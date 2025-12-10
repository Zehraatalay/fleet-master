<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
     public function login(Request $request)
    {
        // frontend'den gelen email ve password filedları kontrol edilir
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // email + password dogru mu
        // users tablosunda mail eslesirse password hash'i dogruysa true doner 
        // user oturumda Auth'a set edilmis olur 
        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Geçersiz e-posta veya şifre.',
            ], 401);
        }

        // giris basarili → useri al
        $user = Auth::user();

        // user icin yeni bir token create eder 
        // fleet-master : token adi 
        // plainTextToken : frontend'de kullanilacak Bearer... kismi 
        $token = $user->createToken('fleet-master')->plainTextToken;

        // user info + tokeni dondurur 
        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // mevcut tokeni sil (Bearer token uzerinden)
        // bunun icin bu endpoint'in auth:sanctum ile korunmasi gerekiyor 
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Çıkış yapıldı.',
        ]);
    }
}
