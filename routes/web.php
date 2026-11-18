<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FrontController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\ServicesController;

// Fallback Route to serve storage files via PHP (Fix for symlink issues)
Route::get('/storage-file/{path}', function ($path) {
    // Clean up path and ensure it's relative to public storage
    $path = str_replace(['../', './'], '', $path);
    $fullPath = storage_path("app/public/" . str_replace('/', DIRECTORY_SEPARATOR, $path));
    
    if (!file_exists($fullPath) || is_dir($fullPath)) {
        abort(404);
    }
    
    // Determine mime type
    $mime = mime_content_type($fullPath);
    return response()->file($fullPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->where('path', '.*');

// Front Routes
Route::prefix('products')->name('products.')->group(function() {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
});

Route::prefix('services')->name('services.')->group(function() {
    Route::get('/', [ServicesController::class, 'index'])->name('index');
    Route::get('/{service}', [ServicesController::class, 'show'])->name('show');
});

// Web Installer
Route::get('/install', [\App\Http\Controllers\System\InstallerController::class, 'show'])->name('system.install');
Route::post('/install', [\App\Http\Controllers\System\InstallerController::class, 'run'])->name('system.install.run');

// Production Deployment Helpers (No SSH Access)
Route::get('/system/migrate/louis-deploy-2026', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "Migration successful: " . \Illuminate\Support\Facades\Artisan::output();
    } catch (\Exception $e) {
        return "Migration failed: " . $e->getMessage();
    }
});

Route::get('/system/cache-clear/louis-deploy-2026', function () {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    return "Cache cleared successfully.";
});













// Front Routes
Route::get('/', [FrontController::class, 'home'])->name('home');
Route::get('/about-us', function() {
    return app(FrontController::class)->page('about');
})->name('about');
Route::get('page/{slug}', [FrontController::class, 'page'])->name('page');
Route::get('contact', function() { return view('frontend.contact'); })->name('contact');
Route::post('contact', [FrontController::class, 'contact'])->name('contact.submit');
// Quote system not implemented yet - redirect to contact page
Route::get('/quote', function() { return redirect()->route('contact'); })->name('quote.index');


// Manager Dashboard
Route::middleware(['auth', 'role:manager'])->prefix('manager')->as('manager.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('dashboard');
});

