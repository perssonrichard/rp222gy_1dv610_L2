<?php

class Config
{
    /**
     * REMOTE SERVER
     */
    // public static $dbServerName = "localhost";
    // public static $dbUsername = "persglgr_root";
    // public static $dbPassword = "s.Ki~F@zE@6L";
    // public static $dbName = "persglgr_loginsystem";

    /**
     * LOCAL SERVER
     */
    public static $dbServerName = "localhost";
    public static $dbUsername = "root";
    public static $dbPassword = "";
    public static $dbName = "loginsystem";

    // Define login view HTML ID's
    public static $loginCookieName = 'LoginView::CookieName';
    public static $loginCookiePassword = 'LoginView::CookiePassword';
    public static $loginName = 'LoginView::UserName';
    public static $loginPassword = 'LoginView::Password';
    public static $loginKeep = 'LoginView::KeepMeLoggedIn';
    public static $loginLogin = 'LoginView::Login';
    public static $loginLogout = 'LoginView::Logout';
    public static $loginMessage = 'LoginView::Message';

    // Define register view HTML ID's
    public static $registerRepeatPassword = 'RegisterView::PasswordRepeat';
    public static $registerName = 'RegisterView::UserName';
    public static $registerPassword = 'RegisterView::Password';
    public static $registerMessage = 'RegisterView::Message';
    public static $registerRegistration = 'RegisterView::Register';

    // Remote server
    // public static $redirectUrl = 'Location: https://perssonrichard.com/1dv610/index.php';

    // Local server
    public static $redirectUrl = 'Location: index.php';
}
