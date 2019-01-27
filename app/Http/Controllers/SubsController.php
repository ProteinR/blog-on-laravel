<?php

namespace App\Http\Controllers;

use App\Mail\SubscribeEmail;
use App\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class SubsController extends Controller
{
    public function subscribe(Request $request)
    {
        $this->validate($request,[
            'email' => 'required|email|unique:subscriptions'
        ]);
        $subs = Subscription::add($request->get('email'));
        $subs->generateToken();
        Mail::to($subs)->send(new SubscribeEmail($subs));
        return redirect()->back()->with('status', 'Проверьте вашу почту!');
    }

    public function verify($token)
    {
        $sub = Subscription::where('token', $token)->firstOrFail();
        $sub->verify();

        return redirect('/')->with('status', 'Ваша почта подтверждена');
    }
}
