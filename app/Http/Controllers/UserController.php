<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class UserController extends Controller
{
    public function authenticate(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'=>'required',
            'password'=>'required'
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);


        $credentials = $request->only('email', 'password');
        try {
            if(! $token = JWTAuth::attempt($credentials))
                return response()->json(['error'=>'invalid Email or Password'], 400);
        } catch (JWTException $e) {
            return response()->json(['error'=>'Could not create token'], 500);
        }

        return response()->json(compact('token'));

    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>'required|unique:users|email',
            'password'=>'required|min:4'
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        $user = User::create([
           'name'=>$request->name,
           'email'=>$request->email,
           'password'=>Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user','token'),201);
    }

    public function getAuthenticatedUser() {
        try{
            if(!$user = JWTAuth::parseToken()->authenticate())
                return response()->json(['error'=>'User not found'], 404);
        } catch(JWTException $e ) {
            return response()->json($e);
        }

        return response()->json(compact('user'));
    }

}
