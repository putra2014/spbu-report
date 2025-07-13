<?php

if (!function_exists('getCurrentShift')) {
    function getCurrentShift(): int
    {
        $now = date('H:i:s');
        if ($now >= '00:00:00' && $now < '08:00:00') {
            return 1;
        } elseif ($now >= '08:00:00' && $now < '16:00:00') {
            return 2;
        } else {
            return 3;
        }
    }
}
