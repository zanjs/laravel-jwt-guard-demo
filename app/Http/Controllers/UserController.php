<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WeUser;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;

class UserController extends Controller
{
    protected $guard = 'jwt';

    public function regist(Request $request)
    {   

        $hasUser = WeUser::Where('email', $request->email)->first();

        if(!$hasUser){

            WeUser::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            // return 'no hasUser';
        }

        $credentials=[
            'email' => $request->email,
            'password'  => $request->password,
        ];
        try {
            if (! $token = Auth::guard($this->guard)->attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }


        return response()->json(compact('token'));


        return $hasUser;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * 获取token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function token(Request $request)
    {
        $credentials=[
            'email' => $request->email,
            'password'  => $request->password,
        ];
        try {
            if (! $token = Auth::guard($this->guard)->attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json(compact('token'));
    }
    /**
     * @return mixed
     */
    public function refershToken()
    {
        $token = Auth::guard($this->guard)->refresh();
        return $this->response->array(compact('token'));
    }
    /**
     * 个人信息
     *
     * @return User|null
     */
    public function me()
    {   
        try {
            if (! Auth::guard('jwt')->user()) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return Auth::guard('jwt')->user();
    }
    /**
     * 退出
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::guard($this->guard)->logout();
        return response()->json(['status' => 'ok']);
    }
}
