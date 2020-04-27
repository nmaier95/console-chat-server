<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatRoom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request): JsonResponse
    {
        $rules = [
            'message' => 'required|string',
            'chat_room_id' => 'nullable|integer',
        ];

        try
        {
            $this->validate($request, $rules);

            $chatRoomId = $request->post('chat_room_id');
            if (!$chatRoomId)
            {
                $chatRoom = ChatRoom::create();
                $chatRoomId = $chatRoom->getAttribute('id');
            }

            Chat::create([
                'chat_room_id' => $chatRoomId,
                'user_id' => $request->get('jwtPayload')->sub,
                'message' => $request->post('message'),
            ]);

            return response()->json(['success' => true, 'chat_room_id' => $chatRoomId], 201);
        }
        catch (ValidationException $e)
        {
            return response()->json($e->errors());
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => 'SERVER_ERROR']); // most likely a invalid chat_room_id was passed
        }
    }

    /**
     * @param Request $request
     * @param int $chat_room_id
     * @param int|null $timestamp
     * @return JsonResponse
     * @throws \Exception
     */
    public function receive(Request $request, int $chat_room_id, int $timestamp = null): JsonResponse
    {
        try
        {
            /** @var Builder $chatQuery */
            $chatQuery = Chat::where([
                'chat_room_id' => $chat_room_id,
            ])->where('user_id', '!=', $request->get('jwtPayload')->sub);

            if ($timestamp)
            {
                $chatQuery = $chatQuery->where('created_at', '>', Date::createFromTimestamp($timestamp));
            }

            $messages = $chatQuery->with('user')->get();
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json(['error' => 'USER_OR_CHATROOM_NOT_FOUND']);
        }

        return response()->json(['success' => true, 'messages' => $messages, 'timestamp' => count($messages) > 0 ? Date::now()->getTimestamp() : $timestamp], 200);
    }
}
