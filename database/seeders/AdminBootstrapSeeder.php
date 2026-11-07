<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminBootstrapSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'admin.dashboard.view', 'group_name' => 'Dashboard'],
            ['name' => 'admin.settings.index', 'group_name' => 'Settings'],
            ['name' => 'admin.settings.store', 'group_name' => 'Settings'],
            ['name' => 'admin.profile.view', 'group_name' => 'Profile'],
            ['name' => 'admin.profile.edit', 'group_name' => 'Profile'],
            ['name' => 'admin.profile.update', 'group_name' => 'Profile'],
            ['name' => 'admin.password.update', 'group_name' => 'Profile'],
            ['name' => 'admin.users.index', 'group_name' => 'Users'],
            ['name' => 'admin.users.store', 'group_name' => 'Users'],
            ['name' => 'admin.users.edit', 'group_name' => 'Users'],
            ['name' => 'admin.users.update', 'group_name' => 'Users'],
            ['name' => 'admin.users.suspend', 'group_name' => 'Users'],
            ['name' => 'admin.users.change-password', 'group_name' => 'Users'],
            ['name' => 'admin.roles.index', 'group_name' => 'Roles'],
            ['name' => 'admin.roles.create', 'group_name' => 'Roles'],
            ['name' => 'admin.roles.store', 'group_name' => 'Roles'],
            ['name' => 'admin.roles.edit', 'group_name' => 'Roles'],
            ['name' => 'admin.roles.update', 'group_name' => 'Roles'],
            ['name' => 'admin.faq.index', 'group_name' => 'FAQ'],
            ['name' => 'admin.faq.store', 'group_name' => 'FAQ'],
            ['name' => 'admin.faq.view', 'group_name' => 'FAQ'],
            ['name' => 'admin.faq.edit', 'group_name' => 'FAQ'],
            ['name' => 'admin.faq.update', 'group_name' => 'FAQ'],
            ['name' => 'admin.faq.delete', 'group_name' => 'FAQ'],
            ['name' => 'admin.contact.index', 'group_name' => 'Contact'],
            ['name' => 'admin.contact.delete', 'group_name' => 'Contact'],
            ['name' => 'admin.seo.index', 'group_name' => 'SEO'],
            ['name' => 'admin.seo.view', 'group_name' => 'SEO'],
            ['name' => 'admin.seo.delete', 'group_name' => 'SEO'],
            ['name' => 'admin.testimonial.index', 'group_name' => 'Testimonial'],
            ['name' => 'admin.testimonial.create', 'group_name' => 'Testimonial'],
            ['name' => 'admin.testimonial.edit', 'group_name' => 'Testimonial'],
            ['name' => 'admin.testimonial.destroy', 'group_name' => 'Testimonial'],
            ['name' => 'admin.schedule-metting.index', 'group_name' => 'Schedule Meeting'],
            ['name' => 'admin.schedule-metting.view', 'group_name' => 'Schedule Meeting'],
            ['name' => 'admin.schedule-metting.delete', 'group_name' => 'Schedule Meeting'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'admin'],
                ['group_name' => $permission['group_name']]
            );
        }

        $role = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'admin']);
        $role->syncPermissions(Permission::where('guard_name', 'admin')->pluck('name')->all());

        $admin = Admin::firstOrCreate(
            ['email' => 'arobil@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'status' => 1,
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles([$role->name]);
    }
}
