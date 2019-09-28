<?php

class RegisterView
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * The response on what to render
     */
    public function response()
    {
        $response = $this->generateRegisterFormHTML($this->model->message);

        return $response;
    }

    public function generateBackToLoginHTML()
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
					<p id="' . Config::$registerMessage . '">' . $message . '</p>
					<label for="' . Config::$registerName . '">Username :</label>
					<input type="text" size="20" name="' . Config::$registerName . '" id="' . Config::$registerName . '" value="' . $this->model->usernameVariable . '">
					<br>
					<label for="' . Config::$registerPassword . '">Password  :</label>
					<input type="password" size="20" name="' . Config::$registerPassword . '" id="' . Config::$registerPassword . '" value="">
					<br>
					<label for="' . Config::$registerPassword . 'Repeat">Repeat password  :</label>
					<input type="password" size="20" name="' . Config::$registerPassword . 'Repeat" id="' . Config::$registerPassword . 'Repeat" value="">
					<br>
					<input id="submit" type="submit" name="' . Config::$registerRegistration . '" value="Register">
					<br>
				</fieldset>
            </form>
            ';
    }
}
