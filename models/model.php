<?php

require "db-config.php";

function GetIdUser()
{
    global $DB;
    $SQL_ID_USER = 'SELECT id_user FROM user WHERE pseudo = ?';
    $result_id_user = $DB->prepare($SQL_ID_USER);
    $result_id_user->bindValue(1, htmlspecialchars($_SESSION["pseudo"]));
    $result_id_user->execute();
    $id_user = $result_id_user->fetch();
    $result_id_user->closeCursor();
    return $id_user["id_user"];
}

function GetDisabledButton()
{    
    global $DB;

    $id_user = GetIdUser();

    for($i = 1 ; $i <= 9 ; $i++){
        $theme = "T" . $i;
        for($j = 1 ; $j <= 5 ; $j++){
            $question = "Q" . $j;
            $_SESSION[$theme . $question . "D"] = "";
        }
    }

    for($i = 1 ; $i <= 9 ; $i++){
        $theme = "T" . $i;
        $SQL = "SELECT $theme FROM user_progress WHERE id_user = ?";
        $result = $DB->prepare($SQL);
        $result->bindValue(1, $id_user);
        $result->execute();
        $user_progress = $result->fetch();
        $result->closeCursor();
        $user_progress = (int)$user_progress[$theme];
        for($j = $user_progress + 2 ; $j <= 5 ; $j++){
            $question = "Q" . $j;
            $_SESSION[$theme . $question . "D"] = "disabled";
        }
        for($j = 1 ; $j <= $user_progress ; $j++){
            $question = "Q" . $j;
            $_SESSION[$theme . $question . "D"] = "disabled";
            $_SESSION[$theme . $question . "V"] = "validated";
        }
    }
}

function Connect()
{
    global $DB;
    $username = htmlspecialchars($_POST['pseudo']);
    $password = htmlspecialchars($_POST['password']);
    $stay_connected = htmlspecialchars($_POST["stay-connected"]);

    $SQL = 'SELECT pseudo, password, progress FROM user WHERE pseudo = ?';
    $result = $DB->prepare($SQL);
    $result->bindValue(1, $username, PDO::PARAM_STR);
    $result->execute();
    $datas = $result->fetch();

    $username_db = htmlspecialchars($datas['pseudo']);
    $password_db = htmlspecialchars($datas['password']);
    $progress_db = htmlspecialchars($datas['progress']);
    $result->closeCursor();

    if($username == $username_db && password_verify($password, $password_db)){
        $_SESSION['pseudo'] = $username_db;
        $_SESSION['progress'] = $progress_db;
        UpdateToken();
        if($stay_connected != "on"){
            setcookie("token", "", time() - 3600);
            unset($_COOKIE["token"]);
        }
        return 1;
    }
    else if($username != $username_db){
        $_SESSION['error'] = 'Pseudo incorrect !';
        return 0;
    }
    else if(!password_verify($password, $password_db)){
        $_SESSION['error'] = 'Mot de passe incorrect !';
        return 0;
    }
}

function Create()
{
    global $DB;
    $username = htmlspecialchars($_POST['pseudo']);
    $password = htmlspecialchars($_POST['password']);
    $stay_connected = htmlspecialchars($_POST["stay-connected"]);

    $SQL = 'SELECT pseudo FROM user WHERE pseudo = ?';
    $result = $DB->prepare($SQL);
    $result->bindValue(1, $username);
    $result->execute();
    $datas = $result->fetch();

    $username_db = htmlspecialchars($datas['pseudo']);
    $result->closeCursor();
    
    if(empty($username_db)){
        $SQL = 'INSERT INTO user(pseudo, password, progress, token) VALUES (?, ?, 0, "")';
        $result = $DB->prepare($SQL);
        $result->bindValue(1, $username);
        $result->bindValue(2, password_hash($password, PASSWORD_BCRYPT));
        $result->execute();
        $result->CloseCursor();
        
        $SQL = 'INSERT INTO user_progress(id_user, T1, T2, T3, T4, T5, T6, T7, T8, T9) VALUES (?, 0, 0, 0, 0, 0, 0, 0, 0, 0)';
        
        $SQL_ID_USER = 'SELECT id_user FROM user WHERE pseudo = ?';
        $result_id_user = $DB->prepare($SQL_ID_USER);
        $result_id_user->bindValue(1, $username);
        $result_id_user->execute();
        $id_user = $result_id_user->fetch();
        $result_id_user->closeCursor();

        $result = $DB->prepare($SQL);
        $result->bindValue(1, $id_user["id_user"]);
        $result->execute();
        $result->CloseCursor();

        $_SESSION['pseudo'] = $username;
        $_SESSION['progress'] = 0;

        UpdateToken();
        if($stay_connected != "on"){
            setcookie("token", "", time() - 3600);
            unset($_COOKIE["token"]);
        }
    }
    else if(!empty($username_db)){
        $_SESSION['error'] = "Pseudo déjà utilisé !";
    }
}

function Delete()
{
    global $DB;
    $id_user = GetIdUser();
    $SQL = 'DELETE FROM user_progress WHERE id_user = ?';
    $result = $DB->prepare($SQL);
    $result->bindValue(1, $id_user);
    $result->execute();
    $result->closeCursor();

    $SQL = 'DELETE FROM user WHERE pseudo = ?';
    $result = $DB->prepare($SQL);
    $result->bindValue(1, $_SESSION['pseudo']);
    $result->execute();
    $result->closeCursor();

    $_SESSION['error'] = "Compte supprimé avec succès !";
}

