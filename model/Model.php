<?php

/**
 *
 */
class Model
{
    public static $loginCookieName = 'LoginView::CookieName';
    public static $loginCookiePassword = 'LoginView::CookiePassword';

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
        // Hash password again and save it as a cookie-password
        $cookiePasswordHash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (user_username, user_pwd, user_cookiePassword) VALUES ('$username', '$passwordHash', '$cookiePasswordHash');";
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

            if ($cookiePw == $_SESSION['oldCookiePassword']) {
                return true;
            } else {
                $_SESSION['manipulatedCookie'] = true;
                return false;
            }
        } else {
            return false;
        }
    }

    public function rehashUserCookiePassword($username)
    {
        $user = $this->fetchUserFromDb($username);
        $pwCookie = $user['user_cookiePassword'];
        $rehashedPw = password_hash($pwCookie, PASSWORD_BCRYPT);

        $_SESSION['oldCookiePassword'] = $pwCookie;
        $_SESSION['newCookiePassword'] = $rehashedPw;

        // Update string
        $sql = "UPDATE users SET user_cookiePassword='$rehashedPw' WHERE user_username='$username';";

        mysqli_query($this->connection, $sql);
    }

    public function setCookies($username)
    {
        $user = $this->fetchUserFromDb($username);
        $pwCookie = $user['user_cookiePassword'];

        setcookie(self::$loginCookieName, $username, time() + (86400 * 30));
        setcookie(self::$loginCookiePassword, $pwCookie, time() + (86400 * 30));
    }

    public function deleteCookies()
    {
        if (isset($_COOKIE[self::$loginCookieName]) && isset($_COOKIE[self::$loginCookiePassword])) {
            setcookie(self::$loginCookieName, "", time() - 3600);
            setcookie(self::$loginCookiePassword, "", time() - 3600);
        }
    }
}
