<?php

class DateTimeView
{
	public function show()
	{
		date_default_timezone_set('Europe/Stockholm');

		$dayName = date('l');
		$dayNumber = date('jS');
		$month = date('F');
		$year = date('Y');
		$time = date('H:i:s');

		$timeString = "$dayName, the $dayNumber of $month $year, The time is $time";

		return '<p>' . $timeString . '</p>';
	}
}
