<?php
require_once("cfg_test.php");

session_start();

if(!isset($_SESSION["user"]) || !$_SESSION["admin"]){
    header("Location: logout.php");
    exit;
}

//削除ボタン押下
if(!empty($_POST["delete"])){
    $user = $_POST["del"];
    
    try{
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

        $stmt1 = $pdo->prepare("DELETE FROM userdata WHERE id = :id");
        $stmt1 -> bindParam(":id", $user);
        $stmt1 -> execute();

        $stmt2 = $pdo->prepare("DELETE FROM userinfo WHERE id = :id");
        $stmt2 -> bindParam(":id", $user);
        $stmt2 -> execute();
    }catch(PDOException $e){
        //デバッグ用
        echo $e->getMessage();
    }
}

//会員情報取得
try{
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
    $stmt3 = $pdo->prepare("SELECT * FROM userdata");
    $stmt3 -> execute();

    $result3 = $stmt3->fetchAll();
}catch(PDOException $e){
    //デバッグ用
    echo $e->getMessage();
}
?>


<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-131239045-1"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        
        gtag('config', 'UA-131239045-1');
        </script>

        <title>ユーザー管理</title>

        <style>
            #memberlist{
                border-collapse: collapse;
                border: solid 4px #00FFFF;
            }

            #memberlist th{
                background: #FFFF99;
                border: solid 2px #00FFFF;
                border-bottom: double 4px #00FFFF;
            }

            #memberlist th:last-child{
                background: #DD0000;
            }

            #memberlist tr{
                border-bottom: solid 2px #00FFFF;
            }

            #memberlist td{
                border: solid 1px #00FFFF;
            }
        </style>
    </head>

    <body>
        <h1>会員管理</h1>
        <h2><a href="home.php">ホーム</a></h2>

        <!-- 会員表 -->
        <table id="memberlist">
            <tr>
                <th>ID</th>
                <th>パスワード</th>
                <th>削除</th>
            </tr>
            <?php foreach($result3 as $res){ ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($res["id"], ENT_QUOTES); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($res["pass"], ENT_QUOTES); ?>
                    </td>
                    
                    <td>
                        <?php if(!$res["administer"]){ ?>
                            <form action="" method="POST">
                                <input type="hidden" name="del" id="del" value="<?php echo htmlspecialchars($res["id"], ENT_QUOTES);?>">
                                <input type="submit" name="delete" id="delete" value="削除">
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

    </body>
</html>