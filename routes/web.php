<?php

use App\Http\Controllers\Tenant\QuoteController;
use App\Http\Controllers\Tenant\AssetController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Tenant\SmmeController;
use Illuminate\Support\Facades\Route;

//base64
Route::post('/log-module-usage', 'HomeController@moduleUsage')->name('log.module.usage');
Route::get('/check-php-version', function () {
	echo 'PHP Version: ' . phpversion();
});

Route::get('/', function () {
	return view('welcome');
})->name('landing');

Route::get('book-demo', function () {
	return view('book-demo');
})->name('book-demo');

// Authentication Routes...
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login')->name('auth2.login');
$this->post('logout', 'Auth\LoginController@logout')->name('auth.logout');

// Change Password Routes...
$this->get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
$this->patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password');

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.reset');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('auth.password.reset');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$this->post('password/reset', 'Auth\ResetPasswordController@reset')->name('auth.password.reset');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
	Route::get('/home', 'HomeController@index');

	// Clients 
	Route::middleware(['check.superadmin'])->group(function () {
		Route::resource('clients', 'Admin\ClientsController');
		Route::get('clients/dashboard/{id}', ['uses' => 'Admin\ClientsController@dashboard', 'as' => 'clients.dashboard']);

		Route::post('clients/active-inactive-data', ['uses' => 'Admin\ClientsController@getActiveInactiveData', 'as' => 'clients.active-inactive-data']);

		Route::post('clients/module-usage-data', ['uses' => 'Admin\ClientsController@getModulesUsageData', 'as' => 'clients.module-usage-data']);

		Route::post('clients/user-sessions-data', ['uses' => 'Admin\ClientsController@getUserSessionsData', 'as' => 'clients.user-sessions-data']);
	});


	Route::resource('roles', 'Admin\RolesController');
	Route::post('roles_mass_destroy', ['uses' => 'Admin\RolesController@massDestroy', 'as' => 'roles.mass_destroy']);
	Route::resource('users', 'Admin\UsersController');
	Route::resource('tenants', 'Admin\TenantsController');       //TODO
	Route::post('users_mass_destroy', ['uses' => 'Admin\UsersController@massDestroy', 'as' => 'users.mass_destroy']);
	Route::resource('teams', 'Admin\TeamsController');
	Route::post('teams_mass_destroy', ['uses' => 'Admin\TeamsController@massDestroy', 'as' => 'teams.mass_destroy']);
	Route::resource('products', 'Admin\ProductsController');
	Route::post('products_mass_destroy', ['uses' => 'Admin\ProductsController@massDestroy', 'as' => 'products.mass_destroy']);
	Route::post('products_restore/{id}', ['uses' => 'Admin\ProductsController@restore', 'as' => 'products.restore']);
	Route::delete('products_perma_del/{id}', ['uses' => 'Admin\ProductsController@perma_del', 'as' => 'products.perma_del']);


	Route::get('/team-select', ['uses' => 'Auth\TeamSelectController@select', 'as' => 'team-select.select']);
	Route::post('/team-select', ['uses' => 'Auth\TeamSelectController@storeSelect', 'as' => 'team-select.select']);
});

