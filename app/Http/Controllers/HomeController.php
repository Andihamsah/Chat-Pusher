<?php

namespace App\Http\Controllers;

use App\Chat;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = User::where('id', '!=', Auth::id())->get();
        return view('home', compact('user'));
    }

    public function getmessage($user_id)
    {
        // getting all message for selected user
        // getting those message which is from = Auth::id() and to = user_id OR from = user_id and to = Auth::id()
        $my_id = Auth::id();
        $message = Chat::where(function ($query) use($user_id, $my_id)
        {
            $query->where(['from' => $my_id, 'to' => $user_id]);
        })
        ->orWhere(function ($query) use($user_id, $my_id)
        {
            $query->where(['to' => $my_id, 'from' => $user_id]);
        })
        ->get();

        return view('message.index', compact('message'));
    }

    public function sendMessage(Request $request)
    {
        $from       = Auth::id();
        $to         = $request->receiver_id;
        $message    = $request->message;

        $data = new Chat();
        $data->from     = $from;
        $data->to       = $to;
        $data->message  = $message;
        $data->is_read  = 0; // message will be unread when sending message
        
        $data->save();

        // PHP Pusher

        $options = [
            'cluster' => 'ap1',
            'useTLS' => true
        ];

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $data = ['from' => $from, 'to' => $to]; // mengirim dari dan ke user_id ketika menekan enter
        $pusher->trigger('my-channel', 'my-event', $data);
    }
}
