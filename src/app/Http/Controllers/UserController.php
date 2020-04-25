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
    /**
     * @var array
     */
    private $credentialRules = [
        'username' => 'required|string',
        'password' => 'required|string',
    ];

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try
        {
            $this->validate($request, $this->credentialRules);

            /** @var ChatUser $user */
            $user = ChatUser::create([
                'username' => $request->post('username'),
                'password' => Crypt::encryptString($request->post('password'))
            ]);

            $jwtPayload = [
                'iss' => 'chat-user-jwt', // Issuer of the token
                'sub' => $user->id, // Subject of the token
                'iat' => time(), // Time when JWT was issued.
                'exp' => time() + 60 * 60 // Expiration time
            ];

            return response()->json(['success' => true, 'token' => JWT::encode($jwtPayload, env('JWT_PRIVATE_KEY'))]);
        }
        catch (ValidationException $e)
        {
            return response()->json($e->errors());
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => 'USERNAME_NOT_AVAILABLE']);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try
        {
            $this->validate($request, $this->credentialRules);

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
            throw new \Exception();
        }
        catch (ValidationException $e)
        {
            return response()->json($e->errors());
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => 'INVALID_USERNAME_PASSWORD']);
        }
    }
}
