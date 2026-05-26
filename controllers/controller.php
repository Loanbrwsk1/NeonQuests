<?php

function Display404()
{
    require "views/404.php";
}

function DisplayLoginPage()
{
    require "views/login.php";
}

function CheckConnect()
{
    require "models/model.php";
    if(Connect()){
        header("Location: /home");
        exit();
    }
    else{
        header("Location: /login");
        exit();
    }
}

function DisplayHome()
{
    require_once "models/model.php";
    if($_SESSION["pseudo"] != "Invité"){
        GetDisabledButton();
    }
    if($_SESSION["progress"] == 100){
        header("Location: /finished");
        exit();
    }
    require "views/home.php";
}

function DisplayCreate()
{
    require "views/create-account.php";
}

function DisplayFinished()
{
    require "views/finished.php";
}

function CheckCreate()
{
    require_once "models/model.php";

    $username = htmlspecialchars($_POST['pseudo']);
    $password = htmlspecialchars($_POST['password']);
    $confirm_password = htmlspecialchars($_POST['confirm-password']);

    if($username != "Invité" && $password == $confirm_password && !empty($username) && !empty($password)){
        Create();
        header("location: /introduction");
        exit();
    }
    else if($username == "Invité"){
        $_SESSION["error"] = "Vous ne pouvez pas utiliser le pseudo <Invité>";
    }
    else if($password != $confirm_password){
        $_SESSION['error'] = "Les mots de passe ne correspondent pas !";
    }
    else if(empty($username)){
        $_SESSION["error"] = "Le pseudo ne peut pas être vide !";
    }
    else if(empty($password)){
        $_SESSION["error"] = "Le mot de passe ne peut pas être vide !";
    }
    header("Location: /create");
    exit();
}

function LogOut()
{
    session_start();
    unset($_SESSION['pseudo']);
    session_destroy();
    setcookie("token", "", time() - 3600);
    unset($_COOKIE["token"]);
    header("location: /login");
    exit();
}

function DisplayAccount()
{
    require "views/account.php";
}

function displayIntroduction()
{
    require "views/intro.php";
}

function DisplayIntro(string $theme)
{
    require "views/questions/$theme/intro.php";
}

function DisplayQuestion(string $theme, string $question)
{
    require_once "models/model.php";
    if($_SESSION["pseudo"] != "Invité"){
        
    }
    if(CanDoQuestion($theme, $question)){
        require "views/questions/$theme/$question.php";
    }
    else{
        header("Location: /home");
        exit();
    }
}

function DisplayThemeFinished()
{
    require "views/theme-finished.php";
}

function CheckDelete()
{
    require_once "models/model.php";
    if($_SESSION["pseudo"] != "Invité"){
        Delete();
        unset($_SESSION["pseudo"]);
        setcookie("token", "", time() - 3600);
        unset($_COOKIE["token"]);
        header("Location: /login");
        exit();
    }
    else{
        $_SESSION['error'] = "Vous ne pouvez pas supprimé le compte Invité !";
        header("Location: /account");
        exit();
    }
}

function CheckChangePwd()
{
    require_once "models/model.php";

    $password = htmlspecialchars($_POST["password"]);
    $confirm_password = htmlspecialchars($_POST["confirm-password"]);

    if($_SESSION["pseudo"] != "Invité" && $password == $confirm_password){
        ChangePwd();
    }
    else if($_SESSION["pseudo"] == "Invité"){
        $_SESSION['error'] = "Vous ne pouvez pas changer le mot de passe du compte Invité !";
    }
    else if($password != $confirm_password){
        $_SESSION['error'] = "Les mots de passe ne correspondent pas !";
    }
    header("Location: /account");
    exit();
}

function CheckAnswer(string $theme, string $question)
{
    require_once "models/model.php";
    $result = CheckAnswerDB($theme, $question);
    if($result){
        if($question <= 4){
            $question += 1;
            header("Location: /question/$theme/$question");
            exit();
        }
        else{
            header("Location: /theme-finished");
            exit();
        }
    }
    else{
        header("Location: /question/$theme/$question");
        exit();
    }
}

function AutoLogin()
{
    require_once "models/model.php";
    if(GetAutoLogin()){
        header("Location: /home");
        exit();
    }
    else{
        LogOut();
    }
}

function RedirectHome()
{
    header("Location: /home");
    exit();
}