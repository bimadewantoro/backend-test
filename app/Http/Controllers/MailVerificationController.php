<?php

namespace App\Http\Controllers;

use App\Models\MailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MailVerificationController extends Controller
{
    public function MailVerification($token)
    {
        $MailVerification = MailVerification::where('token', $token)->first();

        if(isset($MailVerification) ){
            $user = $MailVerification->user;
            if(!$user->verified) {
                $MailVerification->status = 'verified';
                $MailVerification->save();
                $MailVerification->user->status = 'verified';
                $MailVerification->user->save();
                response()->json(['message' => 'User verified successfully'], 200);
            }else{
                response()->json(['message' => 'User already verified'], 200);
            }
    } else {
        return response()->json(['message' => 'Verification token is invalid.'], 404);
        }
    }
}