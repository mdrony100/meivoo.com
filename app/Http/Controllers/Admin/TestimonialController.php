<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends Controller
{
    protected $testimonial;
    public $user;
    public function __construct(Testimonial $testimonial)
    {
        $this->testimonial = $testimonial;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }


    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('admin.testimonial.index')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Testimonial List';
        $data['testimonials'] = Testimonial::orderBy('id', 'asc')->get();
        return view('admin.testimonial.index', $data);
    }

    public function create()
    {
        // if (is_null($this->user) || !$this->user->can('admin.testimonial.create')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }

        $data['title'] = 'Testimonial Create';
        return view('admin.testimonial.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'message' => 'required',
            'image' => 'required|image',
            'status' => 'required',
        ]);

        try {

            $testimonial = new Testimonial();

            $testimonial->name = $request->name;
            $testimonial->message = $request->message;
            $testimonial->status = $request->status;

            if ($request->hasFile('image')) {

                $image = $request->file('image');

                $base_name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $base_name = Str::lower(str_replace(' ', '-', $base_name));

                $image_name = $base_name . "-" . uniqid() . "." . $image->getClientOriginalExtension();

                $file_path = 'uploads/testimonial';

                $image->move(public_path($file_path), $image_name);

                $testimonial->image = $file_path . '/' . $image_name;
            }

            $testimonial->save();

        } catch (\Exception $e) {

            return redirect()
            ->route('admin.testimonial.index')
            ->with('error', 'Something went wrong while creating testimonial.');
        }

        return redirect()
            ->route('admin.testimonial.index')
            ->with('success', 'Testimonial created successfully.');

        // return redirect()->route('admin.testimonial.index');
    }

    public function edit($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.testimonial.edit')) {
        //     abort(403, 'Sorry !! You are Unauthorized.');
        // }
        $title = 'testimonial Edit';
        $testimonial = Testimonial::find($id);
        return view('admin.testimonial.edit', compact('testimonial', 'title'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'message' => 'required',
            'image' => 'nullable|image',
            'status' => 'required',
        ]);

        try {

            $testimonial = Testimonial::findOrFail($id);

            $testimonial->name = $request->name;
            $testimonial->subtitle = $request->subtitle;
            $testimonial->message = $request->message;
            $testimonial->status = $request->status;

            if ($request->hasFile('image')) {

                // Delete old image if exists
                if ($testimonial->image && File::exists(public_path($testimonial->image))) {
                    File::delete(public_path($testimonial->image));
                }

                $image = $request->file('image');

                $base_name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $base_name = Str::lower(str_replace(' ', '-', $base_name));

                $image_name = $base_name . "-" . uniqid() . "." . $image->getClientOriginalExtension();

                $file_path = 'uploads/testimonial';

                $image->move(public_path($file_path), $image_name);

                $testimonial->image = $file_path . '/' . $image_name;
            }

            $testimonial->save();

            return redirect()
                ->route('admin.testimonial.index')
                ->with('success', 'Testimonial updated successfully.');

        } catch (\Exception $e) {

            return redirect()
                ->route('admin.testimonial.index')
                ->with('error', 'Something went wrong while updating testimonial.');
        }
    }

    public function delete($id)
    {
        try {

            $testimonial = Testimonial::findOrFail($id);

            // Delete image if exists
            if ($testimonial->image && File::exists(public_path($testimonial->image))) {
                File::delete(public_path($testimonial->image));
            }

            $testimonial->delete();

            return redirect()
                ->route('admin.testimonial.index')
                ->with('success', 'Testimonial deleted successfully.');

        } catch (\Exception $e) {

            return redirect()
                ->route('admin.testimonial.index')
                ->with('error', 'Something went wrong while deleting testimonial.');
        }
    }

}
