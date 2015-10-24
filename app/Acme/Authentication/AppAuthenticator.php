<?php namespace app\Acme\Authentication;

use App\models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\Store;
use JWTAuth;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Tymon\JWTAuth\Exceptions\JWTException;

class AppAuthenticator
{
    use oauth2Authenticator;

    /**
     * Socialite implementation
     *
     * @var Socialite
     */
    protected $socialite;

    /**
     * Authenticator implementation
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The socialite driver, ie an API name, like facebook, google, etc
     *
     * @var string
     */
    protected $driver = 'github';

    /**
     * Our user repo
     *
     * @var User
     */
    protected $user;

    /**
     * @var Store
     */
    private $session;

    /**
     * @param Socialite $socialite
     * @param Guard $guard
     * @param User $userRepository
     * @param Store $session
     */
    public function __construct(Socialite $socialite, Guard $guard, User $userRepository, Store $session)
    {
        $this->socialite = $socialite;
        $this->auth = $guard;
        $this->user = $userRepository;
        $this->session = $session;
    }

    /**
     * Creates a user's account
     *
     * @param array $data
     * @return array|null
     */
    public function register(array $data)
    {
        $this->user = $this->user->create($data);

        if (!is_null($this->user)) {

            $this->auth->login($this->user, true);

            return ['user' => $this->user];
        }
        return null;
    }

    /**
     * Logs in a user. True is returned if login succeeds
     *
     * @param array $credentials
     * @return bool|null
     */
    public function login(array $credentials)
    {
        if ($this->auth->attempt($credentials, array_has($credentials, 'remember'))) {

            $user = $this->auth->user();

            return true;
        }
        return false;
    }

    /**
     * @param array $credentials
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateWebToken(array $credentials)
    {

        try {
            // verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials provided'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'OOps something happened'], 500);
        }

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }

    /**
     * Log out a user.
     *
     */
    public function logout()
    {
        $this->auth->logout();
    }

}