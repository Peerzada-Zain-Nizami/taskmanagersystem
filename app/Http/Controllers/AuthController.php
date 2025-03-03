<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use App\Notifications\ResetPassword;
use App\Notifications\NewUserRegistered;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ], [], [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            return response()->json([
                'errors' => $errors,
            ], 400);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'user',
                'approved' => false,
            ]);

            $admin = User::where('role', 'admin')->get();
            Notification::send($admin, new NewUserRegistered($user));

            return response()->json(['message' => 'User registered successfully. Waiting for admin approval.'], 201);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string|min:8',
        ], [], [
            'email' => 'Email',
            'password' => 'Password',
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            return response()->json([
                'errors' => $errors,
            ], 400);
        } else {

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $user = Auth::user();
            if ($user->role !== 'admin' && !$user->is_approved) {
                return response()->json(['message' => 'Admin approval required.'], 403);
            }

            $token = $user->createToken('authToken')->plainTextToken;
            $cookie = cookie('auth_token', $token, 60 * 24, '/', null, true, true, false, 'Strict');
            return response()->json(['user' => $user, 'token' => $token], 200)->cookie($cookie);;
        }
    }

    public function profile()
    {
        return response()->json(Auth::user());
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            return response()->json(['errors' => $errors], 422);
        }

        $status = Password::broker()->sendResetLink(
            $request->only('email'),
            function ($user, $token) {
                $user->notify(new ResetPassword($token));
            }
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.'])
            : response()->json(['message' => 'Unable to send reset link.'], 500);
    }

    /**
     * Handle the reset password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            return response()->json(['errors' => $errors], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset.'])
            : response()->json(['message' => 'Unable to reset password.'], 500);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $cookie = cookie('auth_token', '', 0, '/', null, true, true, false, 'Strict');
        return response()->json(['message' => 'Logged out successfully'], 200)->cookie($cookie);
    }
}
