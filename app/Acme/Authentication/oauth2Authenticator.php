<?php namespace app\Acme\Authentication;

use App\Http\Requests\ApiRegistration;
use App\models\User as UserRepo;
use Illuminate\Http\Request;
use JWTAuth;
use Laravel\Socialite\Contracts\User;

/**
 * Class oauth2Authenticator
 * Wrapper around socialite
 * @package app\Acme\Authentication
 */
trait oauth2Authenticator
{
    /**
     * Oauth scopes
     * @var array
     */
    protected $scopes = [];

    /**
     * Redirects a user to an OAUTH provider
     *
     * @param $has_code
     * @param $provider
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider($has_code, $provider)
    {
        // if we have a code in the request, then we attempt to gather data about our user
        if ($has_code) {

            return $this->setScopes(['user', 'repo'])->handleProviderCallback($provider);

        } else {
            // we redirect to the provider login page
            return $this->getAuthorizationFirst($provider);
        }
    }

    /**
     * Set oauth scopes
     * @param array $scopes
     * @return $this
     */
    public function setScopes(array $scopes)
    {
        $this->scopes = $scopes;
        return $this;
    }

    /**
     * Handles a callback from an OAUTH API provider. This will be placed in the OAUTH redirect url handler
     *
     * @param $api
     *
     * @return \Laravel\Socialite\Contracts\User
     */
    public function handleProviderCallback($api)
    {
        $user = $this->getApiUser($api, empty($this->scopes) ? [] : $this->scopes);

        return $user;
    }

    /**
     * Returns the user data from an api call
     *
     * @param $api
     *
     * @param array $scopes
     * @return User
     */
    protected function getApiUser($api, array $scopes)
    {
        if (empty($scopes)) {
            return $this->auth->socialite->driver($api)->user();
        }
        return $this->auth->socialite->driver($api)->scopes($scopes)->user();
    }

    /**
     * Redirects the client to the OAUTH provider sign in page
     *
     * @param $provider
     * @return mixed
     */
    private function getAuthorizationFirst($provider)
    {
        return $this->auth->socialite->driver($provider)->redirect();
    }

    /**
     * Checks the API data returned with what we have in the db. Then logs them in
     * There's an option to create an account for them
     *
     * @param User $api_user
     * @param bool $createNew
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkUsersAccount(User $api_user, $createNew = false)
    {
        // grab the user's email
        $email = $api_user->getEmail();

        // check if the account exists on our server
        $user = UserRepo::where('email', $email)->first();

        if (empty($user)) {

            // means user doesn't exist on our server
            if ($createNew) {

                // store the user api data in the session, then allow them to fill other fields prior to their account creation
                app('session')->put('api_user_data', $api_user);

                return redirect()->route('auth.fill');
            }

            return redirect()->to(session('url.intended', '/'));

        } else {

            return $this->generateWebToken($user);
        }
    }

    /**
     * Creates a user's account using OAUTH provider API data
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createAccount(Request $request)
    {
        $user = (new UserRepo())->createUserUsingDataFromAPI($request->getSession()->get('api_user_data'), $request->all());

        return $this->generateWebToken($user);

        //return redirect()->intended(session('url.intended', '/'));
    }

    /**
     * Redirects a user to the OAUTH provider login page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function apiAuth(Request $request)
    {
        $provider = $request->get('api');

        // store used api in the current session
        $request->getSession()->set('oauth_api', $provider);

        return $this->redirectToProvider($request->has('code'), $provider);
    }

    /**
     * Handle a callback (redirect) from an OAUTH provider
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleOAUTHCallback(Request $request)
    {
        $user = $this->handleProviderCallback($request->getSession()->get('oauth_api'));

        return $this->checkUsersAccount($user, true);
    }

    /**
     * Display the mini form that users will fill in prior to registration via an API
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function getMiniRegistrationForm(Request $request)
    {
        $user = $request->getSession()->get('api_user_data');

        return view('auth.fill_remaining', compact('user'));
    }


    /**
     * Creates an account using API data
     *
     * @param ApiRegistration $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createAccountViaOAUTHData(ApiRegistration $request)
    {
        return $request->getSession()->has('api_user_data')
            ? $this->createAccount($request)
            : redirect()->route('auth.login');
    }

    /**
     * Makes a token for the user, to be sent to the angular end
     *
     * @param UserRepo $user
     * @return \Illuminate\Http\JsonResponse
     */
    private function generateWebToken(UserRepo $user)
    {
        // make a JWT
        $jwt = JWTAuth::fromUser($user);

        //auth()->login($user, true);

        app('session')->pull('api_user_data');

        return response()->json(compact('user', 'jwt'));
    }
}
