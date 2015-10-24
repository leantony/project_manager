<?php

namespace App\Http\Controllers\Auth;

use app\Acme\Authentication\AppAuthenticator;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLogin;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //use ThrottlesLogins;

    /**
     * @var AppAuthenticator
     */
    protected $auth;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param AppAuthenticator $authenticateUser
     * @param Request $request
     */
    public function __construct(AppAuthenticator $authenticateUser, Request $request)
    {
        $this->middleware('jwt.auth', ['except' => 'LoginViaAPI', 'getLogin']);

        $this->auth = $authenticateUser;
        $this->request = $request;

    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        return view('auth.login');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return view('auth.register');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        $this->auth->logout();

        return redirect()->home();
    }

    /**
     * @param UserLogin $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function postLogin(UserLogin $request)
    {
        $data = $this->auth->login($request->except('_token'));

        return is_null($data) ? redirect()->back() : redirect()->intended();
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\Response
     */
    public function postRegister(Request $request)
    {
        return $this->auth->apiAuth($request);
    }

    /**
     * @param UserLogin $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function LoginViaApi(UserLogin $request){

        return $this->auth->generateWebToken($request->except('_token'));
    }
}
