<?php namespace app\Antony\DomainLogic\Modules\Authentication\Traits;

use App\Http\Requests\ApiRegistration;
use Illuminate\Http\Request;
Use App\models\User as UserRepo;
use Laravel\Socialite\Contracts\User;

trait oauth2Authenticator
{
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

            return $this->handleProviderCallback($provider);

        } else {
            // we redirect to the provider login page
            return $this->getAuthorizationFirst($provider);
        }
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
        $user = $this->getApiUser($api, null);

        return $user;
    }

    /**
     * Returns the user data from an api call
     *
     * @param $api
     *
     * @return \Laravel\Socialite\Contracts\User
     */
    protected function getApiUser($api, array $scopes)
    {
        if(empty($scopes)){
            return $this->socialite->driver($api)->user();
        }
        return $this->socialite->driver($api)->scopes($scopes)->user();
    }

    /**
     * Redirects the client to the OAUTH provider sign in page
     *
     * @param $provider
     * @return mixed
     */
    private function getAuthorizationFirst($provider)
    {
        return $this->socialite->driver($provider)->redirect();
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
        $user = UserRepo::where('email', $email);

        if (is_null($user)) {

            if ($createNew) {

                // store the user api data in the session, then allow them to fill other fields prior to their account creation
                app('session')->put('api_user_data', $api_user);

                return redirect()->route('auth.fill');
            }

            return redirect()->to(session('url.intended', '/'));

        } else {

            // login the user
            $this->login($user, true);

            return redirect()->intended(session('url.intended', '/'));
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

        $this->login($user, true);

        app('session')->pull('api_user_data');

        return redirect()->intended(session('url.intended', '/'));
    }

    /**
     * Redirects a user to the OAUTH provider login page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function apiAuth(Request $request)
    {
        $provider = $request->get('provider');

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

        return view('auth.fillRemaining', compact('user'));
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
            : redirect()->route('login');
    }
}