<?php

require_once('HandleDatabase.php');

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

	private $usernameValue = '';

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

				// Save username input to usernameValue variable if it exist
				isset($_POST[self::$name]) ? $this->usernameValue = $_POST[self::$name] : '';

				// Check db for valid combination of username and password.
				$validateLogin = HandleDatabase::compareUsernameAndPassword($_POST[self::$name], $_POST[self::$password]);

				// Check for invalid input.
				if (empty($_POST[self::$name])) {
					$message .= "Username is missing";
				} else if (empty($_POST[self::$password])) {
					$message .= "Password is missing";
				} else if ($validateLogin == false) {
					$message .= "Wrong name or password";
				}

				// If login information is valid
				if ($validateLogin) {
					// Save cookie if "keep me logged in" is checked
					if (isset($_POST[self::$keep])) {
						$_SESSION['loggedin'] = true;

						$this->setCookies($_POST[self::$name], $_POST[self::$password]);

						//Redirect to hardcoded link for testing purposes.
						//header('Location: https://perssonrichard.com/1dv610/index.php');
						header('Location: index.php');
						exit;

					} else {
						$_SESSION['loggedin'] = true;

						//Redirect to hardcoded link for testing purposes.
						//header('Location: https://perssonrichard.com/1dv610/index.php');
						header('Location: index.php');
						exit;
					}
				}
			}
		}

		if ($_SESSION["loggedin"] == false) {
			$response = $this->generateLoginFormHTML($message);
			return $response;
		}
		if ($_SESSION["loggedin"]) {
			$response = $this->generateLogoutButtonHTML($message);
			return $response;
		}
	}

	private function setCookies($username, $password)
	{
		$passwordHash = password_hash($password, PASSWORD_BCRYPT);

		setcookie(self::$cookieName, $username);
		setcookie(self::$cookiePassword, $passwordHash);
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
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->usernameValue . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}
}
