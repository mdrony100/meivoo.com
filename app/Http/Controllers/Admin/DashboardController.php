<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\FamilyMember;
use App\Models\FamilyTree;
use App\Models\FeedPost;
use App\Models\Message;
use App\Models\PostMedia;
use App\Models\Role;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class DashboardController extends Controller
{
    protected $customer;

    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();

            return $next($request);
        });
    }

    public function dashboard()
    {
        $totalFamilyTrees = FamilyTree::count();
        $totalMessages = Message::count();
        $totalPosts = FeedPost::count();
        $totalUser = User::count();
        $activeUsers = User::where('status', 1)->count();

        // Monthly users
        $chartRaw = User::selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, COUNT(*) as total")
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $chartUsers = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartUsers[] = $chartRaw[$i] ?? 0;
        }

        $chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Last 7 days
        $last7DaysLabels = [];
        $last7DaysUsers = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $last7DaysLabels[] = $date->format('D');
            $last7DaysUsers[] = User::whereDate('created_at', $date)->count();
        }

        $data = [
            'title' => 'Dashboard',

            'overviewCards' => [
                [
                    'label' => 'Total Users',
                    'value' => $totalUser,
                    'icon' => 'bi-people',
                    'accent' => 'primary',
                ],
                [
                    'label' => 'Active Users',
                    'value' => $activeUsers,
                    'icon' => 'bi-person-check',
                    'accent' => 'success',
                ],
            ],

            'chartMonths' => $chartMonths,
            'chartUsers' => $chartUsers,

            'last7DaysLabels' => $last7DaysLabels,
            'last7DaysEmails' => $last7DaysUsers,

            'recentLogins' => User::whereNotNull('last_login_at')
                ->orderBy('last_login_at', 'desc')
                ->take(5)
                ->get(),

            'quickActions' => [
                ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'bi-sliders'],
                ['label' => 'Manage Users', 'route' => 'admin.users.index', 'icon' => 'bi-people'],
            ],
        ];

        $treeChartMonths = [];
        $treeChartValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $treeChartMonths[] = $date->format('M');
            $treeChartValues[] = FamilyMember::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        $data['treeChartMonths'] = $treeChartMonths;
        $data['treeChartValues'] = $treeChartValues;

        return view('admin.dashboard', compact('data', 'totalUser', 'totalFamilyTrees', 'totalMessages', 'totalPosts'));
    }

    // public function dashboard()
    // {
    //     $totalUser = User::all()->count();
    //     $activeUsers = User::where('status', 1)->count();
    //     $data = [
    //         'title' => 'Dashboard',
    //         'overviewCards' => [
    //             ['label' => 'Total Users', 'value' => $totalUser, 'icon' => 'bi-people', 'accent' => 'primary', 'delta' => '+12.4%', 'note' => 'vs last month'],
    //             ['label' => 'Active Users', 'value' => $activeUsers, 'icon' => 'bi-person-check', 'accent' => 'success', 'delta' => '+8.1%', 'note' => 'healthy retention'],
    //             // ['label' => 'Trial Users', 'value' => 34, 'icon' => 'bi-hourglass-split', 'accent' => 'warning', 'delta' => '+5', 'note' => 'expiring soon'],
    //         ],
    //         'chartMonths' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    //         'chartUsers' => [42, 58, 61, 74, 88, 92, 104, 118, 133, 141, 153, 168],
    //         'last7DaysLabels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    //         'last7DaysEmails' => [118, 132, 96, 171, 148, 184, 210],
    //         'statusLabels' => ['Completed', 'Pending', 'Cancelled', 'Confirmed'],
    //         'statusValues' => [57, 21, 8, 14],
    //         'recentLogins' => collect([
    //             (object) ['user_name' => 'Super Admin', 'user_email' => 'arobil@gmail.com', 'logged_in_at' => now()->subMinutes(14)],
    //             (object) ['user_name' => 'Support Lead', 'user_email' => 'support@meivoo.com', 'logged_in_at' => now()->subHours(2)],
    //             (object) ['user_name' => 'Content Manager', 'user_email' => 'content@meivoo.com', 'logged_in_at' => now()->subHours(6)],
    //         ]),
    //         'quickActions' => [
    //             ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'bi-sliders'],
    //             ['label' => 'Create Role', 'route' => 'admin.roles.create', 'icon' => 'bi-shield-plus'],
    //             ['label' => 'Manage Users', 'route' => 'admin.users.index', 'icon' => 'bi-people'],
    //             ['label' => 'Clear Cache', 'route' => 'admin.cacheClear', 'icon' => 'bi-lightning-charge'],
    //         ],
    //         'miniStats' => [
    //             ['label' => 'Open Tickets', 'value' => 16, 'tone' => 'primary'],
    //             ['label' => 'Unread Messages', 'value' => 9, 'tone' => 'info'],
    //             ['label' => 'Pending Reviews', 'value' => 11, 'tone' => 'warning'],
    //         ],
    //         'timeline' => [
    //             ['time' => '08:30', 'title' => 'Daily sync completed', 'text' => 'System health and queues checked successfully.'],
    //             ['time' => '10:15', 'title' => 'New user onboarded', 'text' => 'Trial account moved into active state.'],
    //             ['time' => '13:40', 'title' => 'Role permissions updated', 'text' => 'Dashboard and FAQ permissions refreshed.'],
    //             ['time' => '16:05', 'title' => 'Email batch delivered', 'text' => 'Notification jobs completed with no failures.'],
    //         ],
    //     ];

    //     return view('admin.dashboard', compact('data'));
    // }

    // public function cacheClear()
    // {
    //     toastr()->success('Cache Cleared');
    //     Artisan::call('route:clear');
    //     Artisan::call('optimize:clear'); // Don't call deprecated optimize()
    //     Artisan::call('view:clear');
    //     Artisan::call('config:clear');
    //     Artisan::call('cache:clear');
    //     Artisan::call('clear-compiled');
    //     Artisan::call('storage:link');
    //     Artisan::call('cache:forget spatie.permission.cache');
    //     Artisan::call('config:cache');

    //     // This is the equivalent of "php artisan permission:cache-reset"
    //     app(PermissionRegistrar::class)->forgetCachedPermissions();

    //     return back();
    // }

    public function cacheClear()
    {
        try {
            Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('clear-compiled');
        Artisan::call('optimize:clear');
        Artisan::call('storage:link');

            Artisan::call('cache:forget', ['key' => 'spatie.permission.cache']);

            return back()->with('success', 'Cache cleared successfully');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function adminProfile()
    {
        $roles = Role::latest()->get();

        return view('admin.profile.index', compact('roles'));
    }

    public function profileEdit()
    {
        return view('admin.profile.edit');
    }

    public function profileUpdate(Request $request)
    {

        $user_id = Auth::user()->id;
        $user = Admin::where('id', $user_id)->first();
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins,email,'.$user->id,
        ]);
        // $this->validate($request, [
        //     'name'  => 'required',
        //     'email'   => 'required|unique:admins,email,' . $user->id . ',id',
        // ]);

        try {

            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->hasFile('image')) {

                // Delete the existing image file if it exists
                if (File::exists(public_path($user->image))) {
                    File::delete(public_path($user->image));
                }

                // Upload and save the new image
                $image = $request->file('image');
                $base_name = preg_replace('/\..+$/', '', $image->getClientOriginalName());
                $base_name = explode(' ', $base_name);
                $base_name = implode('-', $base_name);
                $base_name = Str::lower($base_name);
                $image_name = $base_name.'-'.uniqid().'.'.$image->getClientOriginalExtension();
                $file_path = 'uploads/admin';
                $image->move(public_path($file_path), $image_name);
                $user->image = $file_path.'/'.$image_name;
            }

            $user->save();
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occured while updating profile information');
        }

        return redirect()->route('admin.profile')->with('success', 'Profile information updated successfully');
    }

    public function passwordUpdate(Request $request)
    {

        $request->validate([
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);
        // $this->validate($request, [

        //     'password'          => 'required|min:6',
        //     'confirm_password'  => 'required|same:password',
        // ]);

        try {
            $user = Admin::find(Auth::user()->id);
            $user->password = Hash::make($request->input('password'));
            $user->update();
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occured while updating profile information');
        }

        return back()->with('success', 'Password updated successfully');
    }
}
