<?php

namespace App\Acme\Api\v1;

use app\Acme\Authentication\ApiAuthenticator;
use app\Acme\Authentication\oauth2Authenticator;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLogin;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //use ThrottlesLogins;

    use oauth2Authenticator;

    /**
     * @var ApiAuthenticator
     */
    protected $auth;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param ApiAuthenticator $authenticateUser
     * @param Request $request
     */
    public function __construct(ApiAuthenticator $authenticateUser, Request $request)
    {
        // $this->middleware('jwt.auth', ['except' => 'LoginViaAPI', 'getLogin']);

        $this->auth = $authenticateUser;
        $this->request = $request;

    }

    /**
     * @param UserLogin $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLogin $request)
    {
        $credentials = ['email' => $request->get('email'), 'password' => $request->get('password')];

        $token = $this->auth->login($credentials);
        if($token == false){

            $this->response()->errorForbidden("Invalid username/password");
        }
        else if($token == null){

            $this->response()->errorInternal();

        } else {
            $user = auth()->user();
            return response()->json(compact('token', 'user'));
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $result = $this->auth->create($request->all());

        if (is_null($result)) {
            $this->response()->errorInternal();
        }

        return response()->json($result);
    }
}
