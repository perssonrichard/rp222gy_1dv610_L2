<?php

require_once('HandleDatabase.php');

class RegisterView
{
    private static $messageId = 'RegisterView::Message';
    private static $name = 'RegisterView::UserName';
    private static $password = 'RegisterView::Password';
    private static $repeatPassword = 'RegisterView::PasswordRepeat';
    private static $registration = 'DoRegistration';

    private $usernameValue = '';

    public function response()
    {
        $message = '';

        // If getting a POST request
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // If clicking the register button
            if (isset($_POST[self::$registration])) {

                // Save username input to usernameValue variable
                isset($_POST[self::$name]) ? $this->usernameValue = $_POST[self::$name] : '';

                if (empty($_POST[self::$name])) {
                    $message .= "Username has too few characters, at least 3 characters.<br>";
                }

                if (empty($_POST[self::$password])) {
                    $message .= "Password has too few characters, at least 6 characters.<br>";
                }
                if (HandleDatabase::checkUsernameInDb($_POST[self::$name])) {
                    $message .= "User exist, pick another username.<br>";
                }

                if ($_POST[self::$password] == $_POST[self::$repeatPassword]) {
                    HandleDatabase::registerUserToDB($_POST[self::$name], $_POST[self::$password]);
                }
            }
        }

        $response = $this->generateRegisterFormHTML($message);

        return $response;
    }

    public function generateBackToLogin()
    {
        return '<a href="?">Back to login</a>';
    }

    private function generateRegisterFormHTML($message)
    {
        return '
        <h2>Register new user</h2>
        <form action="?register" method="post" enctype="multipart/form-data">
				<fieldset>
				<legend>Register a new user - Write username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					<label for="' . self::$name . '">Username :</label>
					<input type="text" size="20" name="' . self::$name . '" id="' . self::$name . '" value="' . $this->usernameValue . '">
					<br>
					<label for="' . self::$password . '">Password  :</label>
					<input type="password" size="20" name="' . self::$password . '" id="' . self::$password . '" value="">
					<br>
					<label for="' . self::$password . 'Repeat">Repeat password  :</label>
					<input type="password" size="20" name="' . self::$password . 'Repeat" id="' . self::$password . 'Repeat" value="">
					<br>
					<input id="submit" type="submit" name="' . self::$registration . '" value="Register">
					<br>
				</fieldset>
            </form>
            ';
    }
}
