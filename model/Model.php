<?php

/**
 *
 */
class Model
{
    private static $loginCookieName = 'LoginView::CookieName';
    private static $loginCookiePassword = 'LoginView::CookiePassword';

    private $connection;

    // Message to view
    public $message = "";
    // Save entered username to prevent input field being empty on refresh
    public $usernameVariable = "";

    public function __construct($config)
    {
        //Database connection
        $this->connection = mysqli_connect($config['dbServerName'], $config['dbUsername'], $config['dbPassword'], $config['dbName']);
    }

    /**
     * Check sql database if a user exist
     * 
     * @return boolean Return true if user exist
     */
    public function checkUsernameInDb($username)
    {
        // Search string
        $sql = "SELECT * FROM users WHERE user_username='$username';";
        // Search database with the search string
        $result = mysqli_query($this->connection, $sql);

        // If user was found, result is larger than 0
        $resultCheck = mysqli_num_rows($result);
        if ($resultCheck > 0) {
            return true;
        }
    }

    /**
     * Save a username with a password to an sql database
     */
    public function saveUserToDb($username, $password)
    {
        // Hash password before saving it to database
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (user_username, user_pwd) VALUES ('$username', '$passwordHash');";
        mysqli_query($this->connection, $sql);
    }

    /**
     * Get at username from the database.
     * 
     * @return 
     */
    public function fetchUserFromDb($username)
    {
        // Search string with BINARY, meaning case sensitive
        $sql = "SELECT * FROM users WHERE BINARY user_username='$username';";
        $result = mysqli_query($this->connection, $sql);
        $resultCheck = mysqli_num_rows($result);

        if ($resultCheck > 0) {
            return mysqli_fetch_assoc($result);
        }
    }

    /**
     * Verify a password from a user
     * 
     * @return boolean Returns true if password verifies
     */
    public function verifyPassword($username, $password)
    {
        $userDB = $this->fetchUserFromDb($username);

        return password_verify($password, $userDB['user_pwd']);
    }

    public function validateCookies()
    {
        if (isset($_COOKIE[self::$loginCookieName]) && isset($_COOKIE[self::$loginCookiePassword])) {
            $cookieUser = $_COOKIE[self::$loginCookieName];
            $cookiePw = $_COOKIE[self::$loginCookiePassword];

            $verifyPassword = $this->verifyPassword($cookieUser, $cookiePw);

            if ($verifyPassword) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function setCookies($username, $password)
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        setcookie(self::$loginCookieName, $username);
        setcookie(self::$loginCookiePassword, $passwordHash);
    }
}
