<?php

use App\Models\Stuff;
use Illuminate\Database\Seeder;

class AddDefaultStuffs extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $voters = new Stuff();
        $voters->name = 'iphone x';
        $voters->description = 'need to sell iphone x';
        $voters->seller_id = 1;
        $voters->save();

        $voters = new Stuff();
        $voters->name = 'house';
        $voters->description = 'need to sell my house';
        $voters->seller_id = 1;
        $voters->save();
    }
}
