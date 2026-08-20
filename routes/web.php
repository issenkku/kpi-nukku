<?php

// use App\Http\Controllers\Auth\Auth_ssoController; // deprecated
use App\Http\Controllers\AffiliationsController;
use App\Http\Controllers\Api\IndicatorApiController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthSSOController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardExportController;
use App\Http\Controllers\DashboardKpiAdminController;
use App\Http\Controllers\DashboardKpiUserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\IndicatorPresetController;
use App\Http\Controllers\KKUApiController;
use App\Http\Controllers\NotifycationController;
use App\Http\Controllers\ProgressReportController;
use App\Http\Controllers\SarReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SettingNotifyController;
use App\Http\Controllers\StandardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes (Public Access)
|--------------------------------------------------------------------------
*/

// Minimal API endpoints under web guard to support tests
Route::middleware(['auth', 'role:super_admin'])->prefix('api')->group(function () {
    Route::post('indicators', [IndicatorApiController::class, 'store']);
    Route::put('indicators/{indicator}', [IndicatorApiController::class, 'update']);
    Route::delete('indicators/{indicator}', [IndicatorApiController::class, 'destroy']);
});
Route::middleware('guest')->group(function () {
    // Redirect root to login
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // Authentication routes
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
    });

    Route::get('/auth/sso/login', [AuthSSOController::class, 'redirectToSSO'])->name('sso.login');
    Route::get('/auth/callback/login', [AuthSSOController::class, 'callback'])->name('sso.callback');
    // Additional callback path for providers configured with a short callback URL.
    Route::get('/auth', [AuthSSOController::class, 'callback'])->name('sso.callback.alt');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Logout)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->controller(AuthController::class)->group(function () {
    Route::post('/logout', 'logout')->name('logout');
});
Route::middleware('auth')->get('/auth/sso/logout', [AuthSSOController::class, 'logout'])->name('sso.logout');

