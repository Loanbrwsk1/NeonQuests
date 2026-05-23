<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/img/neonquests_logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title>NeonQuests</title>
</head>

<body>

    <div class="image-container">
        <img src="/assets/img/neonquests_full_logo.svg" alt="neonquests-logo">
    </div>
    <!-- From Uiverse.io by InfinityLoopGames -->
    <form class="container" action="/index.php" method="post">
        <div class="input-container">
            <div class="input-content">
                <div class="input-dist">
                    <div class="input-type">
                        <input class="input-is" type="text" required="" placeholder="Pseudo" name="pseudo" />
                        <div class="password-container">
                            <input class="input-is" type="password" required="" placeholder="Mot de passe" name="password" id="password" />
                            <button type="button" class="toggle-password password-field" onclick="togglePwd('password')">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <label><input type="checkbox" name="stay-connected" id="stay-connected">Rester connecté</label>
                        <button class="submit" type="submit" name="action" value="check-connect">Se connecter</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="sup-button">
        <a href="create">
            <button class="create-account-button">Créer un compte</button>
        </a>
        <a href="/introduction">
            Jouer sans se connecter
        </a>
    </div>
    <?php
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