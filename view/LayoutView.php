<?php

class LayoutView
{
  private $model;
  private $controller;
  private $registerQueryString = 'register';

  public function __construct(Model $model, Controller $controller)
  {
    $this->model = $model;
    $this->controller = $controller;
  }

  public function render($isLoggedIn, LoginView $loginView, RegisterView $registerView, DateTimeView $dtv)
  {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->linkToRender($isLoggedIn, $loginView, $registerView) . '
          ' . $this->renderIsLoggedIn($isLoggedIn) . '
          
          <div class="container">
              ' . $this->viewToRender($loginView, $registerView) . '
              ' . $dtv->show() . '
          </div>
         </body>
      </html>
    ';
  }

  /**
   * Decides what link to render
   */
  private function linkToRender($isLoggedIn, LoginView $loginView, RegisterView $registerView)
  {
    if (isset($_GET[$this->registerQueryString])) {
      return $registerView->generateBackToLoginHTML();
    } else if ($isLoggedIn == false) {
      return $loginView->generateRegisterUserHTML($this->registerQueryString);
    }
  }

  /**
   * Decides what view to render
   */
  private function viewToRender(LoginView $loginView, RegisterView $registerView)
  {
    if (isset($_GET[$this->registerQueryString])) {
      return $registerView->response();
    } else {
      return $loginView->response();
    }
  }

  private function renderIsLoggedIn($isLoggedIn)
  {
    if ($isLoggedIn) {
      return '<h2>Logged in</h2>';
    } else {
      return '<h2>Not logged in</h2>';
    }
  }
}
