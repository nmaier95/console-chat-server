<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatRoom;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use phpDocumentor\Reflection\Types\Integer;

class ChatController extends Controller
{

    public function send(Request $request): JsonResponse
    {
        $rules = [
            'message' => 'required|string',
            'chat_room_id' => 'nullable|integer',
        ];

        try
        {
            $this->validate($request, $rules);
        }
        catch (ValidationException $e)
        {
            return response()->json($e->errors());
        }

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

    public function receive(Request $request, int $chat_room_id, int $timestamp = null): JsonResponse
    {
        try
        {
            /** @var Builder $chatQuery */
            $chatQuery = Chat::where([
                'chat_room_id' => $chat_room_id,
                'user_id' => $request->get('jwtPayload')->sub
            ]);

            if ($timestamp)
            {
                $date = new \DateTime();
                $date->setTimestamp($timestamp);
                $chatQuery = $chatQuery->whereDate('created_at', '>', $timestamp);
            }

            $messages = $chatQuery->with('user')->get();
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json(['error' => 'USER_OR_CHATROOM_NOT_FOUND']);
        }

        $now = new \DateTime(); $now->setTimestamp(time()); $now->format('Y-m-d');
        return response()->json(['success' => true, 'messages' => $messages, 'timestamp' => $now->getTimestamp()], 201);
    }
}
