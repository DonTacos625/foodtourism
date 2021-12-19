<?php
session_start();

if (!empty($_SESSION["user"])) {
    $errormessage = "ログアウトしました";
} else {
    $errormessage = "再度ログインして下さい";
}

$_SESSION = array();

@session_destroy();
?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-214561408-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-214561408-1');
    </script>

    <title>ログイン</title>

    <link rel="stylesheet" type="text/css" href="css/copyright.css">
    <style>
        body {
            background: linear-gradient(45deg, #99ffff, #ffffff);
        }

        #logoutbox {
            width: 768px;
            height: 500px;
            margin: auto;
            border: 1px solid #aaa;
            text-align: center;
        }

        @media screen and (max-width: 768px) {
            h2 {
                font-size: 19px;
            }

            h3 {
                font-size: 17px;
            }

            #logoutbox {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <main>
            <div id="logoutbox"><br><br><br>
                <h2>横浜みなとみらいフードツーリズム計画作成システム</h2>
                <h3>ログアウト</h3>
                <a href="login.php">ログイン画面</a>
                <div><?php echo htmlspecialchars($errormessage, ENT_QUOTES); ?></div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>