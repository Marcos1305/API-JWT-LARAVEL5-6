<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator, DB, Hash, Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->only('name', 'email', 'password');

        $rules = [
            'name'  =>  'required|max:255',
            'email' =>  'required|email|max:255|unique:users'
        ];

        $validator = Validator::make($credentials, $rules);
        if($validator->fails()){
            return response()->json(['success' => false, 'error' => $validator->messages()], 400);
        }
        $name = $request->name;
        $email = $request->email;
        $user = User::create($request->all());

        $verification_code = str_random(30); //Generate verification code

        DB::table('user_verifications')->insert(['user_id' => $user->id, 'token' => $verification_code]);

        $subject = 'Please verify your email address.';

        Mail::send('email.verify', ['name' => $name, 'verification_code' => $verification_code], function($mail) use ($email, $name, $subject){
            $mail->from(getenv('FROM_EMAIL_ADDRESS'), "API LARAVEL");
            $mail->to($email, $name);
            $mail->subject($subject);
        });

        return response()->json(['success' => true, 'message' => 'Thanks for signing up! Please check your email to complete your registration']);

    }
    public function verifyUser($verification_code)
    {
        $check = DB::table('user_verifications')->where('token', $verification_code)->first();

        if (!is_null($check)){
            $user = User::find($check->user_id);

            if($user->is_verified == 1){
                return response()->json([
                    'success'   => true,
                    'message'   => 'Account already verified..'
                ]);
            }

            $user->update(['is_verified' => 1]);

            DB::table('user_verifications')->where('token', $verification_code)->delete();
            return reponse()->json([
                'success'   => true,
                'message'   => 'You have successfully verified email address'
            ]);

        }

        return response()->json(['success' => false, 'error' => 'Verification code is invalid.']);

    }

}
