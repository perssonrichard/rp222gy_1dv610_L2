<?php

require_once('config/config.php');
require_once('config/serverConfig.php');

class Model
{
    private $databaseConnection;

    public $message = "";
    public $usernameVariable = "";

    public function __construct()
    {
        $this->databaseConnection = mysqli_connect(ServerConfig::$dbServerName, ServerConfig::$dbUsername, ServerConfig::$dbPassword, ServerConfig::$dbName);
    }

    public function usernameExist($username)
    {
        $sqlSearchString = "SELECT * FROM users WHERE BINARY user_username='$username';";

        $result = mysqli_query($this->databaseConnection, $sqlSearchString);

        // If user was found, result is larger than 0
        $resultCheck = mysqli_num_rows($result);
        if ($resultCheck > 0) {
            return true;
        }
    }

    public function saveUserToDb($username, $password)
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $cookiePasswordHash = password_hash($password, PASSWORD_BCRYPT);

        $sqlSearchString = "INSERT INTO users (user_username, user_pwd, user_cookiePassword) VALUES ('$username', '$passwordHash', '$cookiePasswordHash');";
        mysqli_query($this->databaseConnection, $sqlSearchString);
    }

    private function getUser($username)
    {
        $sqlSearchString = "SELECT * FROM users WHERE BINARY user_username='$username';";
        
        $result = mysqli_query($this->databaseConnection, $sqlSearchString);

        // If user was found, result is larger than 0
        $resultCheck = mysqli_num_rows($result);
        if ($resultCheck > 0) {
            return mysqli_fetch_assoc($result);
        }
    }

    public function verifyPassword($username, $password)
    {
        $databaseUser = $this->getUser($username);

        return password_verify($password, $databaseUser['user_pwd']);
    }

    public function validateCookies()
    {
        // It takes a refresh for cookies to update, so check previous cookie saved in a session variable
        if ($_COOKIE[Config::$loginCookiePassword] == $_SESSION['oldCookiePassword']) {
            return true;
        } else {
            $_SESSION['manipulatedCookie'] = true;
            return false;
        }
    }

    public function rehashUserCookiePassword($username)
    {
        $databaseUser = $this->getUser($username);
        $password = $databaseUser['user_cookiePassword'];
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // It takes a refresh for cookies to update, so save previous cookie in session variable
        $_SESSION['oldCookiePassword'] = $password;

        $sqlUpdateString = "UPDATE users SET user_cookiePassword='$hashedPassword' WHERE user_username='$username';";

        mysqli_query($this->databaseConnection, $sqlUpdateString);
    }

    public function setCookies($username)
    {
        $user = $this->getUser($username);
        $password = $user['user_cookiePassword'];

        // 86400 * 30 = 24 hours
        setcookie(Config::$loginCookieName, $username, time() + (86400 * 30));
        setcookie(Config::$loginCookiePassword, $password, time() + (86400 * 30));
    }

    public function deleteCookies()
    {
        setcookie(Config::$loginCookieName, "", time() - 3600);
        setcookie(Config::$loginCookiePassword, "", time() - 3600);
    }
}
