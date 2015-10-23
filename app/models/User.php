<?php

namespace App\models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class User extends \Eloquent implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Creates a user in our system using data from an OAUTH provider API
     *
     * @param SocialiteUser $user
     *
     * @param array $params
     * @return static
     */
    public function createUserUsingDataFromAPI(SocialiteUser $user, $params = [])
    {
        $data = $this->getUserData($user, $params);

        $user = $this->create($data);

        return $user;
    }

    /**
     * @param SocialiteUser $user
     * @param $params
     * @return array
     */
    protected function getUserData(SocialiteUser $user, $params)
    {

        $user_data = $user->map($user->user);

        return [

            'avatar' => $user_data->avatar,
            'email' => $user->getEmail(),
            'gender' => $user_data->gender,
            'password' => app('hash')->make($params['password']),
        ];
    }
}
