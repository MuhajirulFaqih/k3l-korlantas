<?php
namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Facades\DB;

class AuthController extends AccessTokenController
{
    public function issueToken(ServerRequestInterface $request) {
        $response = parent::issueToken($request);
        $body = json_decode($response->content());
        if (isset($body->error))
            return $response;

        $oauth = (Controller::userAccessTokenId($body->access_token));

        if ($oauth){
            $affected = DB::table('oauth_access_tokens')->where('user_id', $oauth->user_id)->where('id', '!=', $oauth->id)->where('client_id', $oauth->client_id)->update([
                'revoked' => 1
            ]);
        }

        return $response;
    }

    public function issueTokenAdmin (ServerRequestInterface $request) {
        $response = parent::issueToken($request);

        $body = json_decode($response->content());

        if (isset($body->error))
            return response()->json(['message' => 'Username dan sandi salah'], 403);

        $oauth = (Controller::userAccessTokenId($body->access_token));
        $user = User::find($oauth->user_id);

        if(in_array($user->jenis_pemilik, ['admin', 'kesatuan']))
            return $response;

        return response()->json(['message' => 'Username dan password salah'], 403);

    }
}
