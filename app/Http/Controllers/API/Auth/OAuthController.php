<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class OAuthController extends Controller
{
    public function googleCallback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return response()->json(['error' => 'Code not provided'], 400);
        }

        $googleToken = $this->getGoogleToken($code);
        $googleUserInfo = $this->getGoogleUserInfo($googleToken);
        $email = $googleUserInfo['email'];

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not registered'], 404);
        }

        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;
        $frontendUrl = config('services.google.frontend_callback_url');

        return Redirect::away("{$frontendUrl}?token={$token}");
    }

    private function getGoogleToken($code)
    {
        $response = Http::asForm()->post('https://www.googleapis.com/oauth2/v4/token', [
            'code'          => $code,
            'client_id'     => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri'  => config('services.google.redirect_uri'),
            'grant_type'    => 'authorization_code',
        ]);

        if ($response->failed()) {
            dd(config('services.google'));
            dd($response->json());
            throw new \Exception('Failed to retrieve Google access token');
        }


        return $response->json('access_token');
    }

    private function getGoogleUserInfo($accessToken)
    {
        $response = Http::withToken($accessToken)
            ->get('https://www.googleapis.com/oauth2/v3/userinfo');

        if ($response->failed()) {
            throw new \Exception('Failed to retrieve user info from Google');
        }

        return $response->json();
    }
}