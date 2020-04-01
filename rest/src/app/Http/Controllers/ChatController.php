<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{

    public function send(Request $request): JsonResponse {
        $rules = [
            'message' => 'required|string',
            'user_id' => 'required|integer',
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
        if(!$chatRoomId) {
            $chatRoom = ChatRoom::create();
            $chatRoomId = $chatRoom->getAttribute('id');
        }

        Chat::create([
            'chat_room_id' => $chatRoomId,
            'user_id' => $request->post('user_id'),
            'message' => $request->post('message'),
        ]);

        return response()->json(['success' => true], 201);
    }

    public function receive(Request $request): JsonResponse {
        $rules = [
            'chat_room_id' => 'required|integer',
            'user_id' => 'required|integer',
        ];

        try
        {
            $this->validate($request, $rules);
        }
        catch (ValidationException $e)
        {
            return response()->json($e->errors());
        }

        return response()->json(Chat::where([
            'chat_room_id' => $request->get('chat_room_id'),
            'user_id' => $request->get('user_id')
        ])->get(), 201);
    }
}
