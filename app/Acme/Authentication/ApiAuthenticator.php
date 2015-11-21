<?php

namespace app\Acme\Authentication;

use App\models\User;
use Illuminate\Http\Exception\HttpResponseException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class ApiAuthenticator
{

    /**
     * @var JWTAuth
     */
    private $jwt;
    /**
     * @var User
     */
    private $user;

    public function __construct(JWTAuth $JWTAuth, User $user){

        $this->jwt = $JWTAuth;
        $this->user = $user;
    }

    /**
     * @param array $credentials
     *
     * @return bool|false|null|string
     */
    public function login(array $credentials){
        try {
            // verify the credentials and create a token for the user
            if (!$token = $this->jwt->attempt($credentials)) {
                return false;
            }
        } catch (JWTException $e) {
            // something went wrong
            return null;
        }

        // if no errors are encountered we can return a JWT
        return $token;
    }

    public function create(array $data){

        $result = $this->user->updateOrCreate($data);

        if(is_null($result)){

            return null;
        }

        return $result;
    }
}