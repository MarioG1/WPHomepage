<?php

include_once 'php/config.class.php';
include_once 'php/pwcosts.class.php';
include_once 'php/wpstats.class.php';

session_start();

if (isset($_SESSION['login_state'])) {
    $login = $_SESSION['login_state'];
} else {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

if (!$login) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}


$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$data = [];

switch ($action) {
    case 'get_power_cost':
        $start = filter_input(INPUT_GET, 'start', FILTER_SANITIZE_NUMBER_INT);
        $stop = filter_input(INPUT_GET, 'stop', FILTER_SANITIZE_NUMBER_INT);

        $pwc = new pwcosts();
        $data['data'] = $pwc->get_cost_all($start, $stop);
        break;
}


echo json_encode($data);

