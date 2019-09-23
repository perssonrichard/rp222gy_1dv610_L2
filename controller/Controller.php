<?php

require_once('config/config.php');

/**
 * Controller class
 */
class Controller
{
    // Reference to Model class
    private $model;

    /**
     * Controller constructor
     * 
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Triggered when user is trying to log in
     * 
     * @return void
     */
    public function userLoginAttempt()
    {
        // Save username input to usernameValue variable if it exist. This is to prevent user input to dissapear on reload
        isset($_POST[Config::$loginName]) ? $this->model->usernameVariable = $_POST[Config::$loginName] : '';

        // Check db for valid combination of username and password
        $validateLogin = $this->model->verifyPassword($_POST[Config::$loginName], $_POST[Config::$loginPassword]);

        // Check for invalid input
        if (empty($_POST[Config::$loginName])) {
            $this->model->message .= "Username is missing";
        } else if (empty($_POST[Config::$loginPassword])) {
            $this->model->message .= "Password is missing";
        } else if ($validateLogin == false) {
            $this->model->message .= "Wrong name or password";
        }

        if ($validateLogin) {
            $this->successfulLogin();
        }
    }

    /**
     * Called when a user's login attempt is successfull
     * 
     * @return void
     */
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

        // Save ip and user agent to session to prevent hijacking
        $ip = $_SERVER['REMOTE_ADDR'];
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['sessionValidationString'] = $ip . $browser;

        // Redirect
        header(Config::$redirectUrl);
        exit();
    }

    /**
     * Called when a register attempt is successful
     * 
     * @return void
     */
    private function successfulRegister()
    {
        // Save user
        $this->model->saveUserToDb($_POST[Config::$registerName], $_POST[Config::$registerPassword]);

        $_SESSION['registeredNewUser'] = true;
        $_SESSION['registeredNewUserName'] = $_POST[Config::$registerName];

        // Redirect
        header(Config::$redirectUrl);
        exit();
    }

    /**
     * Triggered when user is trying to register
     * 
     * @return void
     */
    public function userRegisterAttempt()
    {
        // Save username input to usernameValue variable if it exist. This is to prevent user input to dissapear on reload
        isset($_POST[Config::$registerName]) ? $this->model->usernameVariable = strip_tags($_POST[Config::$registerName]) : '';

        $registration = true;

        // Check for invalid input
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

        // If registration is successful
        if ($registration) {
            $this->successfulRegister();
        }
    }

    /**
     * Triggered when user is trying to log out
     * 
     * @return void
     */
    public function userLogOut()
    {
        // Delete cookies if they exist
        if (isset($_COOKIE[Config::$loginCookieName]) && isset($_COOKIE[Config::$loginCookiePassword])) {
            $this->model->deleteCookies();
        }

        // Destroy and start a new session to be able to set new session variable
        session_destroy();
        session_start();

        $_SESSION['showBye'] = true;

        // Redirect
        header(Config::$redirectUrl);
        exit();
    }
}
