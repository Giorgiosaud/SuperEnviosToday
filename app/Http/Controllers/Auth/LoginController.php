<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout','token');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'idn' => ['required', 'string', 'max:20'],
            'idn_type' => ['required', 'in:CI,PASSPORT,RUT,DNI,RIF'],
            'password' => 'required|string',
        ]);
    }

    protected function credentials(Request $request)
    {
        return $request->only('idn', 'idn_type','password');
    }
    /**
     * Get the failed login response instance.
     *
     * @param Request $request
     * @return Response
     *
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse()
    {
        throw ValidationException::withMessages([
            'warning' => [trans('auth.failed')],
        ]);
    }
    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, User $user)
    {
        $user->createToken('Personal Access Token');
    }



}