// Minimal home route for post-login redirect used in tests
// Route::middleware('auth')->get('/home', function () {
//     return response('ok');
// });

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated + Permission-based)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // ===== DASHBOARD ROUTES =====
    Route::prefix('dashboard')->name('dashboard.')->middleware('permission:view-dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/result', [DashboardController::class, 'getData'])->name('getData');
        Route::get('/export', [DashboardExportController::class, 'export'])
            ->name('export')
            ->middleware('permission:export-dashboard');
    });

    // ===== INDICATOR ROUTES =====
    Route::prefix('indicator')->name('indicator.')->group(function () {
        // Dashboard
        Route::get('/', [IndicatorController::class, 'index'])
            ->name('index')
            ->middleware('permission:view-indicator-dashboard');

        // View
        Route::get('/{id}/show', [IndicatorController::class, 'show'])
            ->name('show')
            ->middleware('permission:view-indicator');

        // Create
        Route::get('/create', [IndicatorController::class, 'create'])
            ->name('create')
            ->middleware('permission:create-indicator');
        Route::post('/store', [IndicatorController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-indicator');

        // Edit & Update
        Route::get('/{id}/edit', [IndicatorController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit-indicator');
        Route::put('/{id}', [IndicatorController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-indicator');

        // Delete
        Route::delete('/{id}', [IndicatorController::class, 'delete'])
            ->name('delete')
            ->middleware('permission:delete-indicator');
        // Export
        Route::get('/{id}/export', [IndicatorPresetController::class, 'export'])
            ->name('export')
            ->middleware('permission:export-indicator');
        // Import
        Route::post('/import', [IndicatorPresetController::class, 'import'])
            ->name('import')
            ->middleware('permission:import-indicator');
        // Duplicate to year
        Route::post('/duplicate', [IndicatorPresetController::class, 'duplicate'])
            ->name('duplicate')
            ->middleware('permission:import-indicator');
        // Gracefully handle accidental GET to /indicator/import by redirecting
        Route::get('/import', function () {
            return redirect()->route('indicator.index');
        })->name('import.get');
        // Bulk export presets (accepts GET with ids[])
        Route::get('/export-bulk', [IndicatorPresetController::class, 'exportBulk'])
            ->name('export.bulk')
            ->middleware('permission:export-indicator');
    });

    // ===== USER MANAGEMENT ROUTES =====
    Route::prefix('users')->name('users.')->middleware('permission:view-users')->group(function () {
        // View
        Route::get('/', [UserController::class, 'index'])->name('index');

        // Create
        Route::get('/create', [UserController::class, 'create'])
            ->name('create')
            ->middleware('permission:create-users');
        Route::post('/', [UserController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-users');

        // Edit & Update
        Route::get('/{id}/edit', [UserController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit-users');
        Route::put('/{id}', [UserController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-users');

        // Delete
        Route::delete('/{id}', [UserController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-users');
    });

    // ===== DEPARTMENT ROUTES =====
    Route::prefix('departments')->name('departments.')->middleware('permission:view-departments')->group(function () {
        // View
        Route::get('/', [DepartmentController::class, 'index'])->name('index');

        // Create
        Route::post('/store', [DepartmentController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-departments');
        // Redirect accidental GET access to /departments/store back to index
        // Route::get('/store', function () {
        //     return redirect()->route('departments.index');
        // })->name('store.get');

        // Update
        Route::put('/{id}', [DepartmentController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-departments');

        // Delete
        Route::delete('/{id}', [DepartmentController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-departments');
    });

    // ===== AFFILIATION ROUTES =====
    Route::prefix('affiliations')->name('affiliations.')->middleware('permission:view-departments')->group(function () {
        // View
        Route::get('/', [AffiliationsController::class, 'index'])->name('index');

        // Create
        Route::post('/store', [AffiliationsController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-departments');

        // Update
        Route::put('/{id}', [AffiliationsController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-departments');

        // Delete
        Route::delete('/{id}', [AffiliationsController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-departments');
    });

    // ===== CATEGORY ROUTES =====
    Route::prefix('categories')->name('categories.')->middleware('permission:view-categories')->group(function () {
        // View
        Route::get('/', [CategorieController::class, 'index'])->name('index');

        // Create
        Route::post('/store', [CategorieController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-categories');
        // Redirect accidental GET access to /categories/store back to index
        // Route::get('/store', function () {
        //     return redirect()->route('categories.index');
        // })->name('store.get');

        // Update
        Route::put('/{id}', [CategorieController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-categories');

        // Delete
        Route::delete('/{id}', [CategorieController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-categories');
    });

    // ===== STANDARD ROUTES =====
    Route::prefix('standards')->name('standards.')->middleware('permission:view-standards')->group(function () {
        // View
        Route::get('/', [StandardController::class, 'index'])->name('index');

        // Create
        Route::post('/store', [StandardController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-standards');

        // Update
        Route::put('/{id}', [StandardController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-standards');

        // Delete
        Route::delete('/{id}', [StandardController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-standards');
    });

    // ===== SETTINGS ROUTES =====
    Route::prefix('settings')->name('settings.')->middleware('permission:view-settings')->group(function () {
        // View
        Route::get('/', [SettingController::class, 'index'])->name('index');

        // Create
        Route::post('/store', [SettingController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-settings');
        // Send notifications immediately using current form values
        Route::post('/send-now', [SettingNotifyController::class, 'sendNow'])
            ->name('sendNow')
            ->middleware('permission:edit-settings');

        // Update
        Route::put('/{id}', [SettingController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-settings');
    });

    // ===== KKU API TEST ROUTES (Auth required) =====
    Route::prefix('kku')->name('kku.')->group(function () {
        // Get or refresh token
        // GET /kku/token?refresh=1
        Route::get('/token', [KKUApiController::class, 'getKKUToken'])
            ->name('token');

        // Send a test email via KKU API
        // POST JSON/Form: from, fromName, to, subject, message, [cc], [bcc]
        Route::post('/mail-test', [KKUApiController::class, 'sendTestMail'])
            ->name('mail.test');
    });

    // ===== EVIDENCE ROUTES =====
    Route::prefix('evidences')->name('evidences.')->middleware('permission:view-evidence')->group(function () {
        // View
        Route::get('/', [EvidenceController::class, 'index'])->name('index');
        Route::get('criteria/{criteriaId}/evidences', [EvidenceController::class, 'getByCriteria']);
        // Show (JSON for AJAX detail fetching)
        Route::get('/{id}', [EvidenceController::class, 'show'])
            ->name('show')
            ->whereNumber('id');

        // Create
        Route::get('/create/{criteria}', [EvidenceController::class, 'create'])
            ->name('create')
            ->middleware('permission:create-evidence');
        Route::post('/store', [EvidenceController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-evidence');

        // Update
        Route::put('/{id}', [EvidenceController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-evidence');
        Route::patch('/{id}/toggle-status', [EvidenceController::class, 'toggleStatus'])
            ->middleware('permission:edit-evidence');

        // Delete
        Route::delete('/{id}', [EvidenceController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-evidence');

        // Download
        Route::get('/{id}/download', [EvidenceController::class, 'download'])
            ->name('download')
            ->whereNumber('id')
            ->middleware('permission:download-evidence');
        // Preview
        Route::get('/{id}/preview', [EvidenceController::class, 'preview'])
            ->name('preview')
            ->whereNumber('id')
            ->middleware('permission:view-evidence');
    });
    // ===== SAR REPORT ROUTES =====
    Route::prefix('sar_reports')->name('sar_reports.')->middleware('permission:view-sar_report')->group(function () {
        // View
        Route::get('/', [SarReportController::class, 'index'])->name('index');
        Route::get('/{id}/show', [SarReportController::class, 'show'])->name('show');
        // Export (type-specific routes with implicit model binding)
        Route::get('/{report}/export/docx', [SarReportController::class, 'export'])
            ->name('export.docx')
            ->defaults('type', 'docx')
            ->middleware('permission:export-sar_report');
        // Excel export should follow the same naming/prefix pattern as others
        Route::get('/{report}/export/xlsx', [SarReportController::class, 'export'])
            ->name('export.xlsx')
            ->defaults('type', 'excel')
            ->middleware('permission:export-sar_report');
        Route::get('/{report}/export/pdf', [SarReportController::class, 'export'])
            ->name('export.pdf')
            ->defaults('type', 'pdf')
            ->middleware('permission:export-sar_report');
        // Create
        Route::get('/create', [SarReportController::class, 'create'])
            ->name('create')
            ->middleware('permission:create-sar_report');
        Route::post('/store', [SarReportController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-sar_report');
        // Edit & Update
        Route::get('/{id}/edit', [SarReportController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit-sar_report');
        Route::put('/{id}', [SarReportController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-sar_report');
        // Delete
        Route::delete('/{id}', [SarReportController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-sar_report');
        // Update Report Content for a Criteria
        Route::post('/criterias/{id}/report', [SarReportController::class, 'updateReport'])
            ->name('criterias.updateReport')
            ->middleware('permission:edit-sar_report');
    });

    // ===== DASHBOARD KPI ROUTES =====
    Route::prefix('dashboardkpi')->name('dashboardkpi.')->group(function () {

        Route::get('/', [DashboardKpiUserController::class, 'index'])
            ->name('index')
            ->middleware('permission:view-dashboard-kpi-user');

        Route::prefix('/user')->name('user.')->middleware([
            'permission:view-dashboard-kpi-user',
            'permission:show-dashboard-kpi-user',
        ])->group(function () {
            Route::get('/kpi/{id}', [DashboardKpiUserController::class, 'show'])->name('show');
            Route::put('/kpi/{id}/save-variables', [DashboardKpiUserController::class, 'saveVariables'])->name('saveVariables');
            Route::post('/kpi/{id}/request-correction', [DashboardKpiUserController::class, 'requestCorrection'])->name('requestCorrection');
        });

        Route::prefix('/admin')->name('admin.')->middleware('permission:view-indicator-dashboard')->group(function () {
            Route::get('/kpi/{id}', [DashboardKpiAdminController::class, 'show'])->name('show');
            Route::put('/kpi/{id}/save-variables', [DashboardKpiAdminController::class, 'saveVariables'])
                ->name('saveVariables')
                ->middleware('permission:edit-indicator');
            // Route::put('/kpi/{id}/update-status', [DashboardKpiAdminController::class, 'updateStatus'])->name('updateStatus');
        });
    });

    // ===== REPORTS ROUTES =====
    Route::prefix('reports')->name('reports.')->middleware('permission:view-dashboard')->group(function () {
        Route::get('/progress', [ProgressReportController::class, 'index'])->name('progress');
    });

    Route::post('/indicators/{id}/notify', [NotifycationController::class, 'notifyCollectors'])
        ->name('notify')
        ->middleware('permission:edit-indicator');
});
// Route::resource('sar_reports', SarReportController::class);
// routes/web.php

// Route::post('/criterias/{id}/report', [SarReportController::class, 'updateReport'])
//     ->name('criterias.updateReport');
// Route::get('/sar_reports/{id}/edit', [SarReportController::class, 'edit'])
//     ->name('sar_reports.edit');
// Route::put('/sar_reports/{id}', [SarReportController::class, 'update'])
//     ->name('sar_reports.update');
// Route::get('/sar_reports', [SarReportController::class, 'index'])
//     ->name('sar_reports.index');
// Route::get('/sar_reports/create', [SarReportController::class, 'create'])
//     ->name('sar_reports.create');
// Route::post('/sar_reports', [SarReportController::class, 'store'])
//     ->name('sar_reports.store');
// Route::get('/sar_reports/{report}/export/docx', [SarReportController::class, 'export'])
//     ->name('sar_reports.export.docx')
//     ->defaults('type', 'docx');
// Route::get('/sar_reports/{report}/export/xlsx', [SarReportController::class, 'export'])
//     ->name('sar_reports.export.xlsx')
//     ->defaults('type', 'excel');
// Route::get('/sar_reports/{report}/export/pdf', [SarReportController::class, 'export'])
//     ->name('sar_reports.export.pdf')
//     ->defaults('type', 'pdf');
