<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*

	Routing convention: Route::resource('/path', 'Controller') if you have all routes below

	GET /projects (index)
	GET /projects/create (create)

	GET /projects/{1} (show)
	POST /projects (store)

	GET /projects/1/edit (edit)
	PATCH /projects/1 (update)
	DELETE /projects/1 (destroy)

*/

// Begin auth routes
Route::prefix('auth')->group(function () {
	Route::post('login', 'Auth\AuthController@login')->name('auth.login');
	Route::post('login-by-phone', 'Auth\AuthController@loginByPhone')->name('auth.login-by-phone');
	// Route::post('register', 'Auth\AuthController@register')->name('auth.register');
	Route::get('logout', 'Auth\AuthController@logout')->middleware('check.auth')->name('auth.logout');
	Route::get('request-otp-code', 'Auth\AuthController@requestOTPCode')->name('auth.request-otp-code');
	Route::post('update-fcm-token', 'Auth\AuthController@updateFCMToken')->middleware('check.auth')->name('auth.update-fcm-token');
});
// End auth routes

// Begin storage routes
Route::prefix('storage')->group(function () {
	Route::group(['middleware' => ['check.auth']], function () {
		Route::post('upload-files', 'Storage\UploadController@uploadFiles')->name('storage.upload-files');
		Route::post('upload-images', 'Storage\UploadController@uploadImages')->name('storage.upload-images');
		Route::post('upload-base64-files', 'Storage\UploadController@uploadBase64Files')->name('storage.upload-base64-files');
		Route::post('upload-base64-images', 'Storage\UploadController@uploadBase64Images')->name('storage.upload-base64-images');
		Route::get('download-file', 'Storage\DownloadController@downloadFile')->name('storage.download-file');
		Route::get('download-image', 'Storage\DownloadController@downloadImage')->name('storage.download-image');
		Route::get('download-base64-image', 'Storage\DownloadController@downloadBase64Image')->name('storage.download-base64-image');
	});
});
// End storage routes

