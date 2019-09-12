<?php

class HandleDatabase
{

    public static function checkUsernameInDb($username)
    {
        include 'db.php';

        $sql = "SELECT * FROM users WHERE user_username='$username';";
        $result = mysqli_query($dbconnection, $sql);
        $resultCheck = mysqli_num_rows($result);

        if ($resultCheck > 0) {
            return true;
        }
    }

    public static function registerUserToDB($username, $password)
    {
        include 'db.php';

        // Hash password before saving it to database
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (user_username, user_pwd) VALUES ('$username', '$passwordHash');";
        mysqli_query($dbconnection, $sql);
    }

    public static function getUserFromDB($username)
    {
        include 'db.php';

        $sql = "SELECT * FROM users WHERE BINARY user_username='$username';";
        $result = mysqli_query($dbconnection, $sql);
        $resultCheck = mysqli_num_rows($result);

        if ($resultCheck > 0) {
            return mysqli_fetch_assoc($result);
        }
    }

    public static function compareUsernameAndPassword($username, $password)
    {
        $userDB = self::getUserFromDB($username);

        return password_verify($password, $userDB['user_pwd']);
    }
}
