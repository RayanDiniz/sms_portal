<?php

namespace App\Http\Controllers;

use App\Models\UsersPhoneNumber;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class HomeController extends Controller
{
    public function storePhoneNumber(Request $request) {
        $validateData = $request->validate([
            'phone_number' => 'required|unique:users_phone_number|numeric'
        ]);
        $user_phone_number_model = new UsersPhoneNumber($request->all());
        $user_phone_number_model->save();
        $this->sendMessage('Usuario registrado com sucesso!!', $request->phone_number);
        return back()->with(['success'=>"{$request->phone_number} registred"]);
    }

    public function show() {
        $users = UsersPhoneNumber::all();
        return view('welcome', compact("users"));
    }

    private function sendMessage($message, $recipients) {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");

        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients, ['from' => $twilio_number, 'body' => $message]);
    }

    public function sendCustomMessage(Request $request) {
        $validateData = $request->validate([
            'users' => 'required|array',
            'body' => 'required',
        ]);
        $recipients = $validateData["users"];

        foreach ($recipients as $recipient) {
            $this->sendMessage($validateData["body"], $recipient);
        }
        return back()->with(['success' => "Mensagem enviada!"]);
    }
}
