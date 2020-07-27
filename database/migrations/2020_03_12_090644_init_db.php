<?php

use App\Enums\DeviceStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\TaskPriorityEnum;
use App\Enums\TaskRoleEnum;
use App\Enums\TaskStatusEnum;
use App\Enums\SellingStuffStatusEnum;
use App\Enums\RoomStatusEnum;
use App\Enums\TaskLevelEnum;
use App\Enums\VehicleStatusEnum;
use App\Enums\ContractStatusEnum;
use App\Enums\VotingTargetTypeEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitDb extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$currentDB = config('database.default');

		if ($currentDB === config('app.master_db')) {
			Schema::create('settings', function (Blueprint $table) {
				$table->increments('id');
				$table->string('app_name', 32);
				$table->string('app_slogan', 64);
				$table->string('app_owner', 32);
				$table->string('email', 32);
				$table->string('phone_number', 32);
				$table->string('address');
				$table->string('logo')->nullable();
				$table->string('location')->nullable();
				$table->string('term_of_service')->nullable();
				$table->string('faq')->nullable();
				$table->customTimestamps();
			});

			Schema::create('accounts', function (Blueprint $table) {
				$table->bigIncrements('id');
				$table->string('db_name', 32)->nullable();
				$table->string('username', 32);
				$table->string('password', 64)->nullable();
				$table->string('email', 32)->unique();
				$table->string('phone_number', 32)->unique();
				$table->string('image')->nullable();
				$table->string('full_name', 32)->nullable();
				$table->string('address')->nullable();
				$table->string('my_status')->nullable();
				$table->date('birthday')->nullable();
				$table->unsignedInteger('gender_id')->default(GenderEnum::Unknown);
				$table->string('hobbies')->nullable();
				$table->unsignedBigInteger('creater_id')->nullable();
				$table->unsignedBigInteger('updater_id')->nullable();
				$table->customSoftDeletes();
				$table->customTimestamps();

				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('roles', function (Blueprint $table) {
				$table->increments('id');
				$table->string('role_name', 32)->unique();
				$table->unsignedBigInteger('creater_id')->nullable();
				$table->unsignedBigInteger('updater_id')->nullable();
				$table->customSoftDeletes();
				$table->customTimestamps();

				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('web_routes', function (Blueprint $table) {
				$table->increments('id');
				$table->string('web_route_name')->unique();
				$table->string('description')->nullable();
			});

			Schema::create('permissions', function (Blueprint $table) {
				$table->unsignedInteger('role_id');
				$table->unsignedInteger('web_route_id');
				$table->customSoftDeletes();

				$table->primary(array('role_id', 'web_route_id'));
				$table->foreign('role_id')->references('id')->on('roles');
				$table->foreign('web_route_id')->references('id')->on('web_routes');
			});

			Schema::create('role_mappings', function (Blueprint $table) {
				$table->unsignedBigInteger('account_id');
				$table->unsignedInteger('role_id');
				$table->customSoftDeletes();

				$table->primary(array('account_id', 'role_id'));
				$table->foreign('account_id')->references('id')->on('accounts');
				$table->foreign('role_id')->references('id')->on('roles');
			});

			Schema::create('account_sessions', function (Blueprint $table) {
				$table->bigIncrements('id');
				$table->unsignedBigInteger('account_id');
				$table->string('device_name', 64)->nullable();
				$table->string('device_platform', 64)->nullable();
				$table->string('device_imei', 64)->nullable();
				$table->string('fcm_token', 256)->nullable();
				$table->boolean('is_remember')->default(false);
				$table->dateTime('last_active')->useCurrent();

				$table->foreign('account_id')->references('id')->on('accounts');
			});

			Schema::create('phone_otps', function (Blueprint $table) {
				$table->increments('id');
				$table->string('phone_number', 32)->unique();
				$table->string('otp_code', 8);
				$table->dateTime('created_at')->useCurrent();
			});
		} else {
			Schema::create('departments', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name')->unique();
				$table->string('code')->nullable();
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customSoftDeletes();
				$table->customTimestamps();
			});

			Schema::create('positions', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('department_id');
				$table->string('name');
				$table->unsignedInteger('level');

				$table->foreign('department_id')->references('id')->on('departments');
			});

			Schema::create('accounts', function (Blueprint $table) {
				$table->increments('id');
				$table->string('username', 32)->unique();
				$table->string('password', 64);
				$table->string('email', 32)->unique();
				$table->string('phone_number', 32)->unique();
				$table->string('image')->nullable();
				$table->string('full_name', 32)->nullable();
				$table->string('address')->nullable();
				$table->string('my_status')->nullable();
				$table->date('birthday')->nullable();
				$table->unsignedInteger('gender_id')->default(GenderEnum::Unknown);
				$table->string('hobbies')->nullable();
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customSoftDeletes();
				$table->customTimestamps();

				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('roles', function (Blueprint $table) {
				$table->increments('id');
				$table->string('role_name', 32)->unique();
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customSoftDeletes();
				$table->customTimestamps();

				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('web_routes', function (Blueprint $table) {
				$table->increments('id');
				$table->string('web_route_name')->unique();
				$table->string('description')->nullable();
			});

			Schema::create('permissions', function (Blueprint $table) {
				$table->unsignedInteger('role_id');
				$table->unsignedInteger('web_route_id');
				$table->customSoftDeletes();

				$table->primary(array('role_id', 'web_route_id'));
				$table->foreign('role_id')->references('id')->on('roles');
				$table->foreign('web_route_id')->references('id')->on('web_routes');
			});

			Schema::create('role_mappings', function (Blueprint $table) {
				$table->unsignedInteger('account_id');
				$table->unsignedInteger('role_id');
				$table->customSoftDeletes();

				$table->primary(array('account_id', 'role_id'));
				$table->foreign('account_id')->references('id')->on('accounts');
				$table->foreign('role_id')->references('id')->on('roles');
			});

			Schema::create('account_sessions', function (Blueprint $table) {
				$table->bigIncrements('id');
				$table->unsignedInteger('account_id');
				$table->string('device_name', 64)->nullable();
				$table->string('device_platform', 64)->nullable();
				$table->string('device_imei', 64)->nullable();
				$table->string('fcm_token', 256)->nullable();
				$table->boolean('is_remember')->default(false);
				$table->dateTime('last_active')->useCurrent();

				$table->foreign('account_id')->references('id')->on('accounts');
			});

			Schema::create('phone_otps', function (Blueprint $table) {
				$table->increments('id');
				$table->string('phone_number', 32)->unique();
				$table->string('otp_code', 8);
				$table->dateTime('created_at')->useCurrent();
			});

			Schema::create('staffs', function (Blueprint $table) {
				$table->unsignedInteger('account_id');
				$table->unsignedInteger('position_id');

				$table->primary('account_id');
				$table->foreign('account_id')->references('id')->on('accounts');
				$table->foreign('position_id')->references('id')->on('positions');
			});

			Schema::create('customers', function (Blueprint $table) {
				$table->increments('id');
				$table->string('full_name', 32);
				$table->string('email', 32)->unique();
				$table->string('phone_number', 32)->unique();
				$table->string('company_name', 32)->nullable();
				$table->string('address')->nullable();
				$table->date('birthday')->nullable();
				$table->unsignedInteger('gender_id')->default(GenderEnum::Unknown);
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customSoftDeletes();
				$table->customTimestamps();

				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('attachments', function (Blueprint $table) {
				$table->increments('id');
				$table->string('path');
			});

			Schema::create('task_groups', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name', 32);
				$table->string('description')->nullable();
				$table->string('note')->nullable();
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customSoftDeletes();
				$table->customTimestamps();

				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('tasks', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('task_group_id')->nullable();
				$table->unsignedInteger('task_priority')->default(TaskPriorityEnum::Normal);
				$table->unsignedInteger('task_level')->default(TaskLevelEnum::Medium);
				$table->unsignedInteger('status')->default(TaskStatusEnum::New);
				$table->string('title', 32);
				$table->string('description')->nullable();
				$table->dateTime('start_time')->useCurrent();
				$table->dateTime('end_time')->nullable();
				$table->dateTime('actual_end_time')->nullable();
				$table->dateTime('reminder_before_time')->nullable();
				$table->unsignedInteger('reminder_type')->nullable();
				$table->string('note')->nullable();
				$table->unsignedInteger('rating')->default(0);
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customSoftDeletes();
				$table->customTimestamps();

				$table->foreign('task_group_id')->references('id')->on('task_groups');
				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('task_attachments', function (Blueprint $table) {
				$table->unsignedInteger('task_id');
				$table->unsignedInteger('attachment_id');

				$table->primary(array('task_id', 'attachment_id'));
				$table->foreign('task_id')->references('id')->on('tasks');
				$table->foreign('attachment_id')->references('id')->on('attachments');
			});

			Schema::create('task_assignees', function (Blueprint $table) {
				$table->unsignedInteger('task_id');
				$table->unsignedInteger('account_id');
				$table->unsignedInteger('task_role')->default(TaskRoleEnum::Hoster);

				$table->primary(array('task_id', 'account_id'));
				$table->foreign('task_id')->references('id')->on('tasks');
				$table->foreign('account_id')->references('id')->on('accounts');
			});

			Schema::create('unconfirm_tasks', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('task_id');
				$table->unsignedInteger('update_assignee_id');
				$table->string('reason');
				$table->dateTime('update_due_date');
				$table->unsignedInteger('status')->default(TaskStatusEnum::New);
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customTimestamps();

				$table->foreign('task_id')->references('id')->on('tasks');
				$table->foreign('update_assignee_id')->references('id')->on('accounts');
				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('task_schedulers', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('account_id');
				$table->string('name');

				$table->foreign('account_id')->references('id')->on('accounts');
			});

			Schema::create('daily_notifications', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('type');
				$table->text('content');
			});

			Schema::create('announcements', function (Blueprint $table) {
				$table->increments('id');
				$table->string('content');
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customTimestamps();

				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('announcement_attachments', function (Blueprint $table) {
				$table->unsignedInteger('announcement_id');
				$table->unsignedInteger('attachment_id');

				$table->primary(array('announcement_id', 'attachment_id'));
				$table->foreign('announcement_id')->references('id')->on('announcements');
				$table->foreign('attachment_id')->references('id')->on('attachments');
			});

			Schema::create('supplies', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name', 32);
				$table->double('price')->default(0);
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customTimestamps();

				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('supply_bills', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('supply_id');
				$table->double('amount')->default(0);
				$table->double('total')->default(0);
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customTimestamps();
				$table->customSoftDeletes();

				$table->foreign('supply_id')->references('id')->on('supplies');
				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('supply_bill_attachments', function (Blueprint $table) {
				$table->unsignedInteger('supply_bill_id');
				$table->unsignedInteger('attachment_id');

				$table->primary(array('supply_bill_id', 'attachment_id'));
				$table->foreign('supply_bill_id')->references('id')->on('supply_bills');
				$table->foreign('attachment_id')->references('id')->on('attachments');
			});

			Schema::create('voting_options', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name');
			});

			Schema::create('voting_topics', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name');
				$table->string('description')->nullable();
				$table->dateTime('start_date')->nullable();
				$table->dateTime('end_date')->nullable();
				$table->string('winning_option_id')->nullable();
				$table->unsignedInteger('target_type');
				$table->text('target_users');
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customTimestamps();

				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('voting_bindings', function (Blueprint $table) {
				$table->unsignedInteger('voting_topic_id');
				$table->unsignedInteger('voting_option_id');

				$table->foreign('voting_topic_id')->references('id')->on('voting_topics');
				$table->foreign('voting_option_id')->references('id')->on('voting_options');
			});

			Schema::create('voters', function (Blueprint $table) {
				$table->unsignedInteger('voter_id');
				$table->unsignedInteger('voting_option_id');
				$table->unsignedInteger('voting_topic_id');

				$table->foreign('voter_id')->references('id')->on('accounts');
				$table->foreign('voting_option_id')->references('id')->on('voting_options');
				$table->foreign('voting_topic_id')->references('id')->on('voting_topics');
			});

			Schema::create('devices', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('account_id')->nullable();
				$table->string('name', 32);
				$table->string('model', 32);
				$table->date('bought_date');
				$table->date('guarantee_date');
				$table->string('detail')->nullable();
				$table->unsignedInteger('status')->default(DeviceStatusEnum::InWareHouse);
				$table->string('note')->nullable();
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customTimestamps();

				$table->foreign('account_id')->references('id')->on('accounts');
				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('contracts', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name', 128);
				$table->string('description')->nullable();
				$table->unsignedInteger('status')->default(ContractStatusEnum::InProgress);
				$table->date('start_date');
				$table->date('end_date')->nullable();
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customSoftDeletes();
				$table->customTimestamps();

				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('contract_attachments', function (Blueprint $table) {
				$table->unsignedInteger('contract_id');
				$table->unsignedInteger('attachment_id');

				$table->primary(array('contract_id', 'attachment_id'));
				$table->foreign('contract_id')->references('id')->on('contracts');
				$table->foreign('attachment_id')->references('id')->on('attachments');
			});

			Schema::create('chat_groups', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name', 32);
			});

			Schema::create('chat_messages', function (Blueprint $table) {
				$table->bigIncrements('id');
				$table->unsignedInteger('from_account_id');
				$table->unsignedInteger('to_chat_group_id');
				$table->string('content');
				$table->dateTime('created_at')->useCurrent();

				$table->foreign('from_account_id')->references('id')->on('accounts');
				$table->foreign('to_chat_group_id')->references('id')->on('chat_groups');
			});

			Schema::create('chat_group_members', function (Blueprint $table) {
				$table->unsignedInteger('chat_group_id');
				$table->unsignedInteger('account_id');

				$table->foreign('chat_group_id')->references('id')->on('chat_groups');
				$table->foreign('account_id')->references('id')->on('accounts');
			});

			Schema::create('rooms', function (Blueprint $table) {
				$table->increments('id');
				$table->string('room_name', 32);
				$table->string('address');
				$table->unsignedInteger('status')->default(RoomStatusEnum::Available);
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customTimestamps();

				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('meeting_sessions', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('room_id');
				$table->unsignedInteger('booker_id');
				$table->dateTime('meeting_start');
				$table->dateTime('meeting_end')->nullable();
				$table->unsignedInteger('room_rating')->default(0);
				$table->unsignedInteger('attitude_rating')->default(0);
				$table->customTimestamps();

				$table->foreign('room_id')->references('id')->on('rooms');
				$table->foreign('booker_id')->references('id')->on('accounts');
			});

			Schema::create('room_attendees', function (Blueprint $table) {
				$table->unsignedInteger('meeting_session_id');
				$table->unsignedInteger('attendee_id');

				$table->primary(array('meeting_session_id', 'attendee_id'));
				$table->foreign('meeting_session_id')->references('id')->on('meeting_sessions');
				$table->foreign('attendee_id')->references('id')->on('accounts');
			});

			Schema::create('vehicles', function (Blueprint $table) {
				$table->increments('id');
				$table->string('vehicle_name', 32);
				$table->string('vehicle_number', 32);
				$table->unsignedInteger('status')->default(VehicleStatusEnum::Available);
			});

			Schema::create('vehicle_sessions', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('vehicle_id');
				$table->unsignedInteger('booker_id');
				$table->dateTime('booking_start');
				$table->dateTime('booking_end')->nullable();
				$table->unsignedInteger('service_rating')->default(0);
				$table->unsignedInteger('quality_rating')->default(0);
				$table->unsignedInteger('attitude_rating')->default(0);

				$table->foreign('vehicle_id')->references('id')->on('vehicles');
				$table->foreign('booker_id')->references('id')->on('accounts');
			});

			Schema::create('vehicle_attendees', function (Blueprint $table) {
				$table->unsignedInteger('vehicle_session_id');
				$table->unsignedInteger('attendee_id');

				$table->foreign('vehicle_session_id')->references('id')->on('vehicle_sessions');
				$table->foreign('attendee_id')->references('id')->on('accounts');
			});

			Schema::create('stuffs', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name', 32);
				$table->string('description');
				$table->double('price')->default(0);
				$table->unsignedInteger('selling_status')->default(SellingStuffStatusEnum::Open);
				$table->dateTime('selling_start')->useCurrent();
				$table->dateTime('selling_end')->nullable();
				$table->unsignedInteger('seller_id');
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customTimestamps();

				$table->foreign('seller_id')->references('id')->on('accounts');
				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});

			Schema::create('stuff_attachments', function (Blueprint $table) {
				$table->unsignedInteger('stuff_id');
				$table->unsignedInteger('attachment_id');

				$table->primary(array('stuff_id', 'attachment_id'));
				$table->foreign('stuff_id')->references('id')->on('stuffs');
				$table->foreign('attachment_id')->references('id')->on('attachments');
			});

			Schema::create('notifications', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('account_id')->nullable();
				$table->unsignedInteger('type');
				$table->unsignedInteger('ref_id');
				$table->string('title');
				$table->string('content');
				$table->boolean('is_read')->default(false);
				$table->customTimestamps();

				$table->foreign('account_id')->references('id')->on('accounts');
			});

			Schema::create('canteen_registrations', function (Blueprint $table) {
				$table->increments('id');
				$table->dateTime('date');
				$table->unsignedInteger('type');
				$table->unsignedInteger('account_id');
				$table->unsignedInteger('amount');
				$table->unsignedInteger('mode');
            	$table->unsignedInteger('status')->default(1);
				$table->string('note')->nullable();
				$table->string('name_list')->nullable();
				$table->unsignedInteger('creater_id')->nullable();
				$table->unsignedInteger('updater_id')->nullable();
				$table->customSoftDeletes();
				$table->customTimestamps();

				$table->foreign('account_id')->references('id')->on('accounts');
				$table->foreign('creater_id')->references('id')->on('accounts');
				$table->foreign('updater_id')->references('id')->on('accounts');
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$currentDB = config('database.default');

		if ($currentDB === config('app.master_db')) {
			Schema::dropIfExists('account_sessions');
			Schema::dropIfExists('role_mappings');
			Schema::dropIfExists('permissions');
			Schema::dropIfExists('web_routes');
			Schema::dropIfExists('roles');
			Schema::dropIfExists('accounts');
			Schema::dropIfExists('settings');
		} else {
			Schema::dropIfExists('canteen_registrations');
			Schema::dropIfExists('notifications');
			Schema::dropIfExists('stuffs');
			Schema::dropIfExists('vehicle_attendees');
			Schema::dropIfExists('vehicle_sessions');
			Schema::dropIfExists('vehicles');
			Schema::dropIfExists('room_attendees');
			Schema::dropIfExists('meeting_sessions');
			Schema::dropIfExists('rooms');
			Schema::dropIfExists('chat_group_members');
			Schema::dropIfExists('chat_messages');
			Schema::dropIfExists('chat_groups');
			Schema::dropIfExists('contract_attachments');
			Schema::dropIfExists('contracts');
			Schema::dropIfExists('devices');
			Schema::dropIfExists('voters');
			Schema::dropIfExists('voting_bindings');
			Schema::dropIfExists('voting_topics');
			Schema::dropIfExists('voting_options');
			Schema::dropIfExists('supply_bill_attachments');
			Schema::dropIfExists('supply_bills');
			Schema::dropIfExists('supplies');
			Schema::dropIfExists('announcement_attachments');
			Schema::dropIfExists('announcements');
			Schema::dropIfExists('daily_notifications');
			Schema::dropIfExists('unconfirm_tasks');
			Schema::dropIfExists('task_assignees');
			Schema::dropIfExists('task_attachments');
			Schema::dropIfExists('tasks');
			Schema::dropIfExists('task_groups');
			Schema::dropIfExists('attachments');
			Schema::dropIfExists('customers');
			Schema::dropIfExists('staffs');
			Schema::dropIfExists('account_sessions');
			Schema::dropIfExists('role_mappings');
			Schema::dropIfExists('permissions');
			Schema::dropIfExists('web_routes');
			Schema::dropIfExists('roles');
			Schema::dropIfExists('accounts');
			Schema::dropIfExists('departments');
		}
	}
}
