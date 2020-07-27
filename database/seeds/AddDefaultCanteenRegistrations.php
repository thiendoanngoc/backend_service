<?php

use App\Models\CanteenRegistration;
use Illuminate\Database\Seeder;
use App\Enums\CanteenRegistrationEnum;
use App\Enums\CanteenModeEnum;
use App\Enums\CanteenStatusEnum;

class AddDefaultCanteenRegistrations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $registration = new CanteenRegistration();
        $registration->date = '2020-07-11';
        $registration->type = CanteenRegistrationEnum::Breakfast;
        $registration->account_id = 2;
        $registration->amount = 1;
        $registration->mode = CanteenModeEnum::Personal;
        $registration->status = CanteenStatusEnum::New;
        $registration->note = 'test';
        $registration->name_list = 'Abc';
        $registration->save();

        $registration = new CanteenRegistration();
        $registration->date = '2020-07-13';
        $registration->type = CanteenRegistrationEnum::Lunch;
        $registration->account_id = 2;
        $registration->amount = 1;
        $registration->mode = CanteenModeEnum::Personal;
        $registration->status = CanteenStatusEnum::New;
        $registration->note = 'test';
        $registration->save();

        $registration = new CanteenRegistration();
        $registration->date = '2020-07-14';
        $registration->type = CanteenRegistrationEnum::Breakfast;
        $registration->account_id = 2;
        $registration->amount = 7;
        $registration->mode = CanteenModeEnum::Department;
        $registration->status = CanteenStatusEnum::New;
        $registration->note = 'test';
        $registration->save();

        $registration = new CanteenRegistration();
        $registration->date = '2020-07-11';
        $registration->type = CanteenRegistrationEnum::Lunch;
        $registration->account_id = 3;
        $registration->amount = 1;
        $registration->mode = CanteenModeEnum::Personal;
        $registration->status = CanteenStatusEnum::New;
        $registration->note = 'test';
        $registration->save();

        $registration = new CanteenRegistration();
        $registration->date = '2020-07-11';
        $registration->type = CanteenRegistrationEnum::Breakfast;
        $registration->account_id = 4;
        $registration->amount = 1;
        $registration->mode = CanteenModeEnum::Personal;
        $registration->status = CanteenStatusEnum::New;
        $registration->note = 'test';
        $registration->save();

        $registration = new CanteenRegistration();
        $registration->date = '2020-07-12';
        $registration->type = CanteenRegistrationEnum::Lunch;
        $registration->account_id = 3;
        $registration->amount = 15;
        $registration->mode = CanteenModeEnum::Guest;
        $registration->status = CanteenStatusEnum::New;
        $registration->note = 'test';
        $registration->save();
    }
}
