<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{

    public function send(Request $request): JsonResponse {
        $rules = [
            'message' => 'required|string',
            'user_id' => 'nullable|integer',
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

//        $author = Author::create($request->all());

        return response()->json(['success' => true], 201);
    }

    public function receive(Request $request): JsonResponse {
        return response()->json(['success' => true], 201);
    }
}
