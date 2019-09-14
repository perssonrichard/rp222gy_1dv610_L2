<?php

class RegisterView
{
    private $model;

    // Define HTML ID's
    private static $messageId = 'RegisterView::Message';
    private static $name = 'RegisterView::UserName';
    private static $password = 'RegisterView::Password';
    private static $registration = 'DoRegistration';


    public function __construct(Model $model)
    {
        $this->model = $model;
    }

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
					<p id="' . self::$messageId . '">' . $message . '</p>
					<label for="' . self::$name . '">Username :</label>
					<input type="text" size="20" name="' . self::$name . '" id="' . self::$name . '" value="' . $this->model->usernameVariable . '">
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
