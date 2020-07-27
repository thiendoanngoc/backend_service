<?php

use App\Models\RoomAttendee;
use Illuminate\Database\Seeder;

class AddDefaultRoomAttendees extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roomAttendee = new RoomAttendee();
        $roomAttendee->meeting_session_id = 1;
        $roomAttendee->attendee_id = 1;
        $roomAttendee->save();

        $roomAttendee = new RoomAttendee();
        $roomAttendee->meeting_session_id = 1;
        $roomAttendee->attendee_id = 2;
        $roomAttendee->save();

        $roomAttendee = new RoomAttendee();
        $roomAttendee->meeting_session_id = 2;
        $roomAttendee->attendee_id = 2;
        $roomAttendee->save();

        $roomAttendee = new RoomAttendee();
        $roomAttendee->meeting_session_id = 2;
        $roomAttendee->attendee_id = 3;
        $roomAttendee->save();
    }
}
