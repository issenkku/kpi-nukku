<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    private function realignSequence(string $table, string $column = 'id'): void
    {
        try {
            // Align Postgres sequence to MAX(id) so next inserts won't collide
            DB::statement(
                "SELECT setval(pg_get_serial_sequence('".$table."','".$column."'), COALESCE((SELECT MAX(".$column.') FROM '.$table.'), 0), true)'
            );
        } catch (\Throwable $e) {
            // Ignore for tables without sequences or on non‑pgsql drivers
        }
    }

    private function realignAllSequences(): void
    {
        $tables = [
            'departments', 'users',
            'standards', 'categories', 'indicators', 'criterias',
            'evidence', 'checklist_items', 'formulas', 'variables',
        ];
        foreach ($tables as $t) {
            $this->realignSequence($t);
        }
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new \RuntimeException(
                'DatabaseSeeder contains destructive demo data and is disabled in production. Run RolesAndPermissionsSeeder directly.'
            );
        }

        // Clear cached roles/permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Truncate core tables to avoid duplicate key errors on reseed (PostgreSQL)
        DB::statement('TRUNCATE TABLE 
            model_has_permissions,
            model_has_roles,
            role_has_permissions,
            permissions,
            roles,
            users,
            departments,
            categories,
            standards,
            indicators,
            assignments,
            criterias,
            evidence,
            checklist_items,
            formulas,
            variables,
            variable_formulas,
            settings
            RESTART IDENTITY CASCADE');

        // ✅ Parent -> Child
        $this->call([
            DepartmentsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionsTableSeeder::class,
            UsersTableSeeder::class,
            StandardsTableSeeder::class,
            CategoriesTableSeeder::class,
            IndicatorsTableSeeder::class,       // ✅ ต้องมาก่อน criterias
            CriteriasTableSeeder::class,        // ✅ มาทีหลัง indicators
            AssignmentsTableSeeder::class,
            EvidenceTableSeeder::class,
            FormulasTableSeeder::class,         // ✅ ต้องมาก่อน variable_formulas
            VariablesTableSeeder::class,        // ✅ ต้องมาก่อน variable_formulas
            VariableFormulasTableSeeder::class, // ✅ มาทีหลัง
            ChecklistItemsTableSeeder::class,
            SettingsTableSeeder::class,
        ]);

        // Cleanup: remove all data related to indicators 57 and 58 (cascades will clean children)
        DB::table('indicators')->whereIn('id', [57, 58])->delete();

        // Reseed core data affected by the cleanup
        $this->call(IndicatorsTableSeeder::class);
        $this->call(VariablesTableSeeder::class);
        $this->call(CriteriasTableSeeder::class);
        $this->call(ChecklistItemsTableSeeder::class);

        // IMPORTANT: After reseeding indicators/variables, formulas and their pivot are
        // cascaded-deleted earlier. Seed them again to ensure UI loads formulas.
        $this->call(FormulasTableSeeder::class);
        $this->call(VariableFormulasTableSeeder::class);

        // After inserting explicit IDs in many seeders, ensure sequences are aligned
        $this->realignAllSequences();
    }
}
