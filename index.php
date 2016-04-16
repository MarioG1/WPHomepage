<?php

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

if(!$login) {
    include 'pages\login.html';
} else {
    switch ($page){
        case 'dashboard':
            include 'pages\nav_bar.php';
            include 'pages\dashboard.php';
            include 'pages\footer.php';
            break;
        case 'settings':
            include 'pages\nav_bar.php';
            include 'pages\settings.php';
            include 'pages\footer.php';
            break;
        default:
            include 'pages\nav_bar.php';
            include 'pages\dashboard.php';
            include 'pages\footer.php';
            break;
    }
}