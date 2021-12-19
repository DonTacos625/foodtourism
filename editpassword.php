<?php
require "frame.php";

$errormessage = "";
$editmessage = "";

if (!empty($_POST["editpass"])) {
    //Pass文字列チェック
    if (!preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,15}+\z/i', $_POST["pass1"])) {
        $errormessage = 'パスワードが不適切です';
    }

    //Pass空判定
    if (empty($_POST["pass"])) {
        $errormessage = '現在のパスワードが未入力です';
    } else if (empty($_POST["pass1"])) {
        $errormessage = '新しいパスワードが未入力です';
    } else if (empty($_POST["pass2"])) {
        $errormessage = '新しいパスワード(確認)が未入力です';
    }

    if (!empty($_POST["pass"]) && !empty($_POST["pass1"]) && !empty($_POST["pass2"]) && $_POST["pass1"] === $_POST["pass2"] && preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,15}+\z/i', $_POST["pass1"])) {
        $pass = $_POST["pass"];
        $pass1 = $_POST["pass1"];

        //DB接続
        try {
            /*
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

            //現在のパスワードを確認
            $stmt1 = $pdo->prepare("SELECT * FROM userinfo WHERE id = :id");
            $stmt1->bindParam(":id", $_SESSION["user"]);
            $stmt1->execute();
            $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

            if (password_verify($pass, $result1["pass"])) {
                $passhash = password_hash($pass1, PASSWORD_DEFAULT);
                $stmt2 = $pdo->prepare("UPDATE userinfo SET pass = :pass WHERE id = :id");
                $stmt2->bindParam(":pass", $passhash, PDO::PARAM_STR);
                $stmt2->bindParam(":id", $_SESSION["user"]);
                $stmt2->execute();

                $editmessage = "変更完了";
            } else {
                $errormessage = "パスワードが間違っています";
            }
        } catch (PDOException $e) {
            $errormessage = "データベースエラー";
            //デバッグ用
            echo $e->getMessage();
        }
    } else if ($_POST["pass1"] != $_POST["pass2"]) {
        $errormessage = "パスワードが一致しません";
    }
}
?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
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

    <title>パスワード変更</title>

    <style>
        #editbox {
            float: left;
            width: 500px;
            margin-left: 5px;
        }

        #editbox h2 {
            margin: 0px;
        }

        #editbox th {
            width: 100px;
            background: #0099FF;
            color: #fff;
            white-space: nowrap;
            margin: 3px;
            padding: 2px;
            border-left: 5px solid #000080;
        }

        #editbox td {
            text-align: left;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            h2 {
                font-size: 20px;
            }

            h3 {
                font-size: 18px;
            }
        }

        @media screen and (max-width:768px) {
            h2 {
                font-size: 19px;
            }

            h3 {
                font-size: 17px;
            }

            #editbox {
                width: auto;
                margin: 0px;
            }

            #editbox th {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <main>
            <div id="editbox">
                <h2>パスワード変更</h2>
                <form id="editform" name="editform" action="" method="POST">
                    <table>
                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="pass">旧パスワード</label></th>
                            <td scope="row"><small>現在のパスワードを入力して下さい</small></td>
                        </tr>

                        </tr>
                        <td><input type="password" id="pass" name="pass" placeholder="現在のパスワードを入力" value="" required></td>
                        </tr>

                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="pass1">新パスワード</label></th>
                            <td scope="row"><small>半角英数字をそれぞれ1種類以上含む6~15文字</small></td>
                        </tr>

                        </tr>
                        <td><input type="password" id="pass1" name="pass1" placeholder="新しいパスワードを入力" value="" required></td>
                        </tr>

                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="pass2">新パスワード(確認)</label></th>
                            <td scope="row"><small>パスワードを再入力して下さい</small></td>
                        </tr>

                        </tr>
                        <td><input type="password" id="pass2" name="pass2" placeholder="新しいパスワードを再入力" value="" required></td>
                        </tr>

                        <tr>
                            <td></td>
                            <td><input type="submit" id="editpass" name="editpass" value="変更"></td>
                        </tr>
                    </table>
                </form>

                <div>
                    <font color="#ff0000"><?php echo htmlspecialchars($errormessage, ENT_QUOTES); ?></font>
                </div>
                <div>
                    <font color="#0000ff"><?php echo htmlspecialchars($editmessage, ENT_QUOTES); ?></font>
                </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>