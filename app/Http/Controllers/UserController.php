<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credential = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credential)) {
            return response()->json(null, Response::HTTP_NOT_FOUND);
        }

        return response()->json(null, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed'],
            'name' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        $user = new User();

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);

        $user->save();

        return response()->json(null, Response::HTTP_CREATED);
    }
}
