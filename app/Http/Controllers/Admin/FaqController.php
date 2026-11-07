<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    protected $faq;
    public $user;

    public function __construct(Faq $faq)
    {
        $this->faq     = $faq;
        $this->middleware(function ($request, $next) {
             $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }


    public function index()
    {
        $data = [
            'title' => 'FAQ Management',
            'faqs'  => Faq::orderBy('order_id', 'asc')->get()
        ];
        return view('admin.faq.index', compact('data'));
    }

    public function create()
    {
        return view('admin.faq.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        try {
            $data               = new Faq();
            $data->title        = $request->question;
            $data->body         = $request->answer;
            $data->status       = $request->status;
            $data->order_id     = $request->order_number ?? Faq::max('order_id') + 1;
            $data->save();
            return redirect()->back()->with('success', 'FAQ created successfully');
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->back()->with('error', 'Something went wrong while creating the FAQ.')->withInput();
        }
    }


    public function show($id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.faq.view', compact('faq'))->render();
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.faq.edit', compact('faq'))->render();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        try {
            $data               = Faq::findOrFail($id);
            $data->title        = $request->question;
            $data->body         = $request->answer;
            $data->status       = $request->status;
            $data->order_id     = $request->order_number ?? Faq::max('order_id') + 1;
            $data->update();
             return redirect()->back()->with('success', 'FAQ updated successfully');
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->back()->with('error', 'Something went wrong while updating the FAQ.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            $faq->delete();
            return redirect()->back()->with('success', 'FAQ deleted successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong while deleting the FAQ.');
        }
    }
}
