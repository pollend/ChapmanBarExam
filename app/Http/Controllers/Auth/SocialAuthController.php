<?php


namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectAzureToProvider()
    {
        return Socialite::driver('azure')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleAzureProviderCallback()
    {
        return $this->callback('azure');
    }

    private function callback($driver){

        $userData = Socialite::driver($driver)->user();

        /** @var User $user */
        $user = User::query()->where('azure_id',$userData->id)
            ->where('email',$userData->email)
            ->firstOrCreate([
                'email' => $userData->email,
                'azure_id' => $userData->id]);
        Auth::login($user);
    }
}