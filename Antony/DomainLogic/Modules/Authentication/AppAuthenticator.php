<?php namespace app\Antony\DomainLogic\Modules\Authentication;

use app\Antony\DomainLogic\Modules\Authentication\Traits\oauth2Authenticator;
use App\models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\Store;
use Laravel\Socialite\Contracts\Factory as Socialite;

abstract class AppAuthenticator
{
    use oauth2Authenticator;

    /**
     * Status of an authentication request. For non API calls only
     *
     * @var string
     */
    protected $authStatus;

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
     * Log out a user.
     *
     */
    public function logout()
    {
        $this->auth->logout();
    }

}