Route::get('/dashboard', function () {
    if(auth()->user()->hasRole('super-admin')) {
        return redirect()->route('admin.dashboard');
    }
    if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('pro')) {
        return redirect()->route('admin.dashboard');
    }
    if(auth()->user()->hasRole('manager')) {
        return redirect()->route('manager.dashboard');
    }
    if(auth()->user()->hasRole('engineer')) {
        return redirect()->route('engineer.dashboard');
    }
    if(auth()->user()->hasRole('accountant')) {
        return redirect()->route('accounts.dashboard');
    }
    if(auth()->user()->hasRole('storekeeper')) {
        return redirect()->route('storekeeper.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications/mark-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Chat Routes
    Route::get('/chat/users', [ChatController::class, 'users'])->name('chat.users');
    Route::get('/chat/messages/{user}', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
});

require __DIR__.'/auth.php';

// SHARED ADMIN ROUTES (Accessible by Engineers)
Route::middleware(['auth', 'role:super-admin|admin|manager|pro|engineer|accountant|storekeeper'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('customers/export', [\App\Http\Controllers\Admin\CustomerController::class, 'export'])->name('customers.export');
    Route::get('customers/template', [\App\Http\Controllers\Admin\CustomerController::class, 'downloadTemplate'])->name('customers.template');
    Route::post('customers/import', [\App\Http\Controllers\Admin\CustomerController::class, 'import'])->name('customers.import');

    Route::post('customers/{customer}/notes', [\App\Http\Controllers\Admin\CustomerController::class, 'storeNote'])->name('customers.notes.store');
    Route::post('customers/{customer}/contacts', [\App\Http\Controllers\Admin\CustomerController::class, 'storeContact'])->name('customers.contacts.store');
    Route::post('customers/{customer}/documents', [\App\Http\Controllers\Admin\CustomerController::class, 'storeDocument'])->name('customers.documents.store');
    
    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);
    
    // Vessel Actions
    Route::get('vessels/export', [\App\Http\Controllers\Admin\VesselController::class, 'export'])->name('vessels.export');
    Route::get('vessels/template', [\App\Http\Controllers\Admin\VesselController::class, 'downloadTemplate'])->name('vessels.template');
    Route::post('vessels/import', [\App\Http\Controllers\Admin\VesselController::class, 'import'])->name('vessels.import');
    Route::resource('vessels', \App\Http\Controllers\Admin\VesselController::class);

    // Service Types
    Route::get('service-types/export', [\App\Http\Controllers\Admin\ServiceTypeController::class, 'export'])->name('service-types.export');
    Route::get('service-types/template', [\App\Http\Controllers\Admin\ServiceTypeController::class, 'downloadTemplate'])->name('service-types.template');
    Route::post('service-types/import', [\App\Http\Controllers\Admin\ServiceTypeController::class, 'import'])->name('service-types.import');
    Route::resource('service-types', \App\Http\Controllers\Admin\ServiceTypeController::class);
    
    // Equipment Categories
    Route::prefix('equipment')->as('equipment.')->group(function() {
        Route::get('categories/export', [\App\Http\Controllers\Admin\EquipmentCategoryController::class, 'export'])->name('categories.export');
        Route::get('categories/template', [\App\Http\Controllers\Admin\EquipmentCategoryController::class, 'downloadTemplate'])->name('categories.template');
        Route::post('categories/import', [\App\Http\Controllers\Admin\EquipmentCategoryController::class, 'import'])->name('categories.import');
        
        Route::resource('categories', \App\Http\Controllers\Admin\EquipmentCategoryController::class);
        Route::post('categories/{category}/toggle', [\App\Http\Controllers\Admin\EquipmentCategoryController::class, 'toggle'])->name('categories.toggle');
        
        // Items
        Route::get('items/export', [\App\Http\Controllers\Admin\EquipmentItemController::class, 'export'])->name('items.export');
        Route::get('items/template', [\App\Http\Controllers\Admin\EquipmentItemController::class, 'downloadTemplate'])->name('items.template');
        Route::post('items/import', [\App\Http\Controllers\Admin\EquipmentItemController::class, 'import'])->name('items.import');
        Route::get('items/by-category/{categoryId}', [\App\Http\Controllers\Admin\EquipmentItemController::class, 'getByCategory'])->name('items.by-category');
        Route::resource('items', \App\Http\Controllers\Admin\EquipmentItemController::class);
    });

    // Certificate Templates
    Route::get('certificate-templates/export', [\App\Http\Controllers\Admin\CertificateTemplateController::class, 'export'])->name('certificate-templates.export');
    Route::get('certificate-templates/template', [\App\Http\Controllers\Admin\CertificateTemplateController::class, 'downloadTemplate'])->name('certificate-templates.template');
    Route::post('certificate-templates/import', [\App\Http\Controllers\Admin\CertificateTemplateController::class, 'import'])->name('certificate-templates.import');

    Route::resource('certificate-templates', \App\Http\Controllers\Admin\CertificateTemplateController::class);
    Route::post('certificate-templates/{certificateTemplate}/duplicate', [\App\Http\Controllers\Admin\CertificateTemplateController::class, 'duplicate'])->name('certificate-templates.duplicate');
    Route::post('certificate-templates/{certificateTemplate}/toggle', [\App\Http\Controllers\Admin\CertificateTemplateController::class, 'toggle'])->name('certificate-templates.toggle');
    Route::post('certificate-templates/{certificateTemplate}/default', [\App\Http\Controllers\Admin\CertificateTemplateController::class, 'setDefault'])->name('certificate-templates.set-default');
    Route::get('certificate-templates/{certificateTemplate}/preview', [\App\Http\Controllers\Admin\CertificateTemplateController::class, 'preview'])->name('certificate-templates.preview');

    // Template Sections
    Route::post('certificate-templates/{certificateTemplate}/sections', [\App\Http\Controllers\Admin\TemplateSectionController::class, 'store'])->name('template-sections.store');
    Route::put('template-sections/{section}', [\App\Http\Controllers\Admin\TemplateSectionController::class, 'update'])->name('template-sections.update');
    Route::delete('template-sections/{section}', [\App\Http\Controllers\Admin\TemplateSectionController::class, 'destroy'])->name('template-sections.destroy');
    Route::post('template-sections/reorder', [\App\Http\Controllers\Admin\TemplateSectionController::class, 'reorder'])->name('template-sections.reorder');

    // Template Fields
    Route::post('template-sections/{section}/fields', [\App\Http\Controllers\Admin\TemplateFieldController::class, 'store'])->name('template-fields.store');
    Route::put('template-fields/{field}', [\App\Http\Controllers\Admin\TemplateFieldController::class, 'update'])->name('template-fields.update');
    Route::delete('template-fields/{field}', [\App\Http\Controllers\Admin\TemplateFieldController::class, 'destroy'])->name('template-fields.destroy');
    Route::post('template-fields/reorder', [\App\Http\Controllers\Admin\TemplateFieldController::class, 'reorder'])->name('template-fields.reorder');

    // Certificates (Shared)
    Route::resource('certificates', \App\Http\Controllers\Admin\CertificateController::class);
    Route::get('certificates/{certificate}/pdf', [\App\Http\Controllers\Admin\CertificateController::class, 'pdf'])->name('certificates.pdf');
    Route::post('certificates/{certificate}/verify-assignment', [\App\Http\Controllers\Admin\CertificateController::class, 'verify'])->name('certificates.verify');
    Route::post('certificates/{certificate}/submit', [\App\Http\Controllers\Admin\CertificateController::class, 'submit'])->name('certificates.submit');
    Route::post('certificates/{certificate}/approve', [\App\Http\Controllers\Admin\CertificateController::class, 'approve'])->name('certificates.approve');
    Route::post('certificates/{certificate}/reject', [\App\Http\Controllers\Admin\CertificateController::class, 'reject'])->name('certificates.reject');
    Route::post('certificates/{certificate}/renew', [\App\Http\Controllers\Admin\CertificateController::class, 'renew'])->name('certificates.renew');
    Route::get('certificates/templates/{categoryId}', [\App\Http\Controllers\Admin\CertificateController::class, 'getCertificateFields'])->name('certificates.get-fields');
    Route::get('certificates/templates/{categoryId}', [\App\Http\Controllers\Admin\CertificateController::class, 'getCertificateFields'])->name('certificates.get-fields');

});

// Shared Service Report Access (Read Only)
// Explicitly defined to match original naming 'engineer.reports.show' but accessible by multiple roles
Route::middleware(['auth', 'role:admin|manager|pro|engineer|accountant'])->prefix('engineer')->as('engineer.')->group(function () {
    Route::get('reports/{report}', [\App\Http\Controllers\Engineer\ServiceReportController::class, 'show'])->name('reports.show');
    Route::get('reports/{report}/pdf', [\App\Http\Controllers\Engineer\ServiceReportController::class, 'download'])->name('reports.download');
});

// Restricted Ledger Access
Route::middleware(['auth', 'role:super-admin|admin|manager|accountant'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('customers/{customer}/ledger', [\App\Http\Controllers\Admin\CustomerController::class, 'ledger'])->name('customers.ledger');
});

// ADMIN ROUTES RECONSTRUCTION
Route::middleware(['auth', 'role:super-admin|admin|manager|pro|accountant|engineer'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Core Modules
    // Existing routes...
    
    // System Maintenance Tools (For Production without SSH)
    Route::get('maintenance', [\App\Http\Controllers\Admin\SystemMaintenanceController::class, 'index'])->name('admin.maintenance.index');
    Route::post('maintenance/seed-accounting', [\App\Http\Controllers\Admin\SystemMaintenanceController::class, 'runAccountingSeeder'])->name('admin.maintenance.seed-accounting');
    Route::post('maintenance/clear-cache', [\App\Http\Controllers\Admin\SystemMaintenanceController::class, 'clearCache'])->name('admin.maintenance.clear-cache');
    
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    
    // Staff Management (Advance/Ledger)
    Route::post('staff/{user}/ledger', [\App\Http\Controllers\Admin\StaffController::class, 'storeData'])->name('staff.ledger.store');
    Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class);
    
    // Activity Logs
    Route::get('activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{id}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
        
    // Dispatch Notes
    Route::get('dispatch/{dispatch_note}/pdf', [\App\Http\Controllers\Admin\DispatchNoteController::class, 'pdf'])->name('dispatch.pdf');
    Route::resource('dispatch', \App\Http\Controllers\Admin\DispatchNoteController::class)->parameters(['dispatch' => 'dispatch_note']);
    Route::post('jobs/{job}/documents', [\App\Http\Controllers\Admin\ServiceJobController::class, 'storeDocument'])->name('jobs.documents.store');
    Route::get('jobs/{job}/pdf', [\App\Http\Controllers\Admin\ServiceJobController::class, 'pdf'])->name('jobs.pdf');
    
    // Job Items
    Route::post('jobs/{job}/items', [\App\Http\Controllers\Admin\ServiceJobItemController::class, 'store'])->name('jobs.items.store');
    Route::delete('jobs/{job}/items/{item}', [\App\Http\Controllers\Admin\ServiceJobItemController::class, 'destroy'])->name('jobs.items.destroy');
    
    Route::resource('jobs', \App\Http\Controllers\Admin\ServiceJobController::class);
    // Inventory - mapped to admin.inventory.products.*
    Route::prefix('inventory')->as('inventory.')->group(function() {
        Route::resource('products', \App\Http\Controllers\Admin\Inventory\ProductController::class);
    });
    // Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class);
    // Route::resource('purchase-orders', \App\Http\Controllers\Admin\PurchaseOrderController::class);
    
    // Finance & Payroll
    Route::prefix('finance')->as('finance.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Admin\Reports\ReportController::class, 'finance'])->name('index'); // Overview
        Route::resource('incomes', \App\Http\Controllers\Admin\Accounts\IncomeController::class);
        Route::resource('expenses', \App\Http\Controllers\Admin\Accounts\ExpenseController::class);
        Route::resource('categories', \App\Http\Controllers\Admin\Accounts\FinanceCategoryController::class);
        
        // Payroll
        Route::prefix('payroll')->as('payroll.')->group(function() {
            Route::get('employees', [\App\Http\Controllers\Admin\Accounts\EmployeeDetailController::class, 'index'])->name('employees.index');
            Route::put('employees/{user}', [\App\Http\Controllers\Admin\Accounts\EmployeeDetailController::class, 'update'])->name('employees.update');
            
            Route::get('/', [\App\Http\Controllers\Admin\Accounts\PayrollController::class, 'index'])->name('index');
            Route::post('generate', [\App\Http\Controllers\Admin\Accounts\PayrollController::class, 'generate'])->name('generate');
            Route::post('store', [\App\Http\Controllers\Admin\Accounts\PayrollController::class, 'store'])->name('store');
            // Overtime
            Route::resource('overtime', \App\Http\Controllers\Admin\Accounts\OvertimeController::class);
            Route::post('deduction', [\App\Http\Controllers\Admin\Accounts\PayrollController::class, 'storeDeduction'])->name('deduction.store');

            Route::get('{payroll}', [\App\Http\Controllers\Admin\Accounts\PayrollController::class, 'show'])->name('show');
            Route::post('{payroll}/finalize', [\App\Http\Controllers\Admin\Accounts\PayrollController::class, 'finalize'])->name('finalize');
            Route::get('{payroll}/export', [\App\Http\Controllers\Admin\Accounts\PayrollController::class, 'export'])->name('export');
        });
    });

    // Reports
    Route::get('reports/operations', [\App\Http\Controllers\Admin\Reports\ReportController::class, 'operations'])->name('reports.operations');
    Route::get('reports/tax', [\App\Http\Controllers\Admin\Reports\TaxReportController::class, 'index'])->name('reports.tax');
    Route::resource('reports', \App\Http\Controllers\Admin\Reports\ReportController::class);

    // Backups
    Route::prefix('security')->as('security.')->group(function() {
        Route::resource('backups', \App\Http\Controllers\Admin\BackupController::class);
    });

    // CMS
    Route::prefix('cms')->as('cms.')->group(function() {
        Route::get('dashboard', [\App\Http\Controllers\Admin\CmsController::class, 'dashboard'])->name('dashboard');
        
        // Services / Offerings
        Route::post('services/sync-erp', [\App\Http\Controllers\Admin\Cms\ServiceController::class, 'syncFromErp'])->name('services.sync-erp');
        Route::resource('services', \App\Http\Controllers\Admin\Cms\ServiceController::class);
        
        // Products (Website Products)
        Route::resource('products', \App\Http\Controllers\Admin\Cms\WebsiteProductController::class);
        
        // Pages
        Route::resource('pages', \App\Http\Controllers\Admin\Cms\PageController::class);
        
        // Contact Submissions
        Route::get('contact-submissions', [\App\Http\Controllers\Admin\Cms\ContactSubmissionController::class, 'index'])->name('contact-submissions.index');
        Route::get('contact-submissions/{submission}', [\App\Http\Controllers\Admin\Cms\ContactSubmissionController::class, 'show'])->name('contact-submissions.show');
        Route::patch('contact-submissions/{submission}/status', [\App\Http\Controllers\Admin\Cms\ContactSubmissionController::class, 'updateStatus'])->name('contact-submissions.status');
        
        // Partners
        // Partners
        Route::resource('partners', \App\Http\Controllers\Admin\Cms\PartnerController::class);

        // Pages Management
        Route::resource('pages', \App\Http\Controllers\Admin\Cms\CmsPageController::class);
        Route::resource('pages.sections', \App\Http\Controllers\Admin\Cms\PageSectionController::class);
        
        // Approvals / Certifications
        Route::resource('certifications', \App\Http\Controllers\Admin\Cms\CertificationController::class);
        
        // Landing Page Editor
        Route::prefix('landing-page')->as('landing-page.')->group(function() {
            Route::get('/', function() {
                return redirect()->route('admin.cms.landing-page.hero-slides.index');
            })->name('index');
            
            // Hero Slides
            Route::resource('hero-slides', \App\Http\Controllers\Admin\Cms\HeroSlideController::class);
            Route::post('hero-slides/reorder', [\App\Http\Controllers\Admin\Cms\HeroSlideController::class, 'reorder'])->name('hero-slides.reorder');
            
            // Features
            Route::resource('features', \App\Http\Controllers\Admin\Cms\FeatureController::class);
            Route::post('features/reorder', [\App\Http\Controllers\Admin\Cms\FeatureController::class, 'reorder'])->name('features.reorder');
            
            // Testimonials
            Route::resource('testimonials', \App\Http\Controllers\Admin\Cms\TestimonialController::class);
            Route::post('testimonials/reorder', [\App\Http\Controllers\Admin\Cms\TestimonialController::class, 'reorder'])->name('testimonials.reorder');
            
            // Statistics
            Route::resource('stats', \App\Http\Controllers\Admin\Cms\StatController::class);
            Route::post('stats/reorder', [\App\Http\Controllers\Admin\Cms\StatController::class, 'reorder'])->name('stats.reorder');
            
            // CTA Sections
            Route::resource('cta-sections', \App\Http\Controllers\Admin\Cms\CtaSectionController::class);
        });
    });
});

