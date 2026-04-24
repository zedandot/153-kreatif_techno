<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\MissionController as AdminMissionController;
use App\Http\Controllers\Admin\ProcessController as AdminProcessController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\AboutController as AdminAboutController;
use App\Http\Controllers\Admin\FinanceController as AdminFinanceController;
use App\Http\Controllers\Admin\Finance\InvoiceController;
use App\Http\Controllers\Admin\Finance\QuotationController;
use App\Http\Controllers\Admin\Finance\PurchaseOrderController;
use App\Http\Controllers\Admin\Finance\ExpenseController;
use App\Http\Controllers\Admin\Finance\BudgetController;
use App\Http\Controllers\Admin\Finance\ProjectController;
use App\Http\Controllers\Admin\Finance\BankController;
use App\Http\Controllers\Admin\Finance\IncomeController;
use App\Http\Controllers\Admin\Finance\CommissionController;
use App\Http\Controllers\Admin\PortfolioCategoryController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Models\AboutSetting;
use App\Models\Mission;
use App\Models\Process;
Route::get('/debug-auth', function (\Illuminate\Http\Request $request) {
    $email = $request->query('email', env('ADMIN_EMAIL', 'admin@153kreatif.com'));
    $password = $request->query('password', 'admin153!');
    
    $user = \App\Models\User::where('email', $email)->first();
    
    if (!$user) {
        return ['status' => 'FAIL', 'message' => 'User not found in database', 'db_connection' => config('database.default')];
    }
    
    $check = \Illuminate\Support\Facades\Hash::check($password, $user->password);
    
    return [
        'status' => 'OK',
        'email_in_db' => $user->email,
        'db_connection' => config('database.default'),
        'password_hash_check' => $check ? 'PASS' : 'FAIL',
        'is_password_hashed_with_quotes' => \Illuminate\Support\Facades\Hash::check('"' . $password . '"', $user->password) ? 'YES' : 'NO'
    ];
});

Route::get('/', fn() => view('user.intro'));
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');

Route::get('/about', function () {
    $about = AboutSetting::firstOrNew(['id' => 1]);
    $missions = Mission::orderBy('order')->get();
    $processes = Process::orderBy('order')->get();
    return view('user.about', compact('about', 'missions', 'processes'));
})->name('about');

