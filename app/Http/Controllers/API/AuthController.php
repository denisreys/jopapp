<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request){
        $request = $request->all();

        $validator = Validator($request, [
            'login' => 'required|regex:/^[a-zA-Z0-9_]+$/u|min:2|max:20|unique:users,login',
            'password' => 'required|min:2|max:100'
        ],['login.regex' => 'Login must contain only latin letters, numbers and "_".'],);
        
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 200);
        }

        $user = User::create(['login' => $request['login'], 'password' => bcrypt($request['password'])]);
        
        if(Auth::attempt(['login' => $request['login'], 'password'=> $request['password'] ], true)){
            $response = [
                'success' => true,
                'token' => $user->createToken('jopapp')->plainTextToken
            ];
            return response()->json($response,200);
        }
        else {
            $response = [
                'success' => true,
                'token' => false
            ];
            return response()->json($response,200);
        }
    }
    public function login(Request $request){
        $validator = Validator($request->all(), [
            'login' => 'required|min:2|max:20',
            'password' => 'required' 
        ]);
        if(Auth::attempt(['login' => $request->login,'password'=>$request->password], true)){
            $user = Auth::user();
            
            $success['token'] = $user->createToken('jopapp')->plainTextToken;
            $success['login'] = $user->login;

            $response = [
                'success' => true,
                'data' => $success
            ];
            return response()->json($response,200);
        }
        else {
            $response = [
                'success' => false,
                'message' => 'Login or password are wrong'
            ];
            return response()->json($response,200);
        }
    }
}
