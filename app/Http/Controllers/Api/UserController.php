<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users =  User::latest()->paginate(5);

        return new UserResource(true , 'List Data User' , $users);
    }

    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email:dns',
            'password' => 'required'
        ]);

        if($validateData->fails()) 
        {
            return response()->json($validateData->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);

        return new UserResource(true , 'Registrasi Berhasil' , $user);
        

        
    }

    public function login(Request $request)
    {
        // //$credentials = $request->only('email' , 'password');

        // if(Auth::attempt($credentials)) 
        // {
        //     $user = User::where('email', $request->email)->first();
        //     //$token = $user->createToken('Levis Products')->accessToken;
        //     return new UserResource(true,'login berhasil',$user);
        // }else
        // {
        //     return response()->json(['error' => 'Unauthorized'], 402);
        // }
  

        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            return response()->json(['message' => 'Login successful', 'user' => $user], 200);
        }
    
        return response()->json(['message' => 'Invalid credentials'], 401);
}

        
    }