function ChangePwd()
{
    global $DB;
    $actual_pwd = htmlspecialchars($_POST['actual-pwd']);
    $password = htmlspecialchars($_POST['password']);

    $result = $DB->prepare('SELECT password FROM user WHERE pseudo = ?');
    $result->bindValue(1, $_SESSION['pseudo']);
    $result->execute();
    $datas = $result->fetch();

    $actual_pwd_db = htmlspecialchars($datas['password']);

    $result->closeCursor();

    if(password_verify($actual_pwd, $actual_pwd_db)){
        $SQL = 'UPDATE user SET password = ? WHERE pseudo = ?';
        $result = $DB->prepare($SQL);
        $result->bindValue(1, password_hash($password, PASSWORD_BCRYPT));
        $result->bindValue(2, $_SESSION['pseudo']);
        $result->execute();
        $_SESSION['error'] = "Mot de passe changé avec succès !";
    }
    else if(!password_verify($actual_pwd, $actual_pwd_db)){
        $_SESSION['error'] = "Le mot de passe actuel n'est pas vérifié !";
    }
}

function CanDoQuestion(string $theme, string $question)
{
    if($_SESSION["pseudo"] == "Invité"){
        return 1;
    }

    global $DB;

    $id_user = GetIdUser();
    $theme_column = "T" . $theme;

    $SQL = "SELECT $theme_column FROM user_progress WHERE id_user = ?";
    $result = $DB->prepare($SQL);
    $result->bindValue(1, $id_user);
    $result->execute();
    $user_progress = $result->fetch();
    $result->closeCursor();
    
    $user_progress = (int)$user_progress["T" . $theme];
    $question = (int)$question;

    if($question == $user_progress + 1){
        return 1;
    }
    else if($question <= $user_progress) {
        $_SESSION["error"] = "Question déjà validée";
        return 0;
    }
    else if($question > $user_progress + 1) {
        $_SESSION["error"] = "Question verrouilée";
        return 0;
    }
}

function CheckAnswerDB(string $theme, string $question)
{
    global $DB;
    $answer = htmlspecialchars($_POST['answer']);
    $is_multiple_answers = 0;
    if(isset($_POST["answer2"]) && !empty($_POST["answer2"]) && isset($_POST["answer3"]) && !empty($_POST["answer3"])){
        $answer2 = htmlspecialchars($_POST["answer2"]);
        $answer3 = htmlspecialchars($_POST["answer3"]);
        $answer = $answer . "@" . $answer2 . "@" . $answer3;
        $answer_split = explode("@", $answer);
        $is_multiple_answers = 1;
    }

    $SQL = 'SELECT answer, gain FROM answers WHERE theme = ? AND question = ?';
    $result = $DB->prepare($SQL);
    $result->bindValue(1, $theme);
    $result->bindValue(2, $question);
    $result->execute();
    $datas = $result->fetch();
    $result->closeCursor();

    $answer_DB = htmlspecialchars($datas["answer"]);
    $gain = $datas["gain"];

    if($is_multiple_answers){
        $multiple_answers_split = explode("@", $answer_DB);
    }

    $id_user = GetIdUser();
    $theme_column = "T" . $theme;

    if($answer == $answer_DB){
        if($_SESSION["pseudo"] != "Invité"){
            $SQL = "UPDATE user_progress SET $theme_column = $theme_column + 1 WHERE id_user = ?";
            $result = $DB->prepare($SQL);
            $result->bindValue(1, $id_user);
            $result->execute();
            $result->closeCursor();
    
            $_SESSION["progress"] += $gain;
            
            $SQL = "UPDATE user SET progress = ? WHERE id_user = ?";
            $result = $DB->prepare($SQL);
            $result->bindValue(1, $_SESSION["progress"]);
            $result->bindValue(2, $id_user);
            $result->execute();
            $result->closeCursor();
        }
        else{
            $_SESSION["progress"] += $gain;
        }
        return 1;
    }
    else if($answer != $answer_DB){
        if($is_multiple_answers){
            $_SESSION["error"] = "";
            for($i = 0 ; $i < 3 ; $i++){
                if($answer_split[$i] != $multiple_answers_split[$i]){
                    $_SESSION["error"] = $_SESSION["error"] . "Réponse " . $i + 1 . " fausse\n";
                }
            }
        }
        else{
            $_SESSION["error"] = "Mauvaise réponse";
        }
        return 0;
    }
}

function UpdateToken()
{
    global $DB;
    $_SESSION["token"] = bin2hex(random_bytes(32));

    $SQL = 'UPDATE user SET token = ? WHERE pseudo = ?';
    $result = $DB->prepare($SQL);
    $result->bindValue(1, htmlspecialchars($_SESSION["token"]));
    $result->bindValue(2, htmlspecialchars($_SESSION["pseudo"]));
    $result->execute();
    $result->closeCursor();
    $expire = time() + 365 * 24 * 60 * 60;
    $options = [
        'expires'  => $expire,
        'path'     => '/',
        'secure'   => false
    ];
    setcookie("token", $_SESSION["token"], $options);
}

function GetAutoLogin()
{
    global $DB;

    $SQL = 'SELECT pseudo, progress FROM user WHERE token = ?';
    $result = $DB->prepare($SQL);
    $result->bindValue(1, htmlspecialchars($_COOKIE["token"]));
    $result->execute();
    $datas = $result->fetch();
    
    $_SESSION["pseudo"] = htmlspecialchars($datas["pseudo"]);
    $_SESSION["bankroll"] = htmlspecialchars($datas["bankroll"]);
    $_SESSION["admin_access"] = htmlspecialchars($datas["admin_access"]);
    $result->closeCursor();

    if(empty($_SESSION["pseudo"])){
        return 0;
    }

    UpdateToken();
    return 1;
}