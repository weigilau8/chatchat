<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Events\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function index(Request $request) {

        $message = Conversation::where('group_id', $request->id)
                    ->get();
        $chat = DB::table('group_user')->select('user_id', 'group_id')
                    ->where('group_id', $request->id)
                    ->get();
        // $conversation = Conversation::find($request->id);
        // $message->join($chat);
        $n = $message->load('user');
        return $n;
        
    }

    public function store()
    {
        $conversation = Conversation::create([
            'message' => request('message'),
            'group_id' => request('group_id'),
            'user_id' => auth()->user()->id,
        ]);

        broadcast(new NewMessage($conversation))->toOthers();

        return $conversation->load('user');
    }
}