// ENGINEER ROUTES RECONSTRUCTION
Route::middleware(['auth', 'role:engineer'])->prefix('engineer')->as('engineer.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Engineer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('jobs/{job}/pdf', [\App\Http\Controllers\Engineer\JobController::class, 'pdf'])->name('jobs.pdf');
    Route::resource('jobs', \App\Http\Controllers\Engineer\JobController::class);
    Route::get('jobs/{job}/delivery-note', [\App\Http\Controllers\Engineer\JobController::class, 'deliveryNote'])->name('jobs.delivery-note');
    Route::get('jobs/{job}/assignments/create', [\App\Http\Controllers\Engineer\AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('jobs/{job}/assignments', [\App\Http\Controllers\Engineer\AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('jobs/{job}/material-request', [\App\Http\Controllers\Engineer\MaterialRequestController::class, 'createForJob'])->name('jobs.material-request');
    Route::resource('material-requests', \App\Http\Controllers\Engineer\MaterialRequestController::class);
    
    // Service Reports
    Route::get('jobs/{job}/report/create', [\App\Http\Controllers\Engineer\ServiceReportController::class, 'create'])->name('jobs.report.create');
    Route::post('jobs/{job}/report', [\App\Http\Controllers\Engineer\ServiceReportController::class, 'store'])->name('jobs.report.store');
    Route::resource('reports', \App\Http\Controllers\Engineer\ServiceReportController::class)->only(['edit', 'update']);

    // Equipment
    Route::post('equipment/categories/quick-store', [\App\Http\Controllers\Engineer\EquipmentController::class, 'storeType'])->name('equipment.categories.quick-store');
    Route::post('equipment/{equipment}/upload-certificate', [\App\Http\Controllers\Engineer\EquipmentController::class, 'uploadCertificate'])->name('equipment.upload-certificate');
    // Equipment Actions
    Route::get('equipment/export', [\App\Http\Controllers\Engineer\EquipmentController::class, 'export'])->name('equipment.export');
    Route::get('equipment/template', [\App\Http\Controllers\Engineer\EquipmentController::class, 'downloadTemplate'])->name('equipment.template');
    Route::post('equipment/import', [\App\Http\Controllers\Engineer\EquipmentController::class, 'import'])->name('equipment.import');
    Route::get('equipment/pdf', [\App\Http\Controllers\Engineer\EquipmentController::class, 'downloadPdf'])->name('equipment.pdf');
    
    Route::resource('equipment', \App\Http\Controllers\Engineer\EquipmentController::class);

    // Tools Requests
    Route::resource('tools', \App\Http\Controllers\Engineer\ToolRequestController::class)->only(['index', 'create', 'store']);

    // Certificates
    Route::resource('certificates', \App\Http\Controllers\Engineer\CertificateController::class);
    Route::get('certificates/{certificate}/verify', [\App\Http\Controllers\Engineer\CertificateController::class, 'verify'])->name('certificates.verify');
    Route::post('certificates/{certificate}/approve', [\App\Http\Controllers\Engineer\CertificateController::class, 'approve'])->name('certificates.approve');
    Route::post('certificates/{certificate}/download', [\App\Http\Controllers\Engineer\CertificateController::class, 'download'])->name('certificates.download');

    Route::resource('service-types', \App\Http\Controllers\Admin\ServiceTypeController::class);
});

// ACCOUNTS ROUTES RECONSTRUCTION
Route::middleware(['auth', 'role:accountant|admin|manager'])->prefix('accounts')->as('accounts.')->group(function () {
    Route::get('test-routes', function() { return 'Accounts Route Group is Working'; });
    Route::get('dashboard', [\App\Http\Controllers\Accounts\DashboardController::class, 'index'])->name('dashboard');

    // Financial Reports
    Route::get('reports/general-ledger', [\App\Http\Controllers\Accounts\ReportingController::class, 'generalLedger'])->name('reports.general_ledger');
    Route::get('reports/general-ledger/pdf', [\App\Http\Controllers\Accounts\ReportingController::class, 'generalLedgerPdf'])->name('reports.ledger.pdf');
    Route::get('reports/general-ledger/print', [\App\Http\Controllers\Accounts\ReportingController::class, 'generalLedgerPrint'])->name('reports.ledger.print');
    Route::get('reports/trial-balance', [\App\Http\Controllers\Accounts\ReportingController::class, 'trialBalance'])->name('reports.trial_balance');
    Route::get('reports/trial-balance/pdf', [\App\Http\Controllers\Accounts\ReportingController::class, 'trialBalancePdf'])->name('reports.trial_balance.pdf');
    Route::get('reports/balance-sheet', [\App\Http\Controllers\Accounts\ReportingController::class, 'balanceSheet'])->name('reports.balance_sheet');
    Route::get('reports/balance-sheet/pdf', [\App\Http\Controllers\Accounts\ReportingController::class, 'balanceSheetPdf'])->name('reports.balance_sheet.pdf');
    Route::get('reports/profit-loss', [\App\Http\Controllers\Accounts\ReportingController::class, 'profitLoss'])->name('reports.profit_loss');
    Route::get('reports/profit-loss/pdf', [\App\Http\Controllers\Accounts\ReportingController::class, 'profitLossPdf'])->name('reports.profit_loss.pdf');
    Route::get('reports/cash-flow', [\App\Http\Controllers\Accounts\ReportingController::class, 'cashFlow'])->name('reports.cash_flow');

    Route::get('reports/cash-flow/pdf', [\App\Http\Controllers\Accounts\ReportingController::class, 'cashFlowPdf'])->name('reports.cash_flow.pdf');
    Route::get('reports/ledger/{account}', [\App\Http\Controllers\Accounts\ReportingController::class, 'ledger'])->name('reports.ledger');

    // Bank Reconciliation
    Route::get('reconciliation', [\App\Http\Controllers\Admin\Accounts\BankReconciliationController::class, 'index'])->name('reconciliation.index');
    Route::get('reconciliation/create', [\App\Http\Controllers\Admin\Accounts\BankReconciliationController::class, 'create'])->name('reconciliation.create');
    Route::post('reconciliation', [\App\Http\Controllers\Admin\Accounts\BankReconciliationController::class, 'store'])->name('reconciliation.store');
    Route::get('reconciliation/{statement}', [\App\Http\Controllers\Admin\Accounts\BankReconciliationController::class, 'show'])->name('reconciliation.show');
    Route::post('reconciliation/{statement}/line', [\App\Http\Controllers\Admin\Accounts\BankReconciliationController::class, 'storeLine'])->name('reconciliation.store-line');
    Route::post('reconciliation/{statement}/match/{line}', [\App\Http\Controllers\Admin\Accounts\BankReconciliationController::class, 'match'])->name('reconciliation.match');
    Route::post('reconciliation/{statement}/auto-match', [\App\Http\Controllers\Admin\Accounts\BankReconciliationController::class, 'autoMatch'])->name('reconciliation.auto-match');
    
    // Journal Entries (New Module)
    Route::resource('journals', \App\Http\Controllers\Accounts\JournalEntryController::class);
    Route::post('journals/{journal_entry}/reverse', [\App\Http\Controllers\Accounts\JournalEntryController::class, 'reverse'])->name('journals.reverse');
    Route::post('journals/{journal_entry}/post', [\App\Http\Controllers\Accounts\JournalEntryController::class, 'post'])->name('journals.post');

    // Chart of Accounts
    Route::resource('coa', \App\Http\Controllers\Accounts\ChartOfAccountController::class);

    // Accounts Receivable
    Route::get('ar', [\App\Http\Controllers\Accounts\AccountsReceivableController::class, 'index'])->name('ar.index');

    // Cash & Bank Book
    Route::resource('cash-bank-book', \App\Http\Controllers\Accounts\CashBankBookController::class)->only(['index', 'show']);

    // Accounts Payable
    Route::resource('vendor-bills', \App\Http\Controllers\Accounts\VendorBillController::class);
    Route::get('vendor-bills/create-from-po/{purchaseOrder}', [\App\Http\Controllers\Accounts\VendorBillController::class, 'createFromOrder'])->name('vendor-bills.create-from-po');
    Route::post('vendor-bills/{vendor_bill}/post', [\App\Http\Controllers\Accounts\VendorBillController::class, 'post'])->name('vendor-bills.post');
    Route::resource('vendor-payments', \App\Http\Controllers\Accounts\VendorPaymentController::class);
    Route::get('vendor-payments/{vendor_payment}/pdf', [\App\Http\Controllers\Accounts\VendorPaymentController::class, 'pdf'])->name('vendor-payments.pdf');
    Route::get('vendor-payments/{vendor_payment}/print', [\App\Http\Controllers\Accounts\VendorPaymentController::class, 'print'])->name('vendor-payments.print');
    Route::post('vendor-payments/{vendor_payment}/post', [\App\Http\Controllers\Accounts\VendorPaymentController::class, 'post'])->name('vendor-payments.post');
    
    // Fixed Assets
    Route::resource('assets', \App\Http\Controllers\Assets\FixedAssetController::class);
    Route::get('depreciation', [\App\Http\Controllers\Assets\AssetDepreciationController::class, 'index'])->name('assets.depreciation.index');
    Route::post('depreciation/post', [\App\Http\Controllers\Assets\AssetDepreciationController::class, 'post'])->name('assets.depreciation.post');

    // Budgeting
    Route::resource('budgets', \App\Http\Controllers\Accounts\BudgetController::class);
    Route::post('budgets/{budget}/items', [\App\Http\Controllers\Accounts\BudgetController::class, 'updateItems'])->name('budgets.items.update');
    Route::resource('forecasts', \App\Http\Controllers\Accounts\ForecastController::class);

    // Cost Centers & Projects
    Route::resource('cost-centers', \App\Http\Controllers\Accounts\CostCenterController::class);
    Route::resource('projects', \App\Http\Controllers\Accounts\ProjectController::class);
    
    // Inventory Valuation
    Route::get('inventory/valuation', [\App\Http\Controllers\Accounts\InventoryValuationController::class, 'valuation'])->name('inventory.valuation');
    Route::get('inventory/ledger', [\App\Http\Controllers\Accounts\InventoryValuationController::class, 'ledger'])->name('inventory.ledger');
    
    // Landed Cost
    Route::resource('landed-costs', \App\Http\Controllers\Accounts\LandedCostController::class);
    Route::post('landed-costs/{landedCost}/add-line', [\App\Http\Controllers\Accounts\LandedCostController::class, 'addLine'])->name('landed-costs.add-line');
    Route::delete('landed-costs/{landedCost}/lines/{line}', [\App\Http\Controllers\Accounts\LandedCostController::class, 'removeLine'])->name('landed-costs.remove-line');
    Route::post('landed-costs/{landedCost}/post', [\App\Http\Controllers\Accounts\LandedCostController::class, 'post'])->name('landed-costs.post');

    // Accounting Periods
    Route::resource('periods', \App\Http\Controllers\Accounts\AccountingPeriodController::class);
    Route::post('periods/{period}/toggle-lock', [\App\Http\Controllers\Accounts\AccountingPeriodController::class, 'toggleLock'])->name('periods.toggle-lock');

    Route::resource('invoices', \App\Http\Controllers\Accounts\InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [\App\Http\Controllers\Accounts\InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::post('invoices/from-report/{report}', [\App\Http\Controllers\Accounts\InvoiceController::class, 'createFromServiceReport'])->name('invoices.from-report');
    Route::post('invoices/{invoice}/mark-sent', [\App\Http\Controllers\Accounts\InvoiceController::class, 'markSent'])->name('invoices.mark-sent');
    Route::resource('payments', \App\Http\Controllers\Accounts\PaymentController::class);
    Route::get('payments/{payment}/receipt', [\App\Http\Controllers\Accounts\PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::resource('reports', \App\Http\Controllers\Admin\Reports\ReportController::class);
});

// STOREKEEPER ROUTES RECONSTRUCTION
Route::middleware(['auth', 'role:storekeeper|admin|manager'])->prefix('storekeeper')->as('storekeeper.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Storekeeper\DashboardController::class, 'index'])->name('dashboard');
    Route::get('inventory/export', [\App\Http\Controllers\Storekeeper\InventoryItemController::class, 'export'])->name('inventory.export');
    Route::get('inventory/import', [\App\Http\Controllers\Storekeeper\InventoryItemController::class, 'importForm'])->name('inventory.import');
    Route::post('inventory/import', [\App\Http\Controllers\Storekeeper\InventoryItemController::class, 'import'])->name('inventory.import.store');
    Route::get('inventory/pdf', [\App\Http\Controllers\Storekeeper\InventoryItemController::class, 'pdf'])->name('inventory.pdf');
    Route::resource('inventory', \App\Http\Controllers\Storekeeper\InventoryItemController::class);

    Route::get('categories/export', [\App\Http\Controllers\Storekeeper\InventoryCategoryController::class, 'export'])->name('categories.export');
    Route::get('categories/import', [\App\Http\Controllers\Storekeeper\InventoryCategoryController::class, 'importForm'])->name('categories.import');
    Route::post('categories/import', [\App\Http\Controllers\Storekeeper\InventoryCategoryController::class, 'import'])->name('categories.import.store');
    Route::get('categories/pdf', [\App\Http\Controllers\Storekeeper\InventoryCategoryController::class, 'pdf'])->name('categories.pdf');
    Route::resource('categories', \App\Http\Controllers\Storekeeper\InventoryCategoryController::class);
    // Route::resource('suppliers', \App\Http\Controllers\Storekeeper\SupplierController::class); // Moved to Shared
    // Route::resource('purchase-orders', \App\Http\Controllers\Storekeeper\PurchaseOrderController::class); // Moved to Shared
    Route::resource('stock', \App\Http\Controllers\Storekeeper\StockMovementController::class);
    // Add custom routes for stock
    Route::get('stock-maintenance/inward', [\App\Http\Controllers\Storekeeper\StockMovementController::class, 'inward'])->name('stock.inward');
    Route::post('stock-maintenance/inward', [\App\Http\Controllers\Storekeeper\StockMovementController::class, 'storeInward'])->name('stock.store-inward');
    Route::get('stock-maintenance/adjustment', [\App\Http\Controllers\Storekeeper\StockMovementController::class, 'adjustment'])->name('stock.adjustment');
    Route::post('stock-maintenance/adjustment', [\App\Http\Controllers\Storekeeper\StockMovementController::class, 'storeAdjustment'])->name('stock.store-adjustment');

    Route::resource('material-requests', \App\Http\Controllers\Storekeeper\MaterialRequestController::class);
    Route::post('material-requests/{materialRequest}/approve', [\App\Http\Controllers\Storekeeper\MaterialRequestController::class, 'approve'])->name('material-requests.approve');
    Route::post('material-requests/{materialRequest}/reject', [\App\Http\Controllers\Storekeeper\MaterialRequestController::class, 'reject'])->name('material-requests.reject');
    Route::post('material-requests/{materialRequest}/issue', [\App\Http\Controllers\Storekeeper\MaterialRequestController::class, 'issue'])->name('material-requests.issue');

    Route::resource('quotations', \App\Http\Controllers\Storekeeper\QuotationController::class);
    // Custom Quotation Routes
    Route::get('quotations/{quotation}/pdf', [\App\Http\Controllers\Storekeeper\QuotationController::class, 'pdf'])->name('quotations.pdf');
    Route::get('quotations/{quotation}/delivery-note', [\App\Http\Controllers\Storekeeper\QuotationController::class, 'deliveryNote'])->name('quotations.delivery-note');
    Route::post('quotations/{quotation}/billing', [\App\Http\Controllers\Storekeeper\QuotationController::class, 'billing'])->name('quotations.billing');
    Route::get('quotations/store-customer/fetch', [\App\Http\Controllers\Storekeeper\QuotationController::class, 'fetchCustomer'])->name('quotations.store-customer'); 

    // Delivery Notes (New Module)
    Route::resource('delivery-notes', \App\Http\Controllers\Storekeeper\DeliveryNoteController::class);
    Route::post('delivery-notes/{deliveryNote}/create-invoice', [\App\Http\Controllers\Storekeeper\DeliveryNoteController::class, 'createInvoice'])->name('delivery-notes.create-invoice');
    
    // Tools & Equipment Tracking
    Route::resource('tools', \App\Http\Controllers\Storekeeper\ToolController::class);
    Route::get('tool-requests', [\App\Http\Controllers\Storekeeper\ToolController::class, 'requests'])->name('tool-requests.index');
    Route::post('tool-requests/{toolRequest}/issue', [\App\Http\Controllers\Storekeeper\ToolController::class, 'issue'])->name('tool-requests.issue');
    Route::post('tool-requests/{toolRequest}/reject', [\App\Http\Controllers\Storekeeper\ToolController::class, 'reject'])->name('tool-requests.reject');
    Route::post('tool-requests/{toolRequest}/return', [\App\Http\Controllers\Storekeeper\ToolController::class, 'returnItem'])->name('tool-requests.return');
    Route::post('tool-requests/{toolRequest}/approve', [\App\Http\Controllers\Storekeeper\ToolController::class, 'approve'])->name('tool-requests.approve');
    Route::post('delivery-notes/{deliveryNote}/dispatch', [\App\Http\Controllers\Storekeeper\DeliveryNoteController::class, 'dispatchNote'])->name('delivery-notes.dispatch');
    Route::get('delivery-notes/{deliveryNote}/print', [\App\Http\Controllers\Storekeeper\DeliveryNoteController::class, 'print'])->name('delivery-notes.print');

    // Equipment (Reused from Engineer)
    Route::resource('equipment', \App\Http\Controllers\Engineer\EquipmentController::class);
});

// SHARED RFQ & SUPPLIER MODULE (Accessible by Storekeeper & Accounts)
Route::middleware(['auth', 'role:storekeeper|accountant'])->prefix('storekeeper')->as('storekeeper.')->group(function () {
    Route::get('suppliers/export', [\App\Http\Controllers\Storekeeper\SupplierController::class, 'export'])->name('suppliers.export');
    Route::get('suppliers/import', [\App\Http\Controllers\Storekeeper\SupplierController::class, 'importForm'])->name('suppliers.import');
    Route::post('suppliers/import', [\App\Http\Controllers\Storekeeper\SupplierController::class, 'import'])->name('suppliers.import.store');
    Route::get('suppliers/{supplier}/pdf', [\App\Http\Controllers\Storekeeper\SupplierController::class, 'pdf'])->name('suppliers.pdf');
    Route::resource('suppliers', \App\Http\Controllers\Storekeeper\SupplierController::class);
    Route::resource('purchase-orders', \App\Http\Controllers\Storekeeper\PurchaseOrderController::class);
    Route::get('purchase-orders/{purchase_order}/pdf', [\App\Http\Controllers\Storekeeper\PurchaseOrderController::class, 'pdf'])->name('purchase-orders.pdf');
    Route::post('purchase-orders/{purchase_order}/receive', [\App\Http\Controllers\Storekeeper\PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::post('suppliers/{supplier}/documents', [\App\Http\Controllers\Storekeeper\SupplierDocumentController::class, 'store'])->name('suppliers.documents.store');
    Route::delete('suppliers/documents/{document}', [\App\Http\Controllers\Storekeeper\SupplierDocumentController::class, 'destroy'])->name('suppliers.documents.destroy');
    Route::get('suppliers/documents/{document}', [\App\Http\Controllers\Storekeeper\SupplierDocumentController::class, 'download'])->name('suppliers.documents.download');
    
    Route::post('rfq/incoterms', [\App\Http\Controllers\Storekeeper\RfqController::class, 'storeIncoterm'])->name('rfq.incoterms.store');
    Route::resource('rfq', \App\Http\Controllers\Storekeeper\RfqController::class);
    Route::get('rfq/{rfq}/pdf', [\App\Http\Controllers\Storekeeper\RfqController::class, 'downloadPdf'])->name('rfq.pdf');
    Route::get('rfq/{rfq}/preview', [\App\Http\Controllers\Storekeeper\RfqController::class, 'streamPdf'])->name('rfq.preview');
    Route::post('rfq/{rfq}/status', [\App\Http\Controllers\Storekeeper\RfqController::class, 'updateStatus'])->name('rfq.status');
    Route::post('rfq/{rfq}/quotations', [\App\Http\Controllers\Storekeeper\SupplierQuotationController::class, 'store'])->name('rfq.quotations.store');
    Route::post('rfq/{rfq}/quotations/{quotation}/select', [\App\Http\Controllers\Storekeeper\SupplierQuotationController::class, 'select'])->name('rfq.quotations.select');
    Route::get('rfq/{rfq}/quotations/{quotation}', [\App\Http\Controllers\Storekeeper\SupplierQuotationController::class, 'show'])->name('rfq.quotations.show');
    Route::get('rfq/{rfq}/quotations/{quotation}/edit', [\App\Http\Controllers\Storekeeper\SupplierQuotationController::class, 'edit'])->name('rfq.quotations.edit');
    Route::put('rfq/{rfq}/quotations/{quotation}', [\App\Http\Controllers\Storekeeper\SupplierQuotationController::class, 'update'])->name('rfq.quotations.update');
    Route::delete('rfq/{rfq}/quotations/{quotation}', [\App\Http\Controllers\Storekeeper\SupplierQuotationController::class, 'destroy'])->name('rfq.quotations.destroy');
});

// SUPER ADMIN SETTINGS
Route::middleware(['auth', 'role:super-admin'])->prefix('admin')->as('admin.')->group(function() {
    Route::get('settings', [\App\Http\Controllers\Admin\Settings\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\Settings\SettingController::class, 'update'])->name('settings.update');

    // Security & Access Control
    // Users resource moved to main admin group line 197
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    Route::get('activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    
    // Backups
    Route::post('backups/settings', [\App\Http\Controllers\Admin\BackupController::class, 'updateSettings'])->name('backups.settings');
    Route::resource('backups', \App\Http\Controllers\Admin\BackupController::class)->except(['create', 'edit', 'update', 'show']);
    Route::get('backups/{filename}/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backups.download');
    Route::post('settings', [\App\Http\Controllers\Admin\Settings\SettingController::class, 'update'])->name('settings.update');
});

// PUBLIC VERIFICATION
Route::get('verify/{number}', [\App\Http\Controllers\Admin\CertificateController::class, 'verifyPublic'])->name('certificates.verify-public');

// STAFF LEDGER CUSTOM ROUTES & PRINT
Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function() {
    Route::put('staff/ledger/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'updateLedgerEntry'])->name('staff.ledger.update');
    Route::delete('staff/ledger/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'destroyLedgerEntry'])->name('staff.ledger.destroy');
    Route::get('staff/ledger/{id}/print', [\App\Http\Controllers\Admin\StaffController::class, 'printLedgerEntry'])->name('staff.ledger.print');
});

// PETTY CASH MODULE
Route::middleware(['auth', 'role:admin|accountant'])->prefix('admin/accounts')->as('admin.accounts.')->group(function() {
    Route::resource('petty-cash', \App\Http\Controllers\Admin\Accounts\PettyCashController::class);
});