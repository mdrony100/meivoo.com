<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;

class UserController extends Controller
{
    protected $settings;
    public $user;

    public function __construct(Setting $settings)
    {
        $this->settings = $settings;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index()
    {
        $data['title'] = 'Admin Users';
        $data['roles'] = Role::where('guard_name', 'admin')->orderBy('name')->get();
        $data['permissions'] = Permission::where('guard_name', 'admin')
            ->orderBy('group_name')
            ->orderBy('name')
            ->get()
            ->groupBy('group_name');
        $data['admins'] = Admin::with('roles', 'permissions')->orderBy('name')->get();

        return view('admin.users.index', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'role' => 'required|string',
            'permissions' => 'nullable|array',
            'password' => 'nullable|string|min:6',
        ]);

        DB::beginTransaction();

        try {
            // Create or get role
            $role = Role::firstOrCreate(['name' => $request->role, 'guard_name' => 'admin']);

            // Sync permissions only if role is new
            if (!empty($request->permissions)) {
                $role->syncPermissions($request->permissions);
            }

            // Create admin user
            $user = new Admin();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->email_verified_at = now();
            $user->status = 1;
            $user->password = $request->password ? bcrypt($request->password) : bcrypt(str()->random(8));
            $user->save();

            // Assign role
            $user->assignRole($role);

            DB::commit();

            $permissionsCount = $user->getAllPermissions()->count();

            return response()->json([
                'message' => 'Admin user created successfully!',
                'userId' => $user->id,
                'userName' => $user->name,
                'userEmail' => $user->email,
                'userInitials' => strtoupper(substr($user->name, 0, 2)),
                'userRole' => $role->name,
                'permissionsCount' => $permissionsCount,
                'createdAt' => $user->created_at->format('d/m/Y'),
                'lastLogin' => $user->last_login_at?->format('d/m/Y') ?? '-',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin User Create Error: ' . $e->getMessage());

            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function edit(Admin $user)
    {
        $role = $user->roles->first();
        $rolePermissions = $role ? $role->permissions->pluck('name')->toArray() : [];
        $userPermissions = $user->permissions->pluck('name')->toArray();
        // dd($rolePermissions, $userPermissions);
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'role' => $role?->name ?? '',
            'status' => $user->status,
            'rolePermissions' => $rolePermissions,
            'userPermissions' => $userPermissions
        ]);
    }

    public function update(Request $request, Admin $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string',
            'permissions' => 'nullable|array',
            'status' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            $user->name = $request->name;
            $user->status = $request->status;

            // Assign role
            $role = Role::firstOrCreate(['name' => $request->role, 'guard_name' => 'admin']);

            if (!empty($request->permissions)) {
                $role->syncPermissions($request->permissions);
            }

            $user->assignRole($role);

            $user->save();
            DB::commit();

            $permissionsCount = $user->getAllPermissions()->count();

            return response()->json([
                'message' => 'Admin user updated successfully!',
                'userId' => $user->id,
                'userName' => $user->name,
                'userRole' => $role->name,
                'permissionsCount' => $permissionsCount,
                'status' => $user->status,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin User Update Error: ' . $e->getMessage());

            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function suspend(Admin $user)
    {
        try {
            $user->status = $user->status ? 0 : 1;
            $user->save();

            $message = $user->status ? 'User activated successfully!' : 'User suspended successfully!';

            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            Log::error('Admin User Suspend Error: ' . $e->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function changePassword(Request $request, $id)
    {
        try {
            $request->validate([
                'password' => 'required|min:8|confirmed',
            ]);

            $user = Admin::findOrFail($id);

            $user->update([
                'password' => bcrypt($request->password),
            ]);

            return back()->with('success', 'Password updated successfully');
        } catch (\Throwable $e) {
            // unexpected error
            Log::error('Password change failed', [
                'user_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }


}
