<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
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
            'password' => 'required|string',
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
            return new PostResource(false, 'Invalid email', [
                'message' => 'The provided email does not exist.'
            ]);
        }

        // Cek password
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Jika password salah
            return new PostResource(false, 'Invalid password', [
                'message' => 'The provided password is incorrect.'
            ]);
        }

        // Jika login berhasil
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;
        return new PostResource(true, 'User login successfully.', $success);
    }

    public function logout(){
        auth()->user()->tokens()->delete();
    
        return new PostResource(true, 'logged out', null);
    }
}
