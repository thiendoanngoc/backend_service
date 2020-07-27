<?php

use Carbon\Carbon;
use App\Models\MeetingSession;
use Illuminate\Database\Seeder;

class AddDefaultMeetingSessions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $start = Carbon::parse('2020-04-15 09:00:00');
        $end = Carbon::parse('2020-04-15 10:00:00');
        $meetingSession = new MeetingSession();
        $meetingSession->room_id = 1;
        $meetingSession->booker_id = 1;
        $meetingSession->meeting_start = $start;
        $meetingSession->meeting_end = $end;
        $meetingSession->save();

        $start = Carbon::parse('2020-04-15 10:00:00');
        $end = Carbon::parse('2020-04-15 11:00:00');
        $meetingSession = new MeetingSession();
        $meetingSession->room_id = 1;
        $meetingSession->booker_id = 1;
        $meetingSession->meeting_start = $start;
        $meetingSession->meeting_end = $end;
        $meetingSession->save();
    }
}
