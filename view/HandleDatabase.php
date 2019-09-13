<?php

/**
 * Handles incoming requests to a sql database.
 */
class HandleDatabase
{

    /**
     * Check sql database if a user exists
     * 
     * @return boolean Return true if user exists
     */
    public static function checkUsernameInDb($username)
    {
        include 'db.php';

        // Search string
        $sql = "SELECT * FROM users WHERE user_username='$username';";
        // Search database with the search string
        $result = mysqli_query($dbconnection, $sql);
        
        // If user was found, result is larger than 0
        $resultCheck = mysqli_num_rows($result);
        if ($resultCheck > 0) {
            return true;
        }
    }

    /**
     * Save a username with a password to an sql database
     */
    public static function saveUserToDB($username, $password)
    {
        include 'db.php';

        // Hash password before saving it to database
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (user_username, user_pwd) VALUES ('$username', '$passwordHash');";
        mysqli_query($dbconnection, $sql);
    }

    /**
     * Get at username from the database.
     * 
     * @return 
     */
    public static function getUserFromDB($username)
    {
        include 'db.php';

        // Search string with BINARY, meaning case sensitive
        $sql = "SELECT * FROM users WHERE BINARY user_username='$username';";
        $result = mysqli_query($dbconnection, $sql);
        $resultCheck = mysqli_num_rows($result);

        if ($resultCheck > 0) {
            return mysqli_fetch_assoc($result);
        }
    }

    /**
     * Compare a username from the database with a user submitted password.
     * 
     * @return boolean Returns true if password verifies
     */
    public static function verifyUsernameAndPassword($username, $password)
    {
        $userDB = self::getUserFromDB($username);

        return password_verify($password, $userDB['user_pwd']);
    }
}
