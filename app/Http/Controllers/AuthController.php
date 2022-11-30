<?php

namespace App\Http\Controllers;

use App\Models\MailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function signup (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'email', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_confirmation' => Hash::make($request->password_confirmation),
        ]);

        $token = auth()->login($user);
        
        $mailVerified = MailVerification::create([
            'user_id' => $user->id,
            'token' => $token
        ]);

        Mail::send('email.verify', ['user' => $user, 'mailVerified' => $mailVerified, 'token' => $token], function($mail) use ($user) {
            $mail->to($user->email, $user->name);
            $mail->subject('Verify your email address');
        });

        $mailVerified->status = 'registered';
        $mailVerified->save();

        $user->status = 'registered';
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'we sent you an activation code. check your email and click on the link to verify.',
            'token' => $token,
        ], 200);
    }

    public function login (Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout ()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh ()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function me ()
    {
        return response()->json(auth()->user());
    }

    public function ChangeEmail(Request $request)
    {
        $oldEmail = auth()->user()->email;

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'max:255', 'email', 'unique:users'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($oldEmail != $request->email) {
            auth()->user()->status = 'registered';
        }
        
        $user = auth()->user();

        $token = auth()->login($user);
        
        $mailVerified = MailVerification::create([
            'user_id' => $user->id,
            'token' => $token
        ]);

        auth()->user()->email = $request->email;

        $mailVerified->status = 'registered';
        $mailVerified->save();

        $user->status = 'registered';
        $user->save();

        Mail::send('email.verify', ['user' => $user, 'mailVerified' => $mailVerified, 'token' => $token], function($mail) use ($user) {
            $mail->to($user->email, $user->name);
            $mail->subject('Verify your email address');
        });

        response()->json([
            'status' => 'success',
            'message' => 'we sent you an activation code. check your email and click on the link to verify.',
            'token' => $token,
        ], 200);
    }

    protected function respondWithToken ($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
