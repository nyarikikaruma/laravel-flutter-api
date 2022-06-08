<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register a user
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        //
        $attributes = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => bcrypt($attributes['password']),

        ]);
        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ]);
    }

    /**
     * Login user
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        //
        $attributes = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        // Attempt login user 
        if (!Auth::attempt($attributes)) {
            return response([
                'message' => 'Invalid credentials',

            ], 403);
        }
        $user = Auth::user();
        return response([
            'user' => $user,
            'token' => $request->bearerToken(),

        ]);
        // dd($attributes);
    }

    /**
     *
     * Logout user
     */
    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response([
            'message' => 'logout success',
        ], 200);
    }

    /**
     * Show user details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function user()
    {
        return response([
            'user' => Auth::user()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function update(Request $request)
    {
        // dd($request->image);
        $attributes = $request->validate([
            'name' => 'required|string',
            'image' => 'image'
        ]);
        $newImageName = time() . '-' . $request->name . '.' .
            $request->image->extension();
        $request->image->move(public_path('images'), $newImageName);
        $attributes['image'] = $newImageName;
        $user = Auth::user();
        $user->name = $attributes['name'];
        $user->image = $attributes['image'];
        return response([
            'message' => 'User updated.',
            'user' => auth()->user()
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
