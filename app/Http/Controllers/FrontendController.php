<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\Contact;
use App\Models\CustomPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Log;
use App\Models\Testimonial;


class FrontendController extends Controller
{
    public function __construct()
    {
        View::share('settings', getSetting());
    }

    public function home()
    {
        $testimonials = Testimonial::where('status', 1)->orderBy('id', 'asc')->get();
        $settings = getSetting();

        return view('frontend.home', compact('testimonials', 'settings'));
    }


    public function privacyPolicy()
    {
        $page = CustomPage::where('url_slug', 'privacy-policy')->first()
            ?? new CustomPage([
                'title' => 'Privacy Policy',
                'url_slug' => 'privacy-policy',
                'meta_description' => 'How Meivoo collects, uses and protects your family data.',
            ]);
        return view('frontend.privacy_policy', compact('page'));
    }

    public function termsCondition()
    {
        $page = CustomPage::where('url_slug', 'terms-conditions')->first()
            ?? new CustomPage([
                'title' => 'Terms & Conditions',
                'url_slug' => 'terms-conditions',
                'meta_description' => 'The terms that apply when you use Meivoo.',
            ]);

        return view('frontend.terms_conditions', compact('page'));
    }

    public function contact()
    {
        $data['title'] = 'Contact Us';
        return view('frontend.contact', $data);
    }


    public function contactStore(Request $request)
    {
        $request->validate([
            'name'    => 'required|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'nullable|max:255',
            'message' => 'required',
        ]);

        try {

            $contact = Contact::create([
                'name'    => $request->name,
                'email'   => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);

            // Send email to admin
            try {
                $settings = getSetting();
                Mail::to($settings->support_email)->send(
                    new ContactFormMail($contact)
                );
            } catch (\Exception $e) {
                Log::error('Failed to send contact form email: ' . $e->getMessage());
            }

            return redirect()
                ->back()
                ->with('success', 'Your message has been sent successfully.');
        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
