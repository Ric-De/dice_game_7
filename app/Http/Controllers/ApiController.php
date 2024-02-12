<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    //Register a new user in the API
    public function register(Request $request){

        //Data validation
        $validate = Validator::make($request->all(), [
            "name" => "nullable|string|unique:users,name,",
            "email" => "required|email|unique:users", 
            "password" => "required|confirmed"
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);
        }

        $input = $request->all();
        $input['name'] = $input['name'] ?? 'anonymous';
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input)->assignRole('player');

        $data['token'] = $user->createToken($request->email)->accessToken;
        $data['user'] = $user;

        //Response
        return response()->json([
            "status" => 'success',
            "message" => "User created successfully",
            'data' => $data,
        ], 201);
    }

    //Login API
    public function login(Request $request)
    {
        //Data validation
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);  
        }

        //Check email exist
        $user = User::where('email', $request->email)->first();

        //Check password
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid credentials'
                ], 401);
        }

        $data['token'] = $user->createToken($request->email)->accessToken;
        $data['user'] = $user;
        
        $response = [
            'status' => 'success',
            'message' => 'User is logged in successfully.',
            'data' => $data,
        ];

        return response()->json($response, 200);
    }

    //Logout API
    public function logout(Request $request){

        $request->user()->token()->revoke(); 

        return response()->json([
            "status" => true,
            "message" => "User logged out"
        ], 200);
    }
}
