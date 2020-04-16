<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Event;

class EventController extends Controller
{
  public function show()
  {
      //get dates in current month/year
      $year = date('Y');
      $month = date('m');

      $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

      $firstDateOfTheMonth = new \DateTime("$year-$month-1");

      $lastDateOfTheMonth = new \DateTime("$year-$month-$days");

      $frontPadding = $firstDateOfTheMonth->format('w');  //0-6
      $backPadding = 6 - $lastDateOfTheMonth->format('w');

      for ($i=0; $i < $frontPadding; $i++) {    //填前面的padiing
          $dates[] = null;
      }
      for ($i=0; $i < $days; $i++) {           //填1~31
          $dates[] = $i + 1;
      }
      for ($i=0; $i < $backPadding; $i++) {     //填後面的padiing
          $dates[] = null;
      }

      //get user's events
      $events = Auth::user()->events()->orderBy('start_time')->get();

      foreach ($events as $key => $event) {
        $events[$key]['start_time'] = substr($event['start_time'], 0, 5);
      }

      return view('/index', ['events' => $events, 'dates' => $dates]);
  }

  public function read(Event $event)
  {
    return response()->json($event);
  }

  public function store(Request $request)
  {
      //Title
      if ($this->eventTitleValidate($request))
        return response()->json('Title caonnot be blank.', 404);
      //Time range
      if ($this->eventTimeValidate($request))
        return response()->json('Time range error.', 404);

      $event = new event;
      $event->fill($request->all());
      $event->user_id = Auth::id();

      $event['start_time'] = substr($event['start_time'], 0, 5);

      $event->save();

      return response()->json($event);
  }

  public function update(Request $request, Event $event)
  {
      //Title
      if ($this->eventTitleValidate($request))
        return response()->json('Title caonnot be blank.', 404);
      //Time range
      if ($this->eventTimeValidate($request))
        return response()->json('Time range error.', 404);

      $event->fill($request->all());
      $event['start_time'] = substr($event['start_time'], 0, 5);

      $event->save();

      return response()->json($event);
  }

  public function destroy(Event $event)
  {
    $event->delete();
    return response()->json($event);
  }

  public function eventTitleValidate($request)
  {
    if (empty($request->input('title')))
      return true;
  }

  public function eventTimeValidate($request)
  {
    $startTime= explode(':', $request->input('start_time'));
    $endTime = explode(':', $request->input('end_time'));
    if ($startTime[0] > $endTime[0] || ($startTime[0]==$endTime[0] && $startTime[1]>$endTime[1])) {
      return true;
    }
  }

}
