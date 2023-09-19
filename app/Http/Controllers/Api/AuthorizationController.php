<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\AuthorizationRequest;

class AuthorizationController extends Controller
{
    public function store(AuthorizationRequest $request)
    {
        $credentials['name'] = $request->name;
        $credentials['password'] = $request->password;

        $token = \Auth::guard('api')->attempt($credentials);
        // if(!$token = \Auth::guard('api')->attempt($credentials)){
        //     throw new AuthenticationException('用户名或密码错误');
        // }
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL()*60
        ])->setStatusCode(201);
    }    
}
