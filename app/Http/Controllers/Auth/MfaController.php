<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\AccessLog;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

// class  MfaController extends Controller
// {
//     protected  $loginController;
//     // MfaController.php
//     public function verifyMfa(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'code' => 'required|string|size:6',
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['errors' => $validator->errors()], 422);
//         }

//         $userId = session('pending_user_id');
//         if (!$userId) {
//             return response()->json(['message' => 'Session expirée.'], 401);
//         }

//         $user = User::find($userId);

//         // Vérifier le code MFA avec Google Authenticator ou autre bibliothèque TOTP
//         $ga = new GoogleAuthenticator();
//         if (!$ga->verifyCode($user->mfa_secret, $request->code)) {
//             AccessLog::create([
//                 'id' => (string) Str::uuid(),
//                 'user_id' => $user->id,
//                 'action' => 'failed_login',
//                 'details' => 'Invalid MFA code',
//                 'ip_address' => $request->ip(),
//                 'device_info' => $request->userAgent(),
//                 'status' => 'failed'
//             ]);

//             return response()->json(['message' => 'Code d\'authentification invalide.'], 401);
//         }

//         // Effacer la session temporaire
//         session()->forget('pending_user_id');

//         return $this->loginController->completeLogin($user, $request);
//     }
// }
