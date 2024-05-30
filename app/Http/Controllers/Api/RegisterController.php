<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PostResource;

class RegisterController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

     public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);    
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
        return new PostResource(true, 'User register successfully.', $success);
    }
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return new PostResource(false, 'Validation Error', [
                'errors' => $validator->errors()
            ]);
        }

        // Cek apakah email ada di database
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Jika email tidak ditemukan
            return response([
                'success'   => false,
                'message' => ['The provided email does not exist.']
            ], 404);
        }

        // Cek password
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Jika password salah
            return response([
                'success'   => false,
                'message' => ['The provided password is incorrect.']
            ], 404);
        }

        // Jika login berhasil
        $token = $user->createToken('ApiToken')->plainTextToken;
        $response = [
            'success'   => true,
            'user'      => $user,
            'token'     => $token
        ];
        return response($response, 201);
    }

    public function logout(){
        auth()->logout();
        return response()->json([
            'success'    => true
        ], 200);
    }
}
