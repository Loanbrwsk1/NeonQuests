<?php
session_start();

require "controllers/controller.php";

if (!isset($_SESSION["pseudo"]) && empty($_SESSION["pseudo"]) && isset($_COOKIE["token"]) && !empty($_COOKIE["token"])) {
    AutoLogin();
}

if (!isset($_GET["page"]) && empty($_GET["page"]) && isset($_SESSION["pseudo"]) && !empty($_SESSION["pseudo"]) && !isset($_GET["action"]) && empty($_GET["action"]) && !isset($_POST["action"]) && empty($_POST["action"])) {
    RedirectHome();
}

if (isset($_GET["page"]) && !empty($_GET["page"]) && isset($_SESSION["pseudo"]) && !empty($_SESSION["pseudo"])) {
    $page = htmlspecialchars($_GET["page"]);
    if(($page == "login" || $page == "create") && $_SESSION["pseudo"] != "Invité"){
        RedirectHome();
    }
    if ($page == "question" && (isset($_GET["theme"]) && !empty($_GET["theme"]) && isset($_GET["question"]) && !empty($_GET["question"]))) {
        $theme = htmlspecialchars($_GET["theme"]);
        $question = htmlspecialchars($_GET["question"]);
    } else if ($page == "intro" && (isset($_GET["theme"]) && !empty($_GET["theme"]))) {
        $theme = htmlspecialchars($_GET["theme"]);
    }
} else {
    $page = "login";
}

if(isset($_POST["action"]) && !empty($_POST["action"])){
    $action = htmlspecialchars($_POST["action"]);
    if ($action == "check-connect") {
        CheckConnect();
    } else if ($action == "check-create") {
        CheckCreate();
    } else if($action == "delete"){
        CheckDelete();
    } else if($action == "change-pwd"){
        CheckChangePwd();
    }
}

if (isset($_GET["action"]) && !empty($_GET["action"])) {
    $action = htmlspecialchars($_GET["action"]);
    if ($action == "logout") {
        LogOut();
    } else if ($action == "check-answer") {
        if (isset($_GET["theme"]) && !empty($_GET["theme"]) && isset($_GET["question"]) && !empty($_GET["question"])) {
            CheckAnswer(htmlspecialchars($_GET["theme"]), htmlspecialchars($_GET["question"]));
        }
    }
}

if ($page == "login") {
    $_SESSION['pseudo'] = "Invité";
    $_SESSION['progress'] = 0;
    for ($i = 1; $i <= 9; $i++) {
        $theme = "T" . $i;
        for ($j = 1; $j <= 5; $j++) {
            $question = "Q" . $j;
            $_SESSION[$theme . $question . "D"] = "";
            $_SESSION[$theme . $question . "V"] = "";
        }
    }
    DisplayLoginPage();
} else if ($page == "home") {
    DisplayHome();
} else if ($page == "create") {
    $_SESSION['pseudo'] = "Invité";
    DisplayCreate();
} else if ($page == "account") {
    DisplayAccount();
} else if ($page == "introduction") {
    DisplayIntroduction();
} else if ($page == "intro") {
    DisplayIntro($theme);
} else if ($page == "question" && (int)$question <= 5) {
    DisplayQuestion($theme, $question);
} else if ($page == "theme-finished") {
    DisplayThemeFinished();
} else if ($page == "finished") {
    DisplayFinished();
} else {
    Display404();
}
