<?php

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/RegisterView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('model/Model.php');
require_once('controller/Controller.php');
$config = include('config/config.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//CREATE MODEL AND CONTROLLER
$model = new Model($config);
$controller = new Controller($model);

//CREATE OBJECTS OF THE VIEWS
$loginView = new LoginView($model);
$registerView = new RegisterView($model);
$dtv = new DateTimeView();
$view = new LayoutView($model, $controller);


if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}

if ($model->validateCookies() && (isset($_SESSION["loggedin"]) == false && $_SESSION["loggedin"] = true)) {
    $_SESSION['loggedinWithCookie'] = true;
}

if (isset($_SESSION["loggedin"]) == false) {
    $_SESSION["loggedin"] = false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // If clicking the login button
    if (isset($_POST['LoginView::Login'])) {
        $controller->userLoginAttempt();
    }
    // If clicking the register button
    if (isset($_POST['DoRegistration'])) {
        $controller->userRegisterAttempt();
    }
    // If clicking the logout button
    if (isset($_POST['LoginView::Logout']) && $_SESSION["loggedin"] == true) {
        $controller->userLogOut();
    }
}

$view->render($_SESSION['loggedin'], $loginView, $registerView, $dtv);
