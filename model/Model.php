<?php

require_once('config/config.php');

/**
 * Model class
 */
class Model
{
    private $connection;

    // Message sent to view
    public $message = "";
    // Save entered username to prevent input field being empty on refresh
    public $usernameVariable = "";

    /**
     * The Model constructor 
     */
    public function __construct()
    {
        //Database connection
        $this->connection = mysqli_connect(Config::$dbServerName, Config::$dbUsername, Config::$dbPassword, Config::$dbName);
    }

    /**
     * Check sql database if a user exist
     * 
     * @param string $username
     * @return boolean
     */
    public function usernameExist($username)
    {
        // SQL search string
        $sql = "SELECT * FROM users WHERE BINARY user_username='$username';";
        // Search db
        $result = mysqli_query($this->connection, $sql);

        // If user was found, result is larger than 0
        $resultCheck = mysqli_num_rows($result);
        if ($resultCheck > 0) {
            return true;
        }
    }

    /**
     * Save a username with a password and a cookie password to an sql database
     * 
     * @param string $username
     * @param string $password
     * @return void
     */
    public function saveUserToDb($username, $password)
    {
        // Hash password before saving it to database
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        // Hash password again and save it as a cookie-password
        $cookiePasswordHash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (user_username, user_pwd, user_cookiePassword) VALUES ('$username', '$passwordHash', '$cookiePasswordHash');";
        mysqli_query($this->connection, $sql);
    }

    /**
     * Fetch a username from the database.
     * 
     * @param string $username 
     * @return string[]
     */
    private function getUser($username)
    {
        // SQL search string
        $sql = "SELECT * FROM users WHERE BINARY user_username='$username';";
        // Search db
        $result = mysqli_query($this->connection, $sql);

        // If user was found, result is larger than 0
        $resultCheck = mysqli_num_rows($result);
        if ($resultCheck > 0) {
            return mysqli_fetch_assoc($result);
        }
    }

    /**
     * Verify input password with database password
     * 
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function verifyPassword($username, $password)
    {
        $userDB = $this->getUser($username);

        return password_verify($password, $userDB['user_pwd']);
    }

    /**
     * Validate username and password cookie
     * @return boolean
     */
    public function validateCookies()
    {
        // It takes a refresh for cookies to update, so check previous cookie saved in a session variable
        if ($_COOKIE[Config::$loginCookiePassword] == $_SESSION['oldCookiePassword']) {
            return true;
        } else {
            // If cookies have been modified
            $_SESSION['manipulatedCookie'] = true;
            return false;
        }
    }

    /**
     * Rehash a cookie password and save it to mysql database
     * 
     * @param string $username
     * @return void
     */
    public function rehashUserCookiePassword($username)
    {
        $user = $this->getUser($username);
        $password = $user['user_cookiePassword'];
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // It takes a refresh for cookies to update, so save previous cookie in session variable
        $_SESSION['oldCookiePassword'] = $password;

        // Update string
        $sql = "UPDATE users SET user_cookiePassword='$hashedPassword' WHERE user_username='$username';";

        mysqli_query($this->connection, $sql);
    }

    /**
     * Set username and password cookies
     * 
     * @param string $username
     * @return void
     */
    public function setCookies($username)
    {
        $user = $this->getUser($username);
        $password = $user['user_cookiePassword'];

        // Save cookies 24 hours
        setcookie(Config::$loginCookieName, $username, time() + (86400 * 30));
        setcookie(Config::$loginCookiePassword, $password, time() + (86400 * 30));
    }

    /**
     * Delete username and password cookie
     * 
     * @return void
     */
    public function deleteCookies()
    {
        setcookie(Config::$loginCookieName, "", time() - 3600);
        setcookie(Config::$loginCookiePassword, "", time() - 3600);
    }
}
