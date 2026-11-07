<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index()
    {
        $data['title']  = 'Newsletter Subscriber';
        $data['rows'] = NewsletterSubscriber::latest()->get();

        return view('admin.newsletter.index', compact('data'));
    }

    public function delete($id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);

        $subscriber->delete();

        return back()->with('success', 'Subscriber deleted successfully.');
    }
}