// Begin admin routes
Route::prefix(config('app.admin_url'))->group(function () {
	Route::group(['middleware' => ['check.permission']], function () {
		Route::get('profile', 'Admin\AccountController@showProfile')->name('admin.accounts.show-profile');
		Route::patch('profile', 'Admin\AccountController@updateProfile')->name('admin.accounts.update-profile');

		Route::get('roles', 'Admin\RoleController@index')->name('admin.roles.index');
		Route::get('roles/{role}', 'Admin\RoleController@show')->name('admin.roles.show');
		Route::post('roles', 'Admin\RoleController@store')->name('admin.roles.store');
		Route::patch('roles/{role}', 'Admin\RoleController@update')->name('admin.roles.update');
		Route::delete('roles/{role}', 'Admin\RoleController@destroy')->name('admin.roles.destroy');

		Route::get('settings', 'Admin\SettingController@show')->name('admin.settings.show');
		Route::patch('settings', 'Admin\SettingController@update')->name('admin.settings.update');

		Route::get('accounts', 'Admin\AccountController@index')->name('admin.accounts.index');
		Route::get('accounts/{account}', 'Admin\AccountController@show')->name('admin.accounts.show');
		Route::post('accounts', 'Admin\AccountController@store')->name('admin.accounts.store');
		Route::patch('accounts/{account}', 'Admin\AccountController@update')->name('admin.accounts.update');
		Route::delete('accounts/{account}', 'Admin\AccountController@destroy')->name('admin.accounts.destroy');

		Route::get('staffs', 'Admin\StaffController@index')->name('admin.staffs.index');
		Route::get('staffs/{account}', 'Admin\StaffController@show')->name('admin.staffs.show');
		Route::post('staffs', 'Admin\StaffController@store')->name('admin.staffs.store');
		Route::patch('staffs/{account}', 'Admin\StaffController@update')->name('admin.staffs.update');
		Route::delete('staffs/{account}', 'Admin\StaffController@destroy')->name('admin.staffs.destroy');

		Route::get('customers', 'Admin\CustomerController@index')->name('admin.customers.index');
		Route::get('customers/{customer}', 'Admin\CustomerController@show')->name('admin.customers.show');
		Route::post('customers', 'Admin\CustomerController@store')->name('admin.customers.store');
		Route::patch('customers/{customer}', 'Admin\CustomerController@update')->name('admin.customers.update');
		Route::delete('customers/{customer}', 'Admin\CustomerController@destroy')->name('admin.customers.destroy');

		Route::get('announcements', 'Admin\AnnouncementController@index')->name('admin.announcements.index');
		Route::get('announcements/{announcement}', 'Admin\AnnouncementController@show')->name('admin.announcements.show');
		Route::post('announcements', 'Admin\AnnouncementController@store')->name('admin.announcements.store');
		Route::patch('announcements/{announcement}', 'Admin\AnnouncementController@update')->name('admin.announcements.update');
		Route::delete('announcements/{announcement}', 'Admin\AnnouncementController@destroy')->name('admin.announcements.destroy');

		Route::get('departments', 'Admin\DepartmentController@index')->name('admin.departments.index');
		Route::get('departments/{department}', 'Admin\DepartmentController@show')->name('admin.departments.show');
		Route::post('departments', 'Admin\DepartmentController@store')->name('admin.departments.store');
		Route::patch('departments/{department}', 'Admin\DepartmentController@update')->name('admin.departments.update');
		Route::delete('departments/{department}', 'Admin\DepartmentController@destroy')->name('admin.departments.destroy');

		Route::get('positions', 'Admin\PositionController@index')->name('admin.positions.index');
		Route::get('positions/{position}', 'Admin\PositionController@show')->name('admin.positions.show');
		Route::post('positions', 'Admin\PositionController@store')->name('admin.positions.store');
		Route::patch('positions/{position}', 'Admin\PositionController@update')->name('admin.positions.update');
		Route::delete('positions/{position}', 'Admin\PositionController@destroy')->name('admin.positions.destroy');

		Route::get('contracts', 'Admin\ContractController@index')->name('admin.contracts.index');
		Route::get('contracts/{contract}', 'Admin\ContractController@show')->name('admin.contracts.show');
		Route::post('contracts', 'Admin\ContractController@store')->name('admin.contracts.store');
		Route::patch('contracts/{contract}', 'Admin\ContractController@update')->name('admin.contracts.update');
		Route::delete('contracts/{contract}', 'Admin\ContractController@destroy')->name('admin.contracts.destroy');

		Route::get('supplies', 'Admin\SupplyController@getAllSupplies')->name('admin.supplies.index');
		Route::get('supplies/{supply}', 'Admin\SupplyController@showSupply')->name('admin.supplies.show');
		Route::post('supplies', 'Admin\SupplyController@storeSupply')->name('admin.supplies.store');
		Route::patch('supplies/{supply}', 'Admin\SupplyController@updateSupply')->name('admin.supplies.update');
		Route::delete('supplies/{supply}', 'Admin\SupplyController@destroySupply')->name('admin.supplies.destroy');

		Route::get('supply-bills', 'Admin\SupplyController@getAllSupplyBills')->name('admin.supply-bills.index');
		Route::get('supply-bills/{supplyBill}', 'Admin\SupplyController@showSupplyBill')->name('admin.supply-bills.show');
		Route::post('supply-bills', 'Admin\SupplyController@storeSupplyBill')->name('admin.supply-bills.store');
		Route::patch('supply-bills/{supplyBill}', 'Admin\SupplyController@updateSupplyBill')->name('admin.supply-bills.update');
		Route::delete('supply-bills/{supplyBill}', 'Admin\SupplyController@destroySupplyBill')->name('admin.supply-bills.destroy');

		Route::get('devices', 'Admin\DeviceController@index')->name('admin.devices.index');
		Route::get('devices/{device}', 'Admin\DeviceController@show')->name('admin.devices.show');
		Route::post('devices', 'Admin\DeviceController@store')->name('admin.devices.store');
		Route::patch('devices/{device}', 'Admin\DeviceController@update')->name('admin.devices.update');
		Route::delete('devices/{device}', 'Admin\DeviceController@destroy')->name('admin.devices.destroy');

		Route::get('topics', 'Admin\VotingController@index')->name('admin.voting.index');
		Route::get('topics/{votingTopic}', 'Admin\VotingController@show')->name('admin.voting.show');
		Route::post('topics', 'Admin\VotingController@store')->name('admin.voting.store');
		Route::patch('topics/{votingTopic}', 'Admin\VotingController@update')->name('admin.voting.update');
		Route::delete('topics/{votingTopic}', 'Admin\VotingController@destroy')->name('admin.voting.destroy');

		Route::get('canteen-report-filter', 'Admin\CanteenController@canteenReportFilter')->name('admin.canteen-report-filter');
	});
});
// End admin routes