Route::group(['middleware' => ['auth'], 'prefix' => 'tenant', 'as' => 'tenant.'], function () {
	Route::get('/home', 'Tenant\HomeController@index');

	Route::resource('roles', 'Admin\RolesController');
	Route::post('roles_mass_destroy', ['uses' => 'Admin\RolesController@massDestroy', 'as' => 'roles.mass_destroy']);
	Route::resource('users', 'Admin\UsersController');
	Route::resource('tenants', 'Admin\TenantsController');       //TODO
	Route::post('users_mass_destroy', ['uses' => 'Admin\UsersController@massDestroy', 'as' => 'users.mass_destroy']);
	Route::resource('teams', 'Admin\TeamsController');
	Route::post('teams_mass_destroy', ['uses' => 'Admin\TeamsController@massDestroy', 'as' => 'teams.mass_destroy']);
	Route::resource('products', 'Admin\ProductsController');
	Route::post('products_mass_destroy', ['uses' => 'Admin\ProductsController@massDestroy', 'as' => 'products.mass_destroy']);
	Route::post('products_restore/{id}', ['uses' => 'Admin\ProductsController@restore', 'as' => 'products.restore']);
	Route::delete('products_perma_del/{id}', ['uses' => 'Admin\ProductsController@perma_del', 'as' => 'products.perma_del']);


	Route::get('/team-select', ['uses' => 'Auth\TeamSelectController@select', 'as' => 'team-select.select']);
	Route::post('/team-select', ['uses' => 'Auth\TeamSelectController@storeSelect', 'as' => 'team-select.select']);

	//projects
		Route::resource('tenants', 'Admin\TenantsController');       //TODO
	Route::get('projects/dashboard', ['uses' => 'Tenant\ProjectsController@dashboard', 'as' => 'projects.dashboard']);
	Route::get('projects/export', ['uses' => 'Tenant\ProjectsController@exportToExcel', 'as' => 'projects.export']);
	Route::get('invoices/export', ['uses' => 'Tenant\InvoiceController@exportToExcel', 'as' => 'invoices.export']);
	Route::get('assets/export', [AssetController::class, 'exportToExcel'])->name('assets.export');
	Route::get('quotes/export', [QuoteController::class, 'exportToExcel'])->name('quotes.export');
	Route::get('smmes/export', [SmmeController::class, 'exportToExcel'])->name('tenant.smmes.export');
	Route::resource('projects', 'Tenant\ProjectsController');

	// Weekly Plans - Employee Routes
	Route::resource('weekly-plans', 'Tenant\WeeklyPlanController', [
		'names' => [
			'index' => 'weekly-plans.index',
			'create' => 'weekly-plans.create',
			'store' => 'weekly-plans.store',
			'show' => 'weekly-plans.show',
			'edit' => 'weekly-plans.edit',
			'update' => 'weekly-plans.update',
			'destroy' => 'weekly-plans.destroy'
		]
	]);
	Route::post('weekly-plans/{id}/submit', [App\Http\Controllers\Tenant\WeeklyPlanController::class, 'submit'])
    ->name('tenant.weekly-plans.submit');


	// Additional weekly plan routes
	Route::patch('weekly-plans/{id}/submit', 'Tenant\WeeklyPlanController@submit')->name('weekly-plans.submit');
	Route::patch('weekly-plans/{id}/approve', 'Tenant\WeeklyPlanController@approve')->name('weekly-plans.approve');
	Route::patch('weekly-plans/{id}/reject', 'Tenant\WeeklyPlanController@reject')->name('weekly-plans.reject');
	Route::delete('weekly-plans/{id}', 'Tenant\WeeklyPlanController@destroy')->name('weekly-plans.destroy');

	// Manager dashboard
	Route::get('weekly-plans-manager', 'Tenant\WeeklyPlanController@managerDashboard')->name('weekly-plans.manager-dashboard');

	// Task management routes
	Route::patch('weekly-plan-tasks/{id}/approve', 'Tenant\WeeklyPlanTaskController@approve')->name('weekly-plan-tasks.approve');
	Route::patch('weekly-plan-tasks/{id}/reject', 'Tenant\WeeklyPlanTaskController@reject')->name('weekly-plan-tasks.reject');


	Route::resource('locations', 'Tenant\LocationController');

	//finance
	Route::resource('quotes', 'Tenant\QuoteController');
	Route::get('quotes/{quote}/pdf', ['uses' => 'Tenant\QuoteController@generatePDF', 'as' => 'quotes.pdf']);

	Route::post('/quotes/{quote}/generate-invoice', [\App\Http\Controllers\Tenant\QuoteController::class, 'generateInvoice'])
		->name('tenant.quotes.generateInvoice');


	//dashboards
	Route::resource('finance-stats', 'Dashboard\FinanceDashboardController');
	Route::resource('smme-stats', 'Dashboard\SmmeDashboardController');

	Route::resource('invoices', 'Tenant\InvoiceController');
	Route::get('invoices/{invoice}/pdf', ['uses' => 'Tenant\InvoiceController@generatePDF', 'as' => 'invoices.pdf']);


	//assets types
	Route::resource('asset-types', 'Tenant\AssetTypeController');
	//assets
	Route::resource('assets', 'Tenant\AssetController');
	Route::get('assets/search', ['uses' => 'Tenant\AssetController@search', 'as' => 'assets.search']);

	//SMMES
	Route::resource('smmes',  'Tenant\SmmeController');
	$this->get('/smmes/search', ['uses' => 'Tenant\SmmeController@search', 'as' => 'smmes.search']);

	//Entity Logs
	Route::resource('logs', 'Tenant\EntityLoggerController');
});
