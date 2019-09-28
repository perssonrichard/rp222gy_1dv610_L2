<?php

require_once('config/config.php');

class Controller
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function userLoginAttempt()
    {
        // Save username input to usernameValue variable. This is to prevent user input to dissapear on reload
        isset($_POST[Config::$loginName]) ? $this->model->usernameVariable = $_POST[Config::$loginName] : '';

        if ($this->loginInputIsCorrect()) {
            $this->successfulLogin();
        }
    }

    private function loginInputIsCorrect()
    {
        $verifyPassword = $this->model->verifyPassword($_POST[Config::$loginName], $_POST[Config::$loginPassword]);
        $login = true;

        if (empty($_POST[Config::$loginName])) {
            $this->model->message .= "Username is missing";
            $login = false;
        } else if (empty($_POST[Config::$loginPassword])) {
            $this->model->message .= "Password is missing";
            $login = false;
        } else if ($verifyPassword == false) {
            $this->model->message .= "Wrong name or password";
            $login = false;
        }

        return $login;
    }

    private function successfulLogin()
    {
        // Save cookie if "keep me logged in" is checked
        if (isset($_POST[Config::$loginKeep])) {
            $this->model->setCookies($_POST[Config::$loginName]);

            $_SESSION['showWelcomeKeep'] = true;
        } else {
            $_SESSION['showWelcome'] = true;
        }

        $_SESSION['loggedin'] = true;

        $ip = $_SERVER['REMOTE_ADDR'];
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['sessionValidationString'] = $ip . $browser;

        header(Config::$redirectUrl);
        exit();
    }

    public function userRegisterAttempt()
    {
        // Save username input to usernameValue variable. This is to prevent user input to dissapear on reload
        isset($_POST[Config::$registerName]) ? $this->model->usernameVariable = strip_tags($_POST[Config::$registerName]) : '';

        if ($this->registrationInputIsCorrect()) {
            $this->successfulRegistration()();
        }
    }

    private function registrationInputIsCorrect()
    {
        $registration = true;

        if (empty($_POST[Config::$registerName]) || strlen($_POST[Config::$registerName]) < 3) {
            $this->model->message .= "Username has too few characters, at least 3 characters.<br>";
            $registration = false;
        }
        if ($_POST[Config::$registerName] != strip_tags($_POST[Config::$registerName])) {
            $this->model->message .= "Username contains invalid characters.<br>";
            $registration = false;
        }
        if (empty($_POST[Config::$registerPassword]) || strlen($_POST[Config::$registerPassword]) < 6) {
            $this->model->message .= "Password has too few characters, at least 6 characters.<br>";
            $registration = false;
        }
        if ($this->model->usernameExist($_POST[Config::$registerName])) {
            $this->model->message .= "User exists, pick another username.<br>";
            $registration = false;
        }
        if ($_POST[Config::$registerPassword] != $_POST[Config::$registerRepeatPassword]) {
            $this->model->message = "Passwords do not match.<br>";
            $registration = false;
        }

        return $registration;
    }

    private function successfulRegistration()
    {
        $this->model->saveUserToDb($_POST[Config::$registerName], $_POST[Config::$registerPassword]);

        $_SESSION['registeredNewUser'] = true;
        $_SESSION['registeredNewUserName'] = $_POST[Config::$registerName];

        header(Config::$redirectUrl);
        exit();
    }

    public function userLogOut()
    {
        if (isset($_COOKIE[Config::$loginCookieName]) && isset($_COOKIE[Config::$loginCookiePassword])) {
            $this->model->deleteCookies();
        }

        // Destroy and start a new session to be able to set new session variable
        session_destroy();
        session_start();

        $_SESSION['showBye'] = true;

        header(Config::$redirectUrl);
        exit();
    }
}
