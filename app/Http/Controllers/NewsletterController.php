<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\NewsletterSubscriber;


class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        Log::info('Newsletter Subscription Request', [
            'email' => $request->email,
        ]);

        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $existingSubscriber = NewsletterSubscriber::where('email', $request->email)->first();

        if ($existingSubscriber) {
            return redirect()->back()->with('error', 'This email is already subscribed.');
        }

        NewsletterSubscriber::create([
            'email' => $request->email,
        ]);

        Log::info('Newsletter Subscription Successful', [
            'email' => $request->email,
        ]);

        return redirect()->back()->with('success', 'Thank you for subscribing to our newsletter.');
    }
}
