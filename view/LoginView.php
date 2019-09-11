<?php

class LoginView
{
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return void BUT writes to standard output and cookies!
	 */
	public function response()
	{
		$message = '';

		// If getting a POST request
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			// If clicking the login button
			if (isset($_POST[self::$login])) {

				if (empty($_POST[self::$name])) {
					$message .= "Username is missing";
				} else if (empty($_POST[self::$password])) {
					$message .= "Password is missing";
				}
			}
		}

		$response = $this->generateLoginFormHTML($message);
		//$response .= $this->generateLogoutButtonHTML($message);
		return $response;
	}

	public function generateRegisterUser($queryString)
	{
		return '<a href="?' . $queryString . '" name="register">Register a new user</a>';
	}

	/**
	 * Generate HTML code on the output buffer for the logout button
	 * @param $message, String output message
	 * @return  void, BUT writes to standard output!
	 */
	private function generateLogoutButtonHTML($message)
	{
		return '
			<form  method="post" >
				<p id="' . self::$messageId . '">' . $message . '</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}

	/**
	 * Generate HTML code on the output buffer for the logout button
	 * @param $message, String output message
	 * @return  void, BUT writes to standard output!
	 */
	private function generateLoginFormHTML($message)
	{
		return '
			<form method="post" > 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}

	//CREATE GET-FUNCTIONS TO FETCH REQUEST VARIABLES
	private function getRequestUserNameFromDB($username)
	{
		require_once('./db.php');

		$sql = "SELECT * FROM users WHERE user_username='$username';";
		$result = mysqli_query($connection, $sql);
		$resultCheck = mysqli_num_rows($result);

		if ($resultCheck > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				echo  $row['user_username'];
			}
		}
	}
}
