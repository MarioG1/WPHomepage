<?php

function get_hm($min) {
    return floor($min/60).":".($min%60)."min";
}

