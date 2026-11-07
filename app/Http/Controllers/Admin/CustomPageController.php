<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomPageController extends Controller
{
    public $user;
    protected $page;

    public function __construct(CustomPage $page)
    {
        $this->page = $page;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // if (is_null($this->user) || !$this->user->can('admin.cpage.index')) {
        //     abort(403, 'Unauthorized.');
        // }

        $data['title'] = 'Custom Page List';
        $data['rows']  = CustomPage::get();
        return view('admin.custom-page.index', compact('data'));
    }

    public function create()
    {
        // if (is_null($this->user) || !$this->user->can('admin.cpage.create')) {
        //     abort(403, 'Unauthorized.');
        // }

        $data['title'] = 'Create Custom Page';
        return view('admin.custom-page.create', compact('data'));
    }


    public function edit($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.cpage.edit')) {
        //     abort(403, 'Unauthorized.');
        // }

        $data['title'] = 'Edit Custom Page';
        $data['row']   = CustomPage::find($id);
        return view('admin.custom-page.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'             => 'required|max:60',
            'meta_title'        => 'required|max:60',
            'meta_keywords'     => 'required',
            'meta_description'  => 'required|max:160',
        ]);

        $this->resp = $this->page->putUpdate($request, $id);

        if (!$this->resp->status) {
            return redirect()->back()->with('error', 'Something wrong please try again');
            // Toastr::error($this->resp->msg, 'Error');
            // return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
        }

        return redirect()
            ->route($this->resp->redirect_to)
            ->with('success', 'Custom page Updated successfully');
        // Toastr::success($this->resp->msg, 'Success');
        // return redirect()->route($this->resp->redirect_to);
    }

    public function view($id)
    {
        // if (is_null($this->user) || !$this->user->can('admin.cpage.view')) {
        //     abort(403, 'Unauthorized.');
        // }

        $data['title'] = 'View Custom Page';
        $data['row']   = CustomPage::find($id);

        return view('admin.custom-page.view', compact('data'));
    }

    public function postEditorImageUpload(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $file_path = 'assets/uploads/page';
            $base_name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $base_name = str_replace(' ', '-', $base_name);
            $feature_image = $base_name . "-" . uniqid() . '.webp';

            $img = \Image::make($image->getRealPath());
            $img->save($file_path . '/' . $feature_image);

            return url('/') . '/' . $file_path . '/' . $feature_image;
        }
    }

    public function getDelete(int $id)
    {
        DB::beginTransaction();

        try {
            $page = CustomPage::findOrFail($id);

            // Prevent deleting system pages
            if (in_array(Str::slug($page->title), ['about', 'privacy-policy', 'terms-condition'])) {
                return redirect()
                    ->route('admin.cpage.index')
                    ->with('error', 'This page cannot be deleted!');
            }

            $page->delete();
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('admin.cpage.index')
                ->with('error', 'Unable to delete page!');
        }

        DB::commit();

        return redirect()
            ->route('admin.cpage.index')
            ->with('success', 'Successfully deleted page!');
    }

    public function changeActiveStatus(Request $request)
    {
        $id = $request->id;
        $page = CustomPage::findOrFail($id);
        $page->is_active = !$page->is_active;
        $page->updated_by = Auth::user()->id;
        $page->update();

        return response()->json($page);
    }
}
