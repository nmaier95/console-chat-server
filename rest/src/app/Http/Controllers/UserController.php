<?php


namespace App\Http\Controllers;

use App\Models\ChatUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;

class UserController extends Controller
{
    private $credentialRules = [
        'username' => 'required|string',
        'password' => 'required|string',
    ];

    public function create(Request $request): JsonResponse
    {
        try
        {
            $this->validate($request, $this->credentialRules);
        }
        catch (ValidationException $e)
        {
            return response()->json($e->errors());
        }

        try
        {
            ChatUser::create([
                'username' => $request->post('username'),
                'password' => Crypt::encryptString($request->post('password'))
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => 'USERNAME_NOT_AVAILABLE']);
        }

        try
        {
            /** @var ChatUser $user */
            $user = ChatUser::firstWhere('username', '=', $request->post('username'));

            $jwtPayload = [
                'iss' => 'chat-user-jwt', // Issuer of the token
                'sub' => $user->id, // Subject of the token
                'iat' => time(), // Time when JWT was issued.
                'exp' => time() + 60 * 60 // Expiration time
            ];

            return response()->json(['success' => true, 'token' => JWT::encode($jwtPayload, env('JWT_PRIVATE_KEY'))]);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json('something went wrong trying to find new user in DB');
        }
    }

    public function login(Request $request)
    {
        try
        {
            $this->validate($request, $this->credentialRules);
        }
        catch (ValidationException $e)
        {
            return response()->json($e->errors());
        }

        try
        {
            /** @var ChatUser $user */
            $user = ChatUser::firstWhere('username', '=', $request->post('username'));

            $decryptedPassword = Crypt::decryptString($user->password);

            if($decryptedPassword === $request->post('password')) {
                $jwtPayload = [
                    'iss' => 'chat-user-jwt', // Issuer of the token
                    'sub' => $user->id, // Subject of the token
                    'iat' => time(), // Time when JWT was issued.
                    'exp' => time() + 60 * 60 // Expiration time
                ];

                return response()->json(['success' => true, 'token' => JWT::encode($jwtPayload, env('JWT_PRIVATE_KEY'))]);
            }
            else
            {
                throw new \Exception();
            }
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => 'WRONG_USERNAME_PASSWORD']);
        }
    }
}
