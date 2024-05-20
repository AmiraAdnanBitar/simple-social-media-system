<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResourse;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // محاولة تسجيل الدخول للمستخدم باستخدام البيانات المدخلة والتحقق منها. 
        // إذا تم تسجيل الدخول بنجاح، يتم حفظ التوكين الذي يمثل الجلسة للمستخدم في متغير 
        // $token. 
        // إذا لم يتم تسجيل الدخول بنجاح، يتم إرجاع رد 
        // JSON يحتوي على رسالة الخطأ "Unauthorized".

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json([
            'access_token'=>$token,
            'user_logedin'=>new UserResourse (auth()->user())]);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string||min:6',//confirmed
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => new UserResourse($user)
        ], 201);
    }


    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }


    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}