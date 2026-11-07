<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index()
    {

        $data['title'] = 'User List';
        $data['rows'] = User::orderBy('id', 'desc')->get();

        return view('admin.customer.index', compact('data'));
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Customer';

        $user = User::find($id);

        return view('admin.customer.edit', compact('data', 'user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {

            $user->name = $request->name;
            $user->email = $request->email;
            $user->status = $request->status;

            if ($request->hasFile('image')) {

                // Delete old image
                if ($user->image && file_exists(public_path($user->image))) {
                    unlink(public_path($user->image));
                }

                $file = $request->file('image');

                $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

                $directory = public_path('uploads/users');

                if (! file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                $file->move($directory, $filename);

                $user->image = 'uploads/users/'.$filename;
            }

            $user->save();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Customer updated successfully.');

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the customer.');
        }
    }

    public function destroy(User $user)
    {
        try {

            // Delete image if exists
            if ($user->image && file_exists(public_path($user->image))) {
                unlink(public_path($user->image));
            }

            $user->delete();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Customer deleted successfully.');

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with('error', 'Something went wrong while deleting the customer.');
        }
    }

}
