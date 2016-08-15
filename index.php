<?php

include_once 'php/config.class.php';

session_start();

if(isset($_SESSION['login_state'])) {
    $login = $_SESSION['login_state'];
} else {
    $login = false;
}

try {
    $conn = new PDO('mysql:host=localhost;dbname=waermepumpe', 'wp_user', 'Ikikulopi485');
} catch (PDOException $e) {
   print "Error!: " . $e->getMessage() . "<br/>";
   die();
}

$user = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

if(!strcasecmp($page,'logout')){
    session_destroy();
    header("Location: index.php");
    exit();
}

if(isset($user) && isset($password)) {
    $stmt = $conn->prepare('SELECT * FROM user WHERE NAME = :name');
    $stmt->bindValue(':name',$user,PDO::PARAM_STR);
    $stmt->execute();
    
    if($stmt->rowCount() == 1) {
        $res = $stmt->fetchObject();
        if(!strcmp($res->password, md5($password))) {
            $_SESSION['login_state'] = true;
            $_SESSION['user_name'] = $user;
            $login = true;
        }
    }
}

if(isset($_POST['save_1'])) {
    $c[] = array (
        'name' => 'contorller_ip',
        'value' => $_POST['contorller_ip']
    );   
    $c[] = array (
        'name' => 'controller_password',
        'value' => $_POST['controller_password']
    ); 
    $c[] = array (
        'name' => 'check_interval',
        'value' => $_POST['check_interval']
    ); 
    $c[] = array (
        'name' => 'pow_running',
        'value' => $_POST['pow_running']
    ); 
    $config = new config();
    $config->save_config($c);
}

if(isset($_POST['save_2'])) {
    $c[] = array (
        'name' => 'awattar_api_url',
        'value' => $_POST['awattar_api_url']
    );   
    $c[] = array (
        'name' => 'awattar_api_token',
        'value' => $_POST['awattar_api_token']
    ); 
    $config = new config();
    $config->save_config($c);
}

if(isset($_POST['save_3'])) {
    $c[] = array (
        'name' => 'add_pow_price',
        'value' => $_POST['add_pow_price']
    );
    $c[] = array (
        'name' => 'add_pow_price_d',
        'value' => $_POST['add_pow_price_d']
    );  
    $config = new config();
    $config->save_config($c);
}

if(!$login) {
    include 'pages/login.html';
} else {
    switch ($page){
        case 'dashboard':
            include 'pages/nav_bar.php';
            include 'pages/dashboard.php';
            include 'pages/footer.php';
            break;
        case 'history_day':
            include 'pages/nav_bar.php';
            include 'pages/history_day.php';
            include 'pages/footer.php';
            break;
        case 'history_week':
            include 'pages/nav_bar.php';
            include 'pages/history_week.php';
            include 'pages/footer.php';
            break;
        case 'history_month':
            include 'pages/nav_bar.php';
            include 'pages/history_month.php';
            include 'pages/footer.php';
            break;
        case 'history_year':
            include 'pages/nav_bar.php';
            include 'pages/history_year.php';
            include 'pages/footer.php';
            break;
        case 'settings':
            include 'pages/nav_bar.php';
            include 'pages/settings.php';
            include 'pages/footer.php';
            break;
        default:
            include 'pages/nav_bar.php';
            include 'pages/dashboard.php';
            include 'pages/footer.php';
            break;
    }
}