// Begin need auth routes

Route::group(['middleware' => ['check.auth']], function () {
	Route::get('check-jwt', 'Client\AccountController@checkJWT')->name('accounts.profile.check-jwt');
	Route::get('profile', 'Client\AccountController@show')->name('accounts.profile.show');
	Route::patch('profile', 'Client\AccountController@update')->name('accounts.profile.update');
	Route::get('get-department-members', 'Client\AccountController@getDepartmentMembers')->name('accounts.get-department-members');
	Route::get('get-today-birthday', 'Client\AccountController@getTodayBirthday')->name('accounts.get-today-birthday');

	Route::get('tasks', 'Client\TaskController@index')->name('tasks.index');
	Route::get('tasks/{task}', 'Client\TaskController@show')->name('tasks.show');
	Route::post('tasks', 'Client\TaskController@store')->name('tasks.store');
	Route::patch('tasks/{task}', 'Client\TaskController@update')->name('tasks.update');
	Route::patch('update-task-assigned/{task}', 'Client\TaskController@updateAssignedTask')->name('tasks.update-assigned-task');
	Route::get('task-statistics', 'Client\TaskController@taskStatistics')->name('tasks.statistics');
	Route::get('task-report', 'Client\TaskController@taskReport')->name('tasks.report');
	Route::get('task-maker-list', 'Client\TaskController@taskMakerList')->name('tasks.maker-list');
	Route::get('task-report-filter', 'Client\TaskController@taskReportFilter')->name('tasks.report-filter');
	Route::post('task-rating', 'Client\TaskController@taskRating')->name('tasks.rating');
	Route::post('task-decline', 'Client\TaskController@declineTask')->name('tasks.decline');
	Route::get('task-weekly-chart', 'Client\TaskController@getWeeklyChart')->name('tasks.weekly-chart');

	Route::get('task-schedulers', 'Client\TaskSchedulerController@index')->name('task-schedulers.index');

	Route::get('task-groups', 'Client\TaskGroupController@index')->name('task-groups.index');
	Route::get('task-groups/{taskGroup}', 'Client\TaskGroupController@show')->name('task-groups.show');
	Route::post('task-groups', 'Client\TaskGroupController@store')->name('task-groups.store');
	Route::patch('task-groups/{taskGroup}', 'Client\TaskGroupController@update')->name('task-groups.update');

	Route::get('announcements', 'Client\AnnouncementController@index')->name('announcements.index');
	Route::get('announcements/{announcement}', 'Client\AnnouncementController@show')->name('announcements.show');

	Route::get('contracts', 'Client\ContractController@index')->name('contracts.index');
	Route::get('contracts/{contract}', 'Client\ContractController@show')->name('contracts.show');

	Route::get('notifications', 'Client\NotificationController@index')->name('notifications.index');
	Route::post('read-notification', 'Client\NotificationController@readNotification')->name('notifications.read-notification');
	Route::get('get-unread-notification', 'Client\NotificationController@getUnreadNotification')->name('notifications.get-unread-notification');
	Route::delete('notifications', 'Client\NotificationController@destroyAll')->name('notifications.destroy-all-notification');
	Route::delete('notifications/{noti}', 'Client\NotificationController@destroyNoti')->name('notifications.destroy-notification');


	Route::get('unconfirm-tasks', 'Client\UnconfirmTaskController@index')->name('unconfirm-tasks.index');
	Route::get('unconfirm-tasks/{unconfirmTask}', 'Client\UnconfirmTaskController@show')->name('unconfirm-tasks.show');
	Route::post('request-cancel', 'Client\UnconfirmTaskController@requestCancel')->name('unconfirm-tasks.request-cancel');
	Route::post('approve-cancel', 'Client\UnconfirmTaskController@approveCancel')->name('unconfirm-tasks.approve-cancel');
	Route::post('approve-done', 'Client\UnconfirmTaskController@approveDone')->name('unconfirm-tasks.approve-done');
	Route::post('reject-request', 'Client\UnconfirmTaskController@rejectRequest')->name('unconfirm-tasks.reject-request');

	Route::get('get-all-assigner', 'Client\TaskController@getAllAssigner')->name('tasks.get-all-assigner');
	Route::get('get-all-assignee', 'Client\TaskController@getAllAssignee')->name('tasks.get-all-assignee');

	Route::get('stuffs', 'Client\StuffController@getAllStuff')->name('stuffs.get-all-stuff');
	Route::get('stuffs/{stuff}', 'Client\StuffController@showStuff')->name('stuffs.show-stuff');
	Route::get('all-my-stuffs', 'Client\StuffController@getAllMyStuffs')->name('stuffs.get-all-my-stuffs');
	Route::post('stuffs', 'Client\StuffController@storeStuff')->name('stuffs.store-stuff');
	Route::patch('stuffs/{stuff}', 'Client\StuffController@updateStuff')->name('stuffs.update-stuff');
	Route::post('change-stuff-status', 'Client\StuffController@changeStuffStatus')->name('stuffs.change-stuff-status');

	Route::get('topics', 'Client\VotingController@index')->name('voting.index');
	Route::get('topics/{votingTopic}', 'Client\VotingController@show')->name('voting.show');
	Route::post('topics', 'Client\VotingController@store')->name('voting.store');
	Route::patch('topics/{votingTopic}', 'Client\VotingController@update')->name('voting.update');
	Route::post('vote', 'Client\VotingController@vote')->name('voting.vote');
	Route::post('un-vote', 'Client\VotingController@unVote')->name('voting.un-vote');
	Route::delete('topics/{topic}', 'Client\VotingController@destroy')->name('voting.destroy');
	
	Route::get('canteen', 'Client\CanteenController@index')->name('canteen.index');
	Route::post('canteen', 'Client\CanteenController@store')->name('canteen.store');
	Route::get('canteen-history', 'Client\CanteenController@showHistory')->name('canteen.canteen-history');
	Route::get('canteen-count', 'Client\CanteenController@countRegister')->name('canteen.canteen-count');
	Route::get('canteen-detail/{canteen}', 'Client\CanteenController@showDetailCanteen')->name('canteen.canteen-detail');
	Route::delete('canteen/{canteen}', 'Client\CanteenController@destroy')->name('canteen.destroy');
	
	Route::get('search', 'Client\CommonController@searchStaff')->name('canteen.search');

});

// End need auth routes

// Begin non auth routes

Route::get('departments', 'Client\DepartmentController@index')->name('departments.index');
Route::get('departments/{department}/positions', 'Client\DepartmentController@getPositions')->name('departments.get-positions');

Route::get('settings', 'Client\SettingController@show')->name('settings.show');

// End non auth routes
