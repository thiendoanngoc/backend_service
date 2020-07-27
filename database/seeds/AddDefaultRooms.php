<?php

use App\Models\Room;
use Illuminate\Database\Seeder;

class AddDefaultRooms extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $room = new Room();
        $room->room_name = 'room 901';
        $room->address = '9th Floor, 111 A Street, B District, C City';
        $room->save();

        $room = new Room();
        $room->room_name = 'room 902';
        $room->address = '9th Floor, 111 A Street, B District, C City';
        $room->save();
    }
}
