<?php

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/RegisterView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');
require_once('model/Model.php');
require_once('controller/Controller.php');

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//CREATE MODEL AND CONTROLLER
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

// If username cookie and password cookie exists
if (isset($_COOKIE[Config::$loginCookiePassword]) && isset($_COOKIE[Config::$loginCookieName])) {
    // Rehash password every GET
    $model->rehashUserCookiePassword($_COOKIE[Config::$loginCookieName]);
    $model->setCookies($_COOKIE[Config::$loginCookieName]);
}


// If cookies are set
if (isset($_COOKIE[Config::$loginCookieName]) && isset($_COOKIE[Config::$loginCookiePassword])) {
    // If no session and valid cookies
    if ($model->validateCookies() && (isset($_SESSION["loggedin"]) == false && $_SESSION["loggedin"] = true)) {
        $_SESSION['loggedinWithCookie'] = true;
    }
}

// Set logged in to false
if (isset($_SESSION["loggedin"]) == false) {
    $_SESSION["loggedin"] = false;
}

// If sessionValidation session is set
if (isset($_SESSION['sessionValidationString'])) {
    // Set validation string to IP and user Agent
    $sessionValidationString = $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'];
} else {
    $sessionValidationString = "";
}

// If sessionValidationString is set
if ($sessionValidationString !== "") {
    // Check if session belongs to the right owner, i.e IP + UserAgent
    if ($sessionValidationString != $_SESSION['sessionValidationString']) {
        $_SESSION['loggedin'] = false;
    } else {
        $_SESSION['loggedin'] = true;
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // If clicking the login button
    if (isset($_POST['LoginView::Login'])) {
        // Prevent trying to resend POST
        if (isset($_SESSION['preventResettingVar']) == false || $_SESSION['preventResettingVar'] != true) {
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
