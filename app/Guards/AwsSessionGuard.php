<?php

namespace App\Guards;

use \Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\SessionGuard;

class AwsSessionGuard extends SessionGuard
{	
	/**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return;
        }

        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (! is_null($this->user)) {
            return $this->user;
        }

        //Now we try using the data stored in session and fetch the user
        $user = $this->session->get($this->getName());

        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        $recaller = $this->getRecaller();

        if (is_null($user) && ! is_null($recaller)) {
            $user = $this->getUserByRecaller($recaller);

            if ($user) {
                $this->updateSession($user->getAuthIdentifier());

                $this->fireLoginEvent($user, true);
            }
        }

        return $this->user = $user;
    }

	/**
     * Log a user into the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  bool  $remember
     * @return void
     */
    public function login(Authenticatable $user, $remember = false)
    {   
        $this->updateSession($user);

        // If the user should be permanently "remembered" by the application we will
        // queue a permanent cookie that contains the encrypted copy of the user
        // identifier. We will then decrypt this later to retrieve the users.
        if ($remember) {
            $this->createRememberTokenIfDoesntExist($user);

            $this->queueRecallerCookie($user);
        }

        // If we have an event dispatcher instance set we will fire an event so that
        // any listeners will hook into the authentication events and run actions
        // based on the login and logout events fired from the guard instances.
        $this->fireLoginEvent($user, $remember);
        
        $this->setUser($user);
    }
}