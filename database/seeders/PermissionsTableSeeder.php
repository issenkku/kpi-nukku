<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // ===== Indicator =====
            'view-indicator-dashboard',
            'create-indicator',
            'view-indicator',
            'edit-indicator',
            'delete-indicator',
            'export-indicator',
            'import-indicator',


            // ===== Users =====
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // ===== Departments=====
            'view-departments',
            'create-departments',
            'edit-departments',
            'delete-departments',

            // ===== Categories =====
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',

            // ===== Standards =====
            'view-standards',
            'create-standards',
            'edit-standards',
            'delete-standards',

            // ===== Settings =====
            'view-settings',
            'create-settings',
            'edit-settings',

            // ===== Evidences =====
            'view-evidence',
            'create-evidence',
            'edit-evidence',
            'delete-evidence',
            'download-evidence',

            // ===== Sar Report =====
            'view-sar_report',
            'export-sar_report',
            'create-sar_report',
            'edit-sar_report',
            'delete-sar_report',


            // ===== Dashboard =====
            'view-dashboard',
            'export-dashboard',

            // ===== Dashboard KPI per User =====
            'view-dashboard-kpi-user',
            'show-dashboard-kpi-user',

            // ===== Auth/โปรไฟล์พื้นฐาน (เผื่อใช้) =====
            'edit-profile'
        ];


        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create roles and assign permissions

        // Admin role - full access
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        $systemAdmin = Role::firstOrCreate(['name' => 'system_admin']);
        $systemAdmin->syncPermissions([
            // ===== Indicator =====
            'view-indicator-dashboard',
            'create-indicator',
            'view-indicator',
            'edit-indicator',
            'delete-indicator',
            'export-indicator',
            'import-indicator',

            // ===== Users =====
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // ===== Departments=====
            'view-departments',
            'create-departments',
            'edit-departments',
            'delete-departments',

            // ===== Categories =====
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',

            // ===== Standards =====
            'view-standards',
            'create-standards',
            'edit-standards',
            'delete-standards',

            // ===== Settings =====
            'view-settings',
            'create-settings',
            'edit-settings',

            // ===== Evidences =====
            'view-evidence',
            'create-evidence',
            'edit-evidence',
            'delete-evidence',
            'download-evidence',

            // ===== Sar Report =====
            'view-sar_report',
            'export-sar_report',
            'create-sar_report',
            'edit-sar_report',
            'delete-sar_report',

            // ===== Dashboard =====
            'view-dashboard',
            'export-dashboard',

            // ===== Auth/โปรไฟล์พื้นฐาน (เผื่อใช้) =====
            'edit-profile'

        ]);

        $qaAdmin = Role::firstOrCreate(['name' => 'qa_admin']);
        $qaAdmin->syncPermissions([
            // ===== Indicator =====
            'view-indicator-dashboard',
            'create-indicator',
            'view-indicator',
            'edit-indicator',
            'delete-indicator',
            'export-indicator',
            'import-indicator',

            // ===== Users =====
            'view-users',

            // ===== Departments =====
            'view-departments',

            // ===== Categories =====
            'view-categories',

            // ===== Standards =====
            'view-standards',

            // ===== Settings =====
            'view-settings',

            // ===== Evidences =====
            'view-evidence',
            'create-evidence',
            'edit-evidence',
            'delete-evidence',
            'download-evidence',

            // ===== Dashboard =====
            'view-dashboard',
            'export-dashboard',

            // ===== Sar Report =====
            'view-sar_report',
            'export-sar_report',
            'create-sar_report',
            'edit-sar_report',
            'delete-sar_report',
            
            // ===== Dashboard KPI per User =====
            'view-dashboard-kpi-user',
            'show-dashboard-kpi-user',

            // ===== Auth/โปรไฟล์พื้นฐาน (เผื่อใช้) =====
            'edit-profile'

        ]);

        $administrationAdmin = Role::firstOrCreate(['name' => 'administration_admin']);
        $administrationAdmin->syncPermissions([
            // ===== Indicator =====
            'view-indicator-dashboard',
            'view-indicator',
            'create-indicator',
            'edit-indicator',
            'delete-indicator',
            'export-indicator',
            'import-indicator',

            // ===== Users =====
            'view-users',

            // ===== Departments =====
            'view-departments',

            // ===== Categories =====
            'view-categories',

            // ===== Standards =====
            'view-standards',

            // ===== Settings =====
            'view-settings',

            // ===== Evidences =====
            'view-evidence',
            'download-evidence',

            // ===== Dashboard =====
            'view-dashboard',
            'export-dashboard',

            // ===== Dashboard KPI per User =====
            'view-dashboard-kpi-user',
            'show-dashboard-kpi-user',

            
            // ===== Auth/โปรไฟล์พื้นฐาน (เผื่อใช้) =====
            'edit-profile'
        ]);

        $user = Role::firstOrCreate(['name' => 'user']);
        $user->syncPermissions([
            // ===== Indicator =====
            // 'view-indicator-dashboard',

            // // ===== Users =====
            // 'view-users',

            // // ===== Departments=====
            // 'view-departments',
            // 'create-departments',
            // 'edit-departments',
            // 'delete-departments',

            // // ===== Categories =====
            // 'view-categories',
            // 'create-categories',
            // 'edit-categories',
            // 'delete-categories',

            // // ===== Standards =====
            // 'view-standards',
            // 'create-standards',
            // 'edit-standards',
            // 'delete-standards',

            // // ===== Settings =====
            // 'view-settings',
            // 'create-settings',
            // 'edit-settings',

            // ===== Evidences =====
            'view-evidence',
            'create-evidence',
            'edit-evidence',
            'delete-evidence',
            'download-evidence',

            // // ===== Dashboard =====
            // 'view-dashboard',
            // 'export-dashboard',

            // ===== Dashboard KPI per User =====
            'view-dashboard-kpi-user',
            'show-dashboard-kpi-user',

            // // ===== Auth/โปรไฟล์พื้นฐาน (เผื่อใช้) =====
            // 'edit-profile'
        ]);

        $this->command->info('✅ Roles and permissions created successfully!');
    }
}
