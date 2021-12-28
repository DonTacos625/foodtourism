<?php
//require_once(__DIR__ . "/../config/cfg.php");

session_start();

//エラーメッセージ初期化
$errormessage = "";


if (!empty($_GET["register"])) {
    $errormessage = "会員登録完了";
}


//ログインボタン
if (!empty($_POST["login"])) {
    //IDのチェック
    if (empty($_POST["user"])) {
        $errormessage = "IDが入力されていません";
    } else if (empty($_POST["pass"])) {
        $errormessage = "パスワードが入力されていません";
    }

    //ID・Passのチェック
    if (!empty($_POST["user"]) && !empty($_POST["pass"])) {
        $user = $_POST["user"];

        //DB接続
        require "connect_database.php";

        try {
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
            $stmt1->bindParam(":id", $user);
            $stmt1->execute();

            if ($result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC)) {
                if (password_verify($pass, $result1[0]["pass"])) {
                    session_regenerate_id(true);

                    $_SESSION["user"] = $result1[0]["id"];
                    $_SESSION["age"] = $result1["age"];
                    $_SESSION["gender"] = $result1["gender"];

                    //ユーザーのデータベースから値を入れる
                    //開始・終了駅と昼食・夕食店舗をセッション変数に格納
                    $user_id = $_SESSION["user"];
                    $userdatastmt = $pdo->prepare("SELECT * FROM userdata.$user_id WHERE start_id IS NOT NULL ");
                    $userdatastmt->execute();
                    foreach ($userdatastmt as $row) {
                        $_SESSION["start_station_id"] = $row["start_id"];
                        $_SESSION["lanch_id"] = $row["lanch_id"];
                        $_SESSION["dinner_id"] = $row["dinner_id"];
                        $_SESSION["goal_station_id"] = $row["goal_id"];
                    }
                    //各スポットIdをセッション変数に格納
                    $userdatastmt1 = $pdo->prepare("SELECT * FROM userdata.$user_id WHERE s_l_ids IS NOT NULL ");
                    $userdatastmt1->execute();
                    $userdatastmt2 = $pdo->prepare("SELECT * FROM userdata.$user_id WHERE l_d_ids IS NOT NULL ");
                    $userdatastmt2->execute();
                    $userdatastmt3 = $pdo->prepare("SELECT * FROM userdata.$user_id WHERE d_g_ids IS NOT NULL ");
                    $userdatastmt3->execute();
                    foreach ($userdatastmt1 as $row1) {
                        $_SESSION["s_l_kankou_spots_id"][] = $row1["s_l_ids"];
                    }
                    foreach ($userdatastmt2 as $row2) {
                        $_SESSION["l_d_kankou_spots_id"][] = $row2["l_d_ids"];
                    }
                    foreach ($userdatastmt3 as $row3) {
                        $_SESSION["d_g_kankou_spots_id"][] = $row3["d_g_ids"];
                    }

                    //ログイン成功でホームに移動
                    header("Location: home.php");
                    exit();
                } else if (!password_verify($pass, $result1[0]["pass"])) {
                    $errormessage = "パスワードが間違っています";
                }
            } else if (empty($result1)) {
                $errormessage = "IDが間違っています";
            }
        } catch (PDOException $e) {
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

        #loginbox {
            width: 768px;
            height: 500px;
            margin: auto;
            border: 1px solid #aaa;
            text-align: center;
        }

        #loginbox table {
            margin: auto;
        }

        #loginbox table th {
            background: #0099FF;
            color: #fff;
            white-space: nowrap;
            border-left: 5px solid #000080;
        }
        #loginbox table td {
            text-align: right;
        }

        @media screen and (max-width: 768px) {
            h2 {
                font-size: 19px;
            }

            h3 {
                font-size: 17px;
            }

            #loginbox {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <main>
            <div id="loginbox"><br><br><br>
                <h2>横浜みなとみらいフードツーリズム計画作成システム</h2>
                <p>
                    こちらは横浜みなとみらい近隣でのフードツーリズム計画の作成を支援するシステムです。<br>
                    利用には<a href="signup.php">利用者登録</a>が必要となります。<br>
                </p>

                <h3>ログイン</h3>
                <form id="loginform" name="loginform" action="" method="POST" autocomplete="off">
                    <table>
                        <tr>
                            <th><label for="user">ID</label></th>
                            <td>
                                <input type="text" id="user" name="user" placeholder="IDを入力" value="<?php if (!empty($_POST["userid"])) {
                                                                                                        echo htmlspecialchars($_POST["userid"], ENT_QUOTES);
                                                                                                    } ?>" required>
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

                <div>
                    <font color="#ff0000"><?php echo htmlspecialchars($errormessage, ENT_QUOTES); ?></font>
                </div>
                <div>
                    <font color="#ff0000"><?php //echo htmlspecialchars($signupmessage, ENT_QUOTES); 
                                            ?></font>
                </div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>