<?php

function excelColumnRange($lower, $upper) {
    ++$upper;
    $result = [];
    for ($i = $lower; $i !== $upper; ++$i) {
        $result[] = $i;
    }
    return $result;
}

function setRandomColor() {
    // Generate random values for each RGB component
    $r = rand(0, 255); // Red
    $g = rand(0, 255); // Green
    $b = rand(0, 255); // Blue
    
    // Return RGB format
    return "rgb($r, $g, $b)";
}

function toNumber($number){
    return (int) preg_replace("/[^0-9]/", '', $number);
}


function toPhoneNumber($number){
    preg_match('/^0+/', $number, $matches);
    $number = toNumber($number);
    // var_dump($matches, $number);
    $hp = toNumber($number);
    $hp = ($matches[0] ?? '').$hp;
    if (substr($hp,0,1) == '08')
        $hp = str_replace_first("08","628",$hp);
    return $hp;
}

function str_replace_first($search, $replace, $subject) {
    return implode($replace, explode($search, $subject, 2));
}

function pluckFromObjectsNested(array $objects, string $path): array {
    $keys = explode('.', $path);

    return array_map(function($obj) use ($keys) {
        $value = $obj;
        foreach ($keys as $key) {
            if (is_object($value) && isset($value->$key)) {
                $value = $value->$key;
            } else {
                return null;
            }
        }
        return $value;
    }, $objects);
}

function getDateRange($start, $end)
{
    $start = new DateTime($start);
    $end = new DateTime($end);

    // Include the end date in the range
    $end->modify('+1 day');

    $interval = new DateInterval('P1D'); // 1 day interval
    $period = new DatePeriod($start, $interval, $end);

    $lists = [];
    foreach ($period as $date) {
        $lists[] = $date->format('Y-m-d');
    }

    return $lists;
}

function get_date_interval($start, $end)
{
    $start = new DateTime($start);
    $end = new DateTime($end);

    $interval = $start->diff($end);

    return $interval->d;
}