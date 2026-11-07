<?php

namespace App\Http\Controllers\Admin;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\ContactReplyMail;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public $user;
    protected $contact;

    public function __construct(Contact $contact)
    {
        $this->contact     = $contact;
        $this->middleware(function ($request, $next) {
             $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }



    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('admin.contact.index')) {
        //      abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title']  = 'Contacts';
        $data['rows']   =  Contact::orderBy('id', 'desc')->get();
        return view('admin.contact.index',compact('data'));
   }

   public function view($id)
   {
        // if (is_null($this->user) || !$this->user->can('admin.contact.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

       $data['row']= Contact::find($id);
       $html = view('admin.contact.view', compact('data'))->render();
       return response()->json($html);
   }


   public function delete($id)
   {
        // if (is_null($this->user) || !$this->user->can('admin.contact.delete')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

       DB::beginTransaction();
       try {
           $contact = Contact::find($id);
           $contact->delete();
       } catch (\Exception $e) {
           DB::rollback();

           return redirect()->route('admin.contact.index')->with('error', 'Something went wrong please try again');
        //    Toastr::error(__('messages.toastr.contact_delete_error'), 'Error', ["positionClass" => "toast-top-center"]);
        //    return redirect()->route('admin.contact.index');
       }
       DB::commit();

       return redirect()->route('admin.contact.index')->with('success', 'Contact deleted successfully.');
        //    Toastr::success(__('messages.toastr.contact_delete_success'), 'Success', ["positionClass" => "toast-top-center"]);
        //    return redirect()->route('admin.contact.index');
   }

    public function reply($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.contact.reply')) {
        //    abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $contact = Contact::findOrFail($id);

        $data['title'] = "Reply to Contact";
        $data['contact'] = $contact;

        // Sample values
        $data['sample_subject'] = "Re: Your inquiry at " . config('app.name');
        $data['sample_body'] = "Hello " . $contact->name . ",\n\nThank you for reaching out. We have received your message and will get back to you shortly.\n\nBest regards,\n" . config('app.name') . " Team";

        return view('admin.contact.reply', compact('data'));
    }

    public function sendReply(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        $contact = Contact::findOrFail($id);

        $data = [];
        $data = [
            'subject'     => $request->subject,
            'body'        => $request->body,
            'site_url'    => route('home'),
            'footer'      => '0',
            'site_name'   => config('app.name'),
            'copyright'   => ' © ' . ' ' . Carbon::now()->format('Y') .' '. config('app.name') . ' ' . 'All rights reserved.',
        ];

        try {
            Mail::to($contact->email)->send(new ContactReplyMail($data));

            $contact->reply_status = 1;
            $contact->save();

            Toastr::success('Reply sent successfully.', 'Success', ["positionClass" => "toast-top-center"]);
            return redirect()->route('admin.contact.index');
        } catch (Exception $e) {
            Toastr::error('Failed to send reply: ' . $e->getMessage(), 'Error', ["positionClass" => "toast-top-center"]);
            return redirect()->back();
        }
    }

}


