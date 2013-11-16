<?php

function generate_onoff_array($n) {
    if ($n == 1) {
        $generate = array(array(0), array(1));
    } else {
        $remaining = generate_onoff_array($n - 1);
        for ($i = 0; $i < count($remaining); $i++) {
            $generate[] = array_merge(array(0), $remaining[$i]);
            $generate[] = array_merge(array(1), $remaining[$i]);
        }
    }
    return $generate;
}

function and_two_calendars($calendar1, $calendar2) {
    for ($i = 0; $i < count($calendar1); $i++) {
        $result_calendar[] = $calendar1[$i] * $calendar2[$i];
    }
    return $result_calendar;
}

function and_calendars($calendars) {
    for ($i = 0; $i < count($calendars[0]); $i++) {
        $result_calendar[] = 1;
    }
    for ($i = 0; $i < count($calendars); $i++) {
        $result_calendar = and_two_calendars($result_calendar, $calendars[$i]);
    }
    return $result_calendar;
}

function and_all_combinations($calendars) {
    $current_comparison = array();
    $combinations = array();
    $onoff_array = generate_onoff_array(count($calendars));
    for ($i = 0; $i < count($onoff_array); $i++) {
        for ($j = 0; $j < count($onoff_array[$i]); $j++) {
            if ($onoff_array[$i][$j] == 1) {
                $current_comparison[] = $calendars[$j];
            }
        }
        if (count($current_comparison) > 1) {
            $combinations[] = and_calendars($current_comparison);
        }
    }
    return $combinations;
}

function compare($common_time1, $common_time2)
{
	return $common_time1[1] > $common_time2[1];
}

function find_times($common_calendar, $num_times, $minumum_minutes)
{
	$consecutive_count = 0;
	$consecutive_start = 0;
	$start_new_consecutive = true;
	$added_to_times = false;
	$times = array();
	echo("<h1>" . strval(count($common_calendar)) . "</h1>");
	for($i = 0; $i < count($common_calendar); $i++)
	{
		if($common_calendar[$i] == 1)
		{
			if($start_new_consecutive == true)
			{
				$consecutive_start = $i;
				$consecutive_count = 0;
				$start_new_consecutive = false;
				$added_to_times = false;
			}
			else if(++$consecutive_count >= $minumum_minutes)
			{
				if(!$added_to_times)
				{
					$times[] = array(++$consecutive_count, $consecutive_start);
					$added_to_times = true;
				}
				else
				{
					$times[count($times) - 1][0]++;
				}
			}
		}
		else if($common_calendar[$i] == 0)
		{
			$consecutive_count = 0;
			$start_new_consecutive = true;
		}
	}

	rsort($times);

	if(count($times) < $num_times)
	{
		$num_times = count($times);
	}

	$final_times = array();
	for($i = 0; $i < $num_times; $i++)
	{
		$final_times[] = $times[$i];
	}

	usort($final_times, compare);
	
	return $final_times;
}

function output_results($common_calendar, $num_times, $minumum_minutes, $start_time)
{
	$times = find_times($common_calendar, $num_times, $minumum_minutes);
	echo("<h1>Here are your top " . strval($num_times) . " times available for meeting:</h1>\n");
	echo("<form><p>\n");
	print_r($times);
	for($i = 0; $i < count($times); $i++)
	{
		echo("<input type = \"checkbox\" name = \"time" . strval($i) . "\" value = \"add\">" . strval($times[$i][0] + 1) . " minutes at " . date('l jS \of F Y h:i A', $times[$i][1] * 60 + $start_time - 1) . "<br/>\n");
	}
	echo("</p>\n");
	echo("<input type = \"submit\" value = \"Submit\">\n");
	echo("</form>\n");
	return;
}

?>
