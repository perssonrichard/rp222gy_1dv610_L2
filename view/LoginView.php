<?php

/**
 * Class that Handles the login view
 */
class LoginView
{
	private $model;

	// Define HTML ID's
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';

	public function __construct(Model $model)
	{
		$this->model = $model;
	}

	public function response()
	{
		// If not logged in
		if ($_SESSION["loggedin"] == false) {

			if (isset($_SESSION['showBye']) && $_SESSION['showBye'] == true) {
				$this->model->message = "Bye bye!";
				$_SESSION['showBye'] = false;
				$_SESSION['preventResendPOST'] = true;
			}

			// If trying to login with manipulated cookie
			if (isset($_SESSION['manipulatedCookie']) && $_SESSION['manipulatedCookie']) {
				$this->model->message = "Wrong information in cookies";
				$_SESSION['manipulatedCookie'] = false;
				$this->model->deleteCookies();
			}

			$response = $this->generateLoginFormHTML($this->model->message);
			return $response;
		}

		// If logged in with cookie
		if (isset($_SESSION['loggedinWithCookie']) && $_SESSION['loggedinWithCookie']) {
			$this->model->message = "Welcome back with cookie";
			$_SESSION['loggedinWithCookie'] = false;
		}
		// If logged in with "keep me logged in"
		if (isset($_SESSION['showWelcomeKeep']) && $_SESSION["showWelcomeKeep"]) {
			$this->model->message = "Welcome and you will be remembered";
			$_SESSION['showWelcomeKeep'] = false;
		}
		// If logged in
		if (isset($_SESSION["showWelcome"]) && $_SESSION["showWelcome"]) {
			$this->model->message = "Welcome";
			$_SESSION['showWelcome'] = false;
		}

		$response = $this->generateLogoutButtonHTML($this->model->message);
		return $response;
	}

	public function generateRegisterUserHTML($queryString)
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
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->model->usernameVariable . '" />

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
