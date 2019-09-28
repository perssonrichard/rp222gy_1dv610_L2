<?php

require_once('view/LoginView.php');
require_once('view/RegisterView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('model/Model.php');
require_once('controller/Controller.php');

$model = new Model();
$controller = new Controller($model);

//CREATE OBJECTS OF THE VIEWS
$loginView = new LoginView($model);
$registerView = new RegisterView($model);
$dtv = new DateTimeView();
$view = new LayoutView($model, $controller);

// If no session
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}

if (isset($_COOKIE[Config::$loginCookiePassword]) && isset($_COOKIE[Config::$loginCookieName])) {
    $model->rehashUserCookiePassword($_COOKIE[Config::$loginCookieName]);
    $model->setCookies($_COOKIE[Config::$loginCookieName]);
}


if (isset($_COOKIE[Config::$loginCookieName]) && isset($_COOKIE[Config::$loginCookiePassword])) {
    // If no session and valid cookies
    if ($model->validateCookies() && (isset($_SESSION["loggedin"]) == false && $_SESSION["loggedin"] = true)) {
        $_SESSION['loggedinWithCookie'] = true;
    }
}

if (isset($_SESSION["loggedin"]) == false) {
    $_SESSION["loggedin"] = false;
}

if (isset($_SESSION['sessionValidationString'])) {
    $sessionValidationString = $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'];
}

if (isset($sessionValidationString)) {
    if ($sessionValidationString == $_SESSION['sessionValidationString']) {
        $_SESSION['loggedin'] = true;
    } else {
        $_SESSION['loggedin'] = false;
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // If clicking the login button
    if (isset($_POST['LoginView::Login'])) {
        if (isset($_SESSION['preventResettingMessageVar']) == false || $_SESSION['preventResettingMessageVar'] != true) {
            $controller->userLoginAttempt();
        }
    }
    // If clicking the register button
    if (isset($_POST['RegisterView::Register'])) {
        $controller->userRegisterAttempt();
    }
    // If clicking the logout button
    if (isset($_POST['LoginView::Logout']) && $_SESSION["loggedin"] == true) {
        $controller->userLogOut();
    }
}
// Render content
$view->render($_SESSION['loggedin'], $loginView, $registerView, $dtv);
