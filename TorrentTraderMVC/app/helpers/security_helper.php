<?php

// PDO escaping
function sqlesc($x)
{
    if (!is_numeric($x)) {
        $x = "'" . $x . "'";
    }
    return $x;
}