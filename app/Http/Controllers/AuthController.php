<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator, DB, Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\Welcome;
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


        $user = User::create($request->all());

        $verification_code = str_random(30); //Generate verification code

        DB::table('user_verifications')->insert(['user_id' => $user->id, 'token' => $verification_code]);

        $subject = 'Please verify your email address.';



        Mail::to($user)->send(new Welcome($verification_code));



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

            $user->is_verified = 1;
            $user->save();

            DB::table('user_verifications')->where('token', $verification_code)->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'You have successfully verified email address'
            ]);

        }

        return response()->json(['success' => false, 'error' => 'Verification code is invalid.']);

    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $rules = [
            'email'     => 'required|email',
            'password'  => 'required'
        ];

        $validator = Validator::make($credentials, $rules);

        if($validator->fails()){
            return response()->json(['success' => false, 'error' => $validator->messages()], 401);
        }

        $credentials['is_verified'] = 1;

        try {
            // attempt to verify the credentials and create a token fo the user
            if(! $token = JWTAuth::attempt($credentials)){
                return response()->json(['success' => false, 'error' => 'We cant find an account with this credentials. Please make sure you entered the right information and you have verified your email address.'], 404);

            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to login, please try again'], 500);
        }

        //all good so return the token
        return response()->json(['success' => true, 'data' => ['token' => $token]], 200);
    }

    public function logout(Request $request)
    {
        $this->validate($request, ['token' => 'required']);

        try {
            JWTAuth::invalidate($request->input('token'));
            return response()->json(['success' => true, 'message' => 'You have successfully logged out ']);
        } catch (JWTException $e) {
            //something went wrong list attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
    }


}
