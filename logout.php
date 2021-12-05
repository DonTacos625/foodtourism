<?php
session_start();

if(!empty($_SESSION["user"])){
    $errormessage = "ログアウトしました";
}else{
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
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-131239045-1"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        
        gtag('config', 'UA-131239045-1');
        </script>

        <title>ログイン</title>

        <style>
            body {
                background: linear-gradient(45deg, #99ffff, #ffffff);
            }

            #logoutbox{
                width: 768px;
                height: 500px;
                margin: auto;
                border: 1px solid #aaa;
                text-align: center;
            }

            @media screen and (max-width: 768px){
                h2{
                    font-size: 19px;
                }

                h3{
                    font-size: 17px;
                }
                
                #logoutbox{
                    width: 90%;
                }
            }
        </style>
    </head>

    <body>
        <div id="logoutbox"><br><br><br>
            <h2>横浜みなとみらいフードツーリズム計画作成システム</h2>
            <h3>ログアウト</h3>
            <a href="login.php">ログイン画面</a>
            <div><?php echo htmlspecialchars($errormessage, ENT_QUOTES); ?></div>
        </div>
    </body>
</html>