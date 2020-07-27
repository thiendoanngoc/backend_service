<?php

use App\Models\Notification;
use Illuminate\Database\Seeder;
use App\Enums\NotificationTypeEnum;

class AddDefaultNotifications extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$notification = new Notification();
		$notification->account_id = 2;
		$notification->type = NotificationTypeEnum::TaskAssign;
		$notification->ref_id = 1;
		$notification->title = 'New task 1';
		$notification->content = 'You have a new task 1';
		$notification->save();

		$notification = new Notification();
		$notification->account_id = 2;
		$notification->type = NotificationTypeEnum::TaskAssign;
		$notification->ref_id = 2;
		$notification->title = 'New task 2';
		$notification->content = 'You have a new task 2';
		$notification->save();

		$notification = new Notification();
		$notification->account_id = 2;
		$notification->type = NotificationTypeEnum::TaskAssign;
		$notification->ref_id = 3;
		$notification->title = 'New task 3';
		$notification->content = 'You have a new task 3';
		$notification->save();

		$notification = new Notification();
		$notification->account_id = 2;
		$notification->type = NotificationTypeEnum::TaskAssign;
		$notification->ref_id = 4;
		$notification->title = 'New task 4';
		$notification->content = 'You have a new task 4';
		$notification->save();

		$notification = new Notification();
		$notification->account_id = 2;
		$notification->type = NotificationTypeEnum::TaskAssign;
		$notification->ref_id = 5;
		$notification->title = 'New task 5';
		$notification->content = 'You have a new task 5';
		$notification->save();

		$notification = new Notification();
		$notification->account_id = 2;
		$notification->type = NotificationTypeEnum::TaskAssign;
		$notification->ref_id = 6;
		$notification->title = 'New task 6';
		$notification->content = 'You have a new task 6';
		$notification->save();

		$notification = new Notification();
		$notification->account_id = 2;
		$notification->type = NotificationTypeEnum::TaskAssign;
		$notification->ref_id = 7;
		$notification->title = 'New task 7';
		$notification->content = 'You have a new task 7';
		$notification->save();

		$notification = new Notification();
		$notification->account_id = 2;
		$notification->type = NotificationTypeEnum::Contract;
		$notification->ref_id = 1;
		$notification->title = 'Contract alert';
		$notification->content = 'Contract end alert';
		$notification->save();

		$notification = new Notification();
		$notification->account_id = 2;
		$notification->type = NotificationTypeEnum::Other;
		$notification->ref_id = 1;
		$notification->title = 'Slogan 1';
		$notification->content = 'This is slogan 1';
		$notification->save();
	}
}
