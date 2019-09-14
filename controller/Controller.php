<?php

class Controller
{
    private static $loginName = 'LoginView::UserName';
    private static $loginPassword = 'LoginView::Password';
    private static $loginKeep = 'LoginView::KeepMeLoggedIn';
    private static $registerRepeatPassword = 'RegisterView::PasswordRepeat';
    private static $registerName = 'RegisterView::UserName';
    private static $registerPassword = 'RegisterView::Password';

    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function userLoginAttempt()
    {
        // Save username input to usernameValue variable if it exist
        isset($_POST[self::$loginName]) ? $this->model->usernameVariable = $_POST[self::$loginName] : '';

        // Check db for valid combination of username and password.
        $validateLogin = $this->model->verifyPassword($_POST[self::$loginName], $_POST[self::$loginPassword]);

        // Check for invalid input.
        if (empty($_POST[self::$loginName])) {
            $this->model->message .= "Username is missing";
        } else if (empty($_POST[self::$loginPassword])) {
            $this->model->message .= "Password is missing";
        } else if ($validateLogin == false) {
            $this->model->message .= "Wrong name or password";
        }

        if ($validateLogin) {
            // Save cookie if "keep me logged in" is checked
            if (isset($_POST[self::$loginKeep])) {
                $this->model->setCookies($_POST[self::$loginName], $_POST[self::$loginPassword]);
            }

            $_SESSION['loggedin'] = true;

            $this->preventResendPOST('showWelcome');

            //Redirect to hardcoded link for testing purposes.
            header('Location: https://perssonrichard.com/1dv610/index.php');
            // header('Location: index.php');
            exit();
        }
    }

    public function userRegisterAttempt()
    {
        // Save username input to usernameValue variable if it exist
        isset($_POST[self::$registerName]) ? $this->model->usernameValue = $_POST[self::$registerName] : '';

        if (empty($_POST[self::$registerName])) {
            $this->model->message .= "Username has too few characters, at least 3 characters.<br>";
        }

        if (empty($_POST[self::$registerPassword])) {
            $this->model->message .= "Password has too few characters, at least 6 characters.<br>";
        }
        if ($this->model->checkUsernameInDb($_POST[self::$registerName])) {
            $this->model->message .= "User exist, pick another username.<br>";
        }

        if ($_POST[self::$registerPassword] == $_POST[self::$registerRepeatPassword]) {
            $this->model->saveUserToDb($_POST[self::$registerName], $_POST[self::$registerPassword]);
        }
    }

    public function userLogOut()
    {
        session_destroy();
        // Start a new session to be able to set variable.
        session_start();

        $this->preventResendPOST('showBye');

        //Redirect to hardcoded link for testing purposes.
        header('Location: https://perssonrichard.com/1dv610/index.php');
        // header('Location: index.php');
        exit();
    }

    private function preventResendPOST($sessionStringVariable)
    {
        if (isset($_SESSION['preventResendPOST']) == false) {
            $_SESSION['preventResendPOST'] = true;
        }

        if ($_SESSION['preventResendPOST']) {
            $_SESSION[$sessionStringVariable] = true;
            $_SESSION['preventResendPOST'] = false;
        }
    }
}