// Admin Auth Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin Protected Routes
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.home.index');
    })->name('dashboard.redirect');

    Route::get('home', [AdminHomeController::class, 'index'])->name('home.index');
    Route::put('home', [AdminHomeController::class, 'update'])->name('home.update');

    // Finance Dashboard - Overview
    Route::get('finance', [AdminFinanceController::class, 'index'])->name('finance.index');
    Route::get('finance/api/stats', [AdminFinanceController::class, 'apiStats'])->name('finance.api.stats');

    // Finance - Invoices
    Route::get('finance/invoices', [InvoiceController::class, 'index'])->name('finance.invoices.index');
    Route::get('finance/invoices/export', [InvoiceController::class, 'exportExcel'])->name('finance.invoices.export');
    Route::get('finance/invoices/create', [InvoiceController::class, 'create'])->name('finance.invoices.create');
    Route::post('finance/invoices', [InvoiceController::class, 'store'])->name('finance.invoices.store');
    Route::get('finance/invoices/{invoice}', [InvoiceController::class, 'show'])->name('finance.invoices.show');
    Route::get('finance/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('finance.invoices.edit');
    Route::get('finance/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('finance.invoices.pdf');
    Route::put('finance/invoices/{invoice}', [InvoiceController::class, 'update'])->name('finance.invoices.update');
    Route::patch('finance/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('finance.invoices.status');
    Route::delete('finance/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('finance.invoices.destroy');

    // Finance - Quotations
    Route::get('finance/quotations', [QuotationController::class, 'index'])->name('finance.quotations.index');
    Route::get('finance/quotations/export', [QuotationController::class, 'exportExcel'])->name('finance.quotations.export');
    Route::post('finance/quotations', [QuotationController::class, 'store'])->name('finance.quotations.store');
    Route::put('finance/quotations/{quotation}', [QuotationController::class, 'update'])->name('finance.quotations.update');
    Route::patch('finance/quotations/{quotation}/status', [QuotationController::class, 'updateStatus'])->name('finance.quotations.status');
    Route::post('finance/quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('finance.quotations.convert');
    Route::get('finance/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('finance.quotations.pdf');
    Route::delete('finance/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('finance.quotations.destroy');

    // Finance - Purchase Orders
    Route::get('finance/purchase-orders', [PurchaseOrderController::class, 'index'])->name('finance.purchase-orders.index');
    Route::post('finance/purchase-orders', [PurchaseOrderController::class, 'store'])->name('finance.purchase-orders.store');
    Route::put('finance/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('finance.purchase-orders.update');
    Route::post('finance/purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('finance.purchase-orders.approve');
    Route::post('finance/purchase-orders/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject'])->name('finance.purchase-orders.reject');
    Route::patch('finance/purchase-orders/{purchaseOrder}/payment', [PurchaseOrderController::class, 'updatePayment'])->name('finance.purchase-orders.payment');
    Route::delete('finance/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('finance.purchase-orders.destroy');

    // Finance - Expenses
    Route::get('finance/expenses', [ExpenseController::class, 'index'])->name('finance.expenses.index');
    Route::get('finance/expenses/export', [ExpenseController::class, 'exportExcel'])->name('finance.expenses.export');
    Route::post('finance/expenses', [ExpenseController::class, 'store'])->name('finance.expenses.store');
    Route::put('finance/expenses/{expense}', [ExpenseController::class, 'update'])->name('finance.expenses.update');
    Route::delete('finance/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('finance.expenses.destroy');

    // Finance - Budgets
    Route::get('finance/budgets', [BudgetController::class, 'index'])->name('finance.budgets.index');
    Route::post('finance/budgets', [BudgetController::class, 'store'])->name('finance.budgets.store');
    Route::put('finance/budgets/{budget}', [BudgetController::class, 'update'])->name('finance.budgets.update');
    Route::delete('finance/budgets/{budget}', [BudgetController::class, 'destroy'])->name('finance.budgets.destroy');

    // Finance - Projects
    Route::get('finance/projects', [ProjectController::class, 'index'])->name('finance.projects.index');
    Route::post('finance/projects', [ProjectController::class, 'store'])->name('finance.projects.store');
    Route::put('finance/projects/{project}', [ProjectController::class, 'update'])->name('finance.projects.update');
    Route::delete('finance/projects/{project}', [ProjectController::class, 'destroy'])->name('finance.projects.destroy');

    // Finance - Bank & Kas
    Route::get('finance/bank', [BankController::class, 'index'])->name('finance.bank.index');
    Route::post('finance/bank/accounts', [BankController::class, 'storeAccount'])->name('finance.bank.accounts.store');
    Route::put('finance/bank/accounts/{bankAccount}', [BankController::class, 'updateAccount'])->name('finance.bank.accounts.update');
    Route::delete('finance/bank/accounts/{bankAccount}', [BankController::class, 'destroyAccount'])->name('finance.bank.accounts.destroy');
    Route::post('finance/bank/mutations', [BankController::class, 'storeMutation'])->name('finance.bank.mutations.store');
    Route::delete('finance/bank/mutations/{mutation}', [BankController::class, 'destroyMutation'])->name('finance.bank.mutations.destroy');

    // Finance - Incomes
    Route::get('finance/incomes', [IncomeController::class, 'index'])->name('finance.incomes.index');
    Route::get('finance/incomes/export', [IncomeController::class, 'exportExcel'])->name('finance.incomes.export');
    Route::post('finance/incomes', [IncomeController::class, 'store'])->name('finance.incomes.store');
    Route::delete('finance/incomes/{income}', [IncomeController::class, 'destroy'])->name('finance.incomes.destroy');

    // Finance - Commissions
    Route::get('finance/commissions', [CommissionController::class, 'index'])->name('finance.commissions.index');
    Route::post('finance/commissions', [CommissionController::class, 'store'])->name('finance.commissions.store');
    Route::put('finance/commissions/{commission}', [CommissionController::class, 'update'])->name('finance.commissions.update');
    Route::post('finance/commissions/{commission}/approve', [CommissionController::class, 'approve'])->name('finance.commissions.approve');
    Route::post('finance/commissions/{commission}/pay', [CommissionController::class, 'pay'])->name('finance.commissions.pay');
    Route::delete('finance/commissions/{commission}', [CommissionController::class, 'destroy'])->name('finance.commissions.destroy');

    // Home Services CRUD
    Route::post('home/services', [AdminHomeController::class, 'storeService'])->name('home.services.store');
    Route::put('home/services/{id}', [AdminHomeController::class, 'updateService'])->name('home.services.update');
    Route::delete('home/services/{id}', [AdminHomeController::class, 'deleteService'])->name('home.services.destroy');
    Route::post('home/services/reorder', [AdminHomeController::class, 'reorderService'])->name('home.services.reorder');

    // Home Projects CRUD
    Route::post('home/projects', [AdminHomeController::class, 'storeProject'])->name('home.projects.store');
    Route::put('home/projects/{id}', [AdminHomeController::class, 'updateProject'])->name('home.projects.update');
    Route::delete('home/projects/{id}', [AdminHomeController::class, 'deleteProject'])->name('home.projects.destroy');
    Route::post('home/projects/reorder', [AdminHomeController::class, 'reorderProject'])->name('home.projects.reorder');

    // About Page Management
    Route::get('about', [AdminAboutController::class, 'index'])->name('about.index');
    Route::put('about', [AdminAboutController::class, 'update'])->name('about.update');

    // Portfolio Categories
    Route::get('portfolio/categories', [PortfolioCategoryController::class, 'index'])->name('portfolio.categories.index');
    Route::post('portfolio/categories', [PortfolioCategoryController::class, 'store'])->name('portfolio.categories.store');
    Route::put('portfolio/categories/{id}', [PortfolioCategoryController::class, 'update'])->name('portfolio.categories.update');
    Route::delete('portfolio/categories/{id}', [PortfolioCategoryController::class, 'destroy'])->name('portfolio.categories.destroy');

    // Portfolio
    Route::get('portfolio', [AdminPortfolioController::class, 'index'])->name('portfolio.index');
    Route::post('portfolio', [AdminPortfolioController::class, 'store'])->name('portfolio.store');
    Route::post('portfolio/reorder', [AdminPortfolioController::class, 'reorder'])->name('portfolio.reorder');
    Route::put('portfolio/settings', [AdminPortfolioController::class, 'updateSettings'])->name('portfolio.settings.update');
    Route::put('portfolio/{id}', [AdminPortfolioController::class, 'update'])->name('portfolio.update');
    Route::delete('portfolio/{id}', [AdminPortfolioController::class, 'destroy'])->name('portfolio.destroy');

    // Services Management
    Route::get('services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::post('services', [AdminServiceController::class, 'store'])->name('services.store');
    Route::post('services/reorder', [AdminServiceController::class, 'reorder'])->name('services.reorder');
    Route::put('services/settings', [AdminServiceController::class, 'updateSettings'])->name('services.settings.update');
    Route::put('services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
    Route::delete('services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');

    // Service Features CRUD
    Route::post('services/{service}/features', [AdminServiceController::class, 'storeFeature'])->name('services.features.store');
    Route::put('services/features/{feature}', [AdminServiceController::class, 'updateFeature'])->name('services.features.update');
    Route::delete('services/features/{feature}', [AdminServiceController::class, 'destroyFeature'])->name('services.features.destroy');

    Route::resource('missions', AdminMissionController::class);
    Route::resource('processes', AdminProcessController::class);

    // Clients (Mitra)
    Route::resource('clients', ClientController::class);

    // Testimonials (Review)
    Route::resource('testimonials', TestimonialController::class);

    // Contact Page Management
    Route::get('contact', [AdminContactController::class, 'index'])->name('contact.index');
    Route::put('contact', [AdminContactController::class, 'update'])->name('contact.update');
    Route::resource('settings', AdminSettingController::class)->only(['index', 'update']);
});

