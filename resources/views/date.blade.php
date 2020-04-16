@php

$year = date('Y');
$month = date('m');

$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

$firstDateOfTheMonth = new DateTime("$year-$month-1");

$lastDateOfTheMonth = new DateTime("$year-$month-$days");

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
@endphp
