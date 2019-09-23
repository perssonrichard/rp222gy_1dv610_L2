<?php

/**
 * Class that handles the login view
 */
class LoginView
{
	private $model;

	/**
	 * The LoginView constructor
	 * 
	 * @param Model $model
	 */
	public function __construct(Model $model)
	{
		$this->model = $model;
	}

	/**
	 * The response given on what to render
	 * 
	 * @return string Returns a html string
	 */
	public function response()
	{
		// If not logged in
		if ($_SESSION["loggedin"] == false) {
			$response = $this->notLoggedIn();
		} else {
			$response = $this->loggedIn();
		}
		return $response;
	}

	/**
	 * Called by response function if user is logged in
	 * 
	 * @return string Returns a HTML string
	 */
	private function loggedIn()
	{
		// If logged in with cookie
		if (isset($_SESSION['loggedinWithCookie']) && $_SESSION['loggedinWithCookie']) {
			$this->model->message = "Welcome back with cookie";

			$_SESSION['loggedinWithCookie'] = false;
		}
		// If logged in with "keep me logged in"
		if (isset($_SESSION['showWelcomeKeep']) && $_SESSION["showWelcomeKeep"]) {
			$this->model->message = "Welcome and you will be remembered";

			$_SESSION['showWelcomeKeep'] = false;
			$_SESSION['preventResettingVar'] = true;
		}
		// If logged in
		if (isset($_SESSION["showWelcome"]) && $_SESSION["showWelcome"]) {
			$this->model->message = "Welcome";

			$_SESSION['showWelcome'] = false;
			$_SESSION['preventResettingVar'] = true;
		}

		return $this->generateLogoutButtonHTML($this->model->message);
	}

	/**
	 * Called from the response function when user is not logged in
	 * 
	 * @return string Returns a html string
	 */
	private function notLoggedIn()
	{
		// If registered new user
		if (isset($_SESSION['registeredNewUser']) && $_SESSION['registeredNewUser'] == true) {
			$this->model->message = "Registered new user.";
			$this->model->usernameVariable = $_SESSION['registeredNewUserName'];

			$_SESSION['registeredNewUser'] = false;
			$_SESSION['preventResendSessionVar'] = true;
		}

		// If recently logged out
		if (isset($_SESSION['showBye']) && $_SESSION['showBye'] == true) {
			$this->model->message = "Bye bye!";

			$_SESSION['showBye'] = false;
			$_SESSION['preventResendSessionVar'] = true;
		}

		// If trying to login with manipulated cookie
		if (isset($_SESSION['manipulatedCookie']) && $_SESSION['manipulatedCookie']) {
			$this->model->message = "Wrong information in cookies";
			$_SESSION['manipulatedCookie'] = false;
			$this->model->deleteCookies();
		}

		return $this->generateLoginFormHTML($this->model->message);
	}

	/**
	 * Generates a HTML <a>-tag
	 * 
	 * @param string $queryString A query string on where to send the user when clicking the link
	 * @return string Returns a HTML <a>-tag
	 */
	public function generateRegisterUserHTML($queryString)
	{
		return '<a href="?' . $queryString . '" name="register">Register a new user</a>';
	}

	/**
	 * Generate HTML code on the output buffer for the logout button
	 * @param string $message output message
	 * @return string Returns a HTML <form>-tag
	 */
	private function generateLogoutButtonHTML($message)
	{
		return '
			<form  method="post" >
				<p id="' . Config::$loginMessage . '">' . $message . '</p>
				<input type="submit" name="' . Config::$loginLogout . '" value="logout"/>
			</form>
		';
	}

	/**
	 * Generate HTML code on the output buffer for the logout button
	 * @param string $message output message
	 * @return string Returns a HTML <form>-tag
	 */
	private function generateLoginFormHTML($message)
	{
		return '
			<form method="post" > 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . Config::$loginMessage . '">' . $message . '</p>
					
					<label for="' . Config::$loginName . '">Username :</label>
					<input type="text" id="' . Config::$loginName . '" name="' . Config::$loginName . '" value="' . $this->model->usernameVariable . '" />

					<label for="' . Config::$loginPassword . '">Password :</label>
					<input type="password" id="' . Config::$loginPassword . '" name="' . Config::$loginPassword . '" />

					<label for="' . Config::$loginKeep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . Config::$loginKeep . '" name="' . Config::$loginKeep . '" />
					
					<input type="submit" name="' . Config::$loginLogin . '" value="login" />
				</fieldset>
			</form>
		';
	}
}
