<?php

class LoginView
{
	private $model;

	public function __construct(Model $model)
	{
		$this->model = $model;
	}

	/**
	 * The response given on what to render
	 */
	public function response()
	{
		if ($_SESSION["loggedin"]) {
			$this->setLoggedInMessage();
			return $this->generateLogoutButtonHTML($this->model->message);
		} else {
			$this->setNotLoggedInMessage();
			return $this->generateLoginFormHTML($this->model->message);
		}
	}

	private function setLoggedInMessage()
	{
		if (isset($_SESSION['loggedinWithCookie']) && $_SESSION['loggedinWithCookie']) {
			$this->model->message = "Welcome back with cookie";

			$_SESSION['loggedinWithCookie'] = false;
		}
		if (isset($_SESSION['showWelcomeKeep']) && $_SESSION["showWelcomeKeep"]) {
			$this->model->message = "Welcome and you will be remembered";

			$_SESSION['showWelcomeKeep'] = false;
		}
		if (isset($_SESSION["showWelcome"]) && $_SESSION["showWelcome"]) {
			$this->model->message = "Welcome";

			$_SESSION['showWelcome'] = false;
		}

		$_SESSION['preventResettingMessageVar'] = true;
	}

	private function setNotLoggedInMessage()
	{
		if (isset($_SESSION['registeredNewUser']) && $_SESSION['registeredNewUser'] == true) {
			$this->model->message = "Registered new user.";
			$this->model->usernameVariable = $_SESSION['registeredNewUserName'];

			$_SESSION['registeredNewUser'] = false;
		}

		// If recently logged out
		if (isset($_SESSION['showBye']) && $_SESSION['showBye'] == true) {
			$this->model->message = "Bye bye!";

			$_SESSION['showBye'] = false;
		}

		if (isset($_SESSION['manipulatedCookie']) && $_SESSION['manipulatedCookie']) {
			$this->model->message = "Wrong information in cookies";
			$_SESSION['manipulatedCookie'] = false;
			$this->model->deleteCookies();
		}

		$_SESSION['preventResettingMessageVar'] = true;
	}

	public function generateRegisterUserHTML($queryString)
	{
		return '<a href="?' . $queryString . '" name="register">Register a new user</a>';
	}

	private function generateLogoutButtonHTML($message)
	{
		return '
			<form  method="post" >
				<p id="' . Config::$loginMessage . '">' . $message . '</p>
				<input type="submit" name="' . Config::$loginLogout . '" value="logout"/>
			</form>
		';
	}


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
