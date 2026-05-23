<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/img/neonquests_logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style-account.css">
    <title>NeonQuests</title>
</head>

<body>

    <?php require "views/templates/top-bar.html" ?>

    <div class="logo-account-wrapper">
        <img src="/assets/img/neonquests_account_icon.png" alt="nonquests-account-icon" class="logo-account">
        <?= $_SESSION['pseudo'] ?>
    </div>

    <div class="wrapper-confirm-delete" id="confirm-delete">
        <div class="confirm-delete">
            <h2>Voulez-vous vraiment supprimer votre compte ?</h2>
            <p class="delete-info">Cette action est irréversible et toutes vos données seront perdues !</p>
            <br>
            <div class="wrapper-button-delete">
                <button onclick="displayConfirm()" class="non-button button-delete">Non</button>
                    <form action="/index.php" method="post">
                        <button class="oui-button button-delete" type="submit" name="action" value="delete">Oui</button>
                    </form>
            </div>
        </div>
    </div>

    <!-- From Uiverse.io by InfinityLoopGames -->
    <form class="container" action="/index.php" method="post">
        <div class="input-container">
            <div class="input-content">
                <div class="input-dist">
                    <div class="input-type">
                        <div class="password-container">
                            <input class="input-is" type="password" required="" placeholder="Mot de passe actuel" name="actual-pwd" id="actual-pwd" />
                            <button type="button" class="toggle-password" onclick="togglePwd('actual-pwd')">
                                <i class="fa-solid fa-eye" id="icon-actual"></i>
                            </button>
                        </div>
                        <div class="password-container">
                            <input class="input-is" type="password" required="" placeholder="Nouveau mot de passe" name="password" id="password" />
                            <button type="button" class="toggle-password" onclick="togglePwd('password')">
                                <i class="fa-solid fa-eye" id="icon-password"></i>
                            </button>
                        </div>
                        <div class="password-container">
                            <input class="input-is" type="password" required="" placeholder="Confirmation mot de passe" name="confirm-password" id="confirm-password" />
                            <button type="button" class="toggle-password" onclick="togglePwd('confirm-password')">
                                <i class="fa-solid fa-eye" id="icon-confirm"></i>
                            </button>
                        </div>
                        <button class="submit" type="submit" name="action" value="change-pwd">Changer le mot de passe</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="sup-button">
        <a href="/index.php?action=logout">
            <button class="create-account-button"><span class="log-out">Se déconnecter</span></button>
        </a>
        <button onclick="displayConfirm()" class="create-account-button"><span class="delete-account">Supprimer mon compte</span></button>
        
    </div>

    <?php
    require "views/templates/footer.html";

    if (!empty($_SESSION['error'])) {
        echo "<script>
        window.onload = function() {
            alert('" . $_SESSION['error'] . "');
        } </script>";
        unset($_SESSION['error']);
    }
    ?>
    <script src="/assets/js/script.js"></script>
</body>
</html> 