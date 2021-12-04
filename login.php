<?php
//require_once(__DIR__ . "/../config/cfg.php");

session_start();

//エラーメッセージ初期化
$errormessage = "";

/*
if(!empty($_GET["register"])){
    $errormessage = "会員登録完了";
}
*/

//ログインボタン
if(!empty($_POST["login"])){
    //IDのチェック
    if(empty($_POST["user"])){
        $errormessage = "IDが入力されていません";       
    }else if(empty($_POST["pass"])){
        $errormessage = "パスワードが入力されていません";
    }

    //ID・Passのチェック
    if(!empty($_POST["user"]) && !empty($_POST["pass"])){
        $user = $_POST["user"];

        //DB接続
        require "cfg_test.php";

        try{
            /*
            //恐らくherokuでのDB接続に必要
            $db = parse_url(getenv("DATABASE_URL"));

            $pdo = new PDO("pgsql:" . sprintf(
            "host=%s;port=%s;user=%s;password=%s;dbname=%s",
            $db["host"],
            $db["port"],
            $db["user"],
            $db["pass"],
            ltrim($db["path"], "/")
            ));
            */      
                  
            $pass = $_POST["pass"];

            $stmt1 = $pdo->prepare("SELECT * FROM userinfo WHERE id = :id");
            $stmt1 -> bindParam(":id", $user);
            $stmt1 -> execute();

            if($result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC)){
                if(password_verify($pass, $result1[0]["pass"])){
                    session_regenerate_id(true);

                    $_SESSION["user"] = $result1[0]["id"];
                    $_SESSION["age"] = $result1["age"];
                    $_SESSION["gender"] = $result1["gender"];

                    for($i=1; $i<=$data_num; $i++){
                        $_SESSION["info" . $i] = $result1["info" . $i];
                    }
                    
                    header("Location: home.php");
                    exit();
                }else if(!password_verify($pass, $result1[0]["pass"])){
                    $errormessage = "パスワードが間違っています";
                }
            }else if(empty($result1)){
                $errormessage = "IDが間違っています";
            }
        }catch(PDOException $e){
            $errormessage = "データベースエラー";

            echo $e->getMessage();
        }
    }
}

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
            #loginbox{
                width: 768px;
                margin: auto;
                border: 1px solid #aaa;
                text-align: center;
            }

            #loginbox table{
                margin: auto;
            }

            #loginbox table th{
                background: #0099FF;
                color: #fff;
                white-space: nowrap;
                border-left: 5px solid #000080;
            }

            @media screen and (max-width: 768px){
                h2{
                    font-size: 19px;
                }

                h3{
                    font-size: 17px;
                }
                
                #loginbox{
                    width: auto;
                }
            }
        </style>
    </head>

    <body>
        <div id= "loginbox">
            <h2>横浜みなとみらいフードツーリズム計画作成システム</h2>
            <p>
                こちらは横浜みなとみらい近隣の観光スポットを推薦するシステムです。<br>
                利用には<a href="signup.php">利用者登録</a>が必要となります。<br>
            </p>

            <h3>ログイン</h3>
            <form id="loginform" name="loginform" action="" method="POST" autocomplete="off">
                <table>
                    <tr>
                        <th><label for="user">ID</label></th>
                        <td>
                            <input type="text" id="user" name="user" placeholder="IDを入力" value="<?php if (!empty($_POST["userid"])) {echo htmlspecialchars($_POST["userid"], ENT_QUOTES);} ?>" required>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="pass">パスワード</label></th>
                        <td>
                            <input type="password" id="pass" name="pass" placeholder="パスワードを入力" value="" required>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><input type="submit" id="login" name="login" value="ログイン"></td>
                    </tr>
                </table>
            </form>

            <div><font color="#ff0000"><?php echo htmlspecialchars($errormessage, ENT_QUOTES); ?></font></div>
            <div><font color="#ff0000"><?php //echo htmlspecialchars($signupmessage, ENT_QUOTES); ?></font></div>
        </div>
    </body>
</html>