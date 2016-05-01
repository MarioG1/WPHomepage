<?php

function get_hm($min) {
    return floor($min/60)."h ".($min%60)."min";
}
