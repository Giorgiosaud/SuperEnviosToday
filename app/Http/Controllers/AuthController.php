<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @param  Request
     *
     * @return [type]
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'idn'     => ['required', 'string', 'max:255'],
            'idn_type'     => ['required', 'in:CI,PASSPORT,RUT,DNI'],
            'phone'     => ['string'],
            'address'     => ['string'],
            'email'    => [ 'string', 'email', 'max:255', 'unique:users','confirmed'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        $user = new User([
            'name'     => $request->name,
            'last_name'     => $request->last_name,
            'idn'     => $request->idn,
            'idn_type'     => $request->idn_type,
            'phone'     => $request->phone,
            'address'     => $request->address,
            'email'    => $request->email,
            'password' => $request->password,

            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $user->save();

        return response()->json([
            'message' => 'Successfully created user!',], 201);
    }

    public function getToken()
    {
        if (Auth::guest()) {
            return response()->json([
                'code'      =>  401,
                'message'   =>  'Unauthorized'
            ], 401);
        }

        $tokenResult = Auth::user()->createToken('Personal Access Token');
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => \Carbon\Carbon::parse(
                $tokenResult->token->expires_at)
                ->toDateTimeString(),
        ]);

    }

    /**
     * @param  Request
     *
     * @return [type]
     */
    public function login(Request $request)
    {
        $request->validate([
            'idn_type' => 'required|in:PASSPORT,DNI,RUT,CI',
            'idn' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at)
                ->toDateTimeString(),
        ]);
    }

    /**
     * @param  Request
     *
     * @return [type]
     */
    public function logout(Request $request)
    {
        Auth::user()->tokens->each(function ($token) {
            return $token->revoke();
        });

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @param  Request
     *
     * @return [type]
     */
    public function user(Request $request)
    {
        return response()->json($request->user()->roles);
    }
    public function isValid()
    {
        return response()->json(['ok'=>'ok']);
    }

    //
}
