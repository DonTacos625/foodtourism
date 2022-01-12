<?php

/*
DB userinfo
id,pass,age,gender

DB userdata
*/

session_start();

$errormessage = "";
$signupmessage = "";

if (!empty($_POST["signup"])) {
    //ID・Pass文字列チェック
    if (!preg_match('/\A[a-z\d]{4,10}+\z/i', $_POST["user"])) {
        $errormessage = 'IDが不適切です';
    }
    if (!preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,15}+\z/i', $_POST["pass"])) {
        $errormessage = 'パスワードが不適切です';
    }
    //ID・Pass空チェック
    if (empty($_POST["user"])) {
        $errormessage = "ユーザーIDが未入力です";
    } else if (empty($_POST["pass"])) {
        $errormessage = 'パスワードが未入力です';
    } else if (empty($_POST["pass2"])) {
        $errormessage = 'パスワードが未入力です';
    }

    if (!empty($_POST["user"]) && !empty($_POST["pass"]) && !empty($_POST["pass2"]) && $_POST["pass"] === $_POST["pass2"] && preg_match('/\A[a-z\d]{4,10}+\z/i', $_POST["user"]) && preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,15}+\z/i', $_POST["pass"])) {
        $user = $_POST["user"];
        $pass = $_POST["pass"];
        $age = $_POST["age"];
        $gender = $_POST["gender"];
        $survey = 1;
        settype($age, "int");
        settype($gender, "int");

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

            //ID重複チェック準備
            $stmt1 = $pdo->prepare("SELECT * FROM userinfo WHERE id = :id");
            $stmt1->bindParam(":id", $user);
            $stmt1->execute();
            $result = $stmt1->fetchAll(PDO::FETCH_ASSOC);

            //ID重複チェック
            if (empty($result)) {
                //パスワードのハッシュ化
                $passhash = password_hash($pass, PASSWORD_DEFAULT);

                //ID,Pass書き込み
                //ユーザー情報書き込み
                $stmt3 = $pdo->prepare("INSERT INTO userinfo(id, pass, age, gender, survey) VALUES(:id, :pass, :age, :gender, :survey)");
                $stmt3->bindParam(":id", $user, PDO::PARAM_STR);
                $stmt3->bindParam(":pass", $passhash, PDO::PARAM_STR);
                $stmt3->bindParam(":age", $age, PDO::PARAM_INT);
                $stmt3->bindParam(":gender", $gender, PDO::PARAM_INT);
                $stmt3->bindParam(":survey", $survey, PDO::PARAM_INT);
                $stmt3->execute();

                $databasename = "d{$user}";
                //DB作成
                $sql = "CREATE TABLE userdata." . $databasename . " (
		            start_id int,
                    s_l_ids int,
                    lanch_id int,
                    l_d_ids int,
                    dinner_id int,
                    d_g_ids int,
                    goal_id int
	            )";
                $res = $pdo->query($sql);

                //ログイン画面へ移動
                header("Location: login.php?register=1");

                $signupmessage = "登録完了";
            } elseif (!empty($result)) {
                $errormessage = "IDが既に使用されています";
            }
        } catch (PDOException $e) {
            $errormessage = "データベースエラー";
        }
    } else if ($_POST["pass"] != $_POST["pass2"]) {
        $errormessage = "パスワードに誤りがあります";
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

    <title>サインアップ</title>

    <link rel="stylesheet" type="text/css" href="css/copyright.css">
    <style>

        #signupbox {
            width: 768px;
            height: 500px;
            margin: auto;
            border: 1px solid #aaa;
            text-align: center;
        }

        #signupbox table {
            margin: auto;
        }

        #signupbox table th {
            background: #0099FF;
            color: #fff;
            white-space: nowrap;
            border-left: 5px solid #000080;
        }

        #signupbox table td {
            text-align: left;
        }

        @media screen and (max-width: 768px) {
            h2 {
                font-size: 19px;
            }

            h3 {
                font-size: 17px;
            }

            #signupbox {
                width: auto;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <main>
            <div id="signupbox">
                <h2>横浜みなとみらいフードツーリズム計画作成支援システム</h2>

                <h3>利用者登録</h3>
                <form id="signupform" name="signupForm" action="" method="POST" autocomplete="off">
                    <table>
                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="user">ID</label></th>
                            <td scope="row"><small>半角英数字4~10文字</small></td>
                        </tr>

                        <tr>
                            <td scope="row"><input type="text" id="user" name="user" placeholder="IDを入力" value="" required></td>
                        </tr>

                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="pass">パスワード</label></th>
                            <td scope="row"><small>半角英数字をそれぞれ1種類以上含む6~15文字</small></td>
                        </tr>

                        <tr>
                            <td scope="row"><input type="password" id="pass" name="pass" placeholder="パスワードを入力" value="" required></td>
                        </tr>

                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="pass2">パスワード(確認)</label></th>
                            <td scope="row"><small>パスワードを再入力して下さい</small></td>
                        </tr>

                        <tr>
                            <td scope="row"><input type="password" id="pass2" name="pass2" placeholder="パスワードを再入力" value="" required></td>
                        </tr>

                        <tr>
                            <th>年代</th>
                            <td>
                                <input type="radio" id="age" name="age" value="10" checked="checked">10代
                                <input type="radio" id="age" name="age" value="20">20代
                                <input type="radio" id="age" name="age" value="30">30代<br>
                                <input type="radio" id="age" name="age" value="40">40代
                                <input type="radio" id="age" name="age" value="50">50代
                                <input type="radio" id="age" name="age" value="60">60代以上
                            </td>
                        </tr>

                        <tr>
                            <th>性別</th>
                            <td>
                                <input type="radio" id="gender" name="gender" value="0" checked="checked">男性
                                <input type="radio" id="gender" name="gender" value="1">女性
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td><input type="submit" id="signup" name="signup" value="登録"><br><br>
                            <a href="login.php">ログイン画面</a></td>
                        </tr>
                    </table>
                </form>
                <div>
                    <font color="#ff0000"><?php echo htmlspecialchars($errormessage, ENT_QUOTES); ?></font>
                </div>
                <div>
                    <font color="#0000ff"><?php echo htmlspecialchars($signupmessage, ENT_QUOTES); ?></font>
                </div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>