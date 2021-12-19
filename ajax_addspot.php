<?php
session_start();
require_once("connect_database.php");

$post_data_1 = $_POST['post_data_1'];
$post_data_2 = json_decode($_POST['post_data_2'], true);

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

    $stmt1 = $pdo->prepare("SELECT * FROM minatomirai_kankou_data where id = :id");
    $stmt1 -> bindParam(":id", $post_data_1, PDO::PARAM_INT);
    $stmt1 -> execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

}catch(PDOException $e){
    //デバッグ用
    echo $e->getMessage();
}

//$_SESSION["s_l_kankou_spots_id"] = [1,2];

switch ($post_data_2) {
    case "1":
        //セッション変数を配列にする
        //スポットは3個以上登録できないようにする設定がまだ未完成
        if (isset($_SESSION["s_l_kankou_spots_id"])) {
            if (!(in_array($post_data_1, $_SESSION["s_l_kankou_spots_id"]))) {
                $_SESSION["s_l_kankou_spots_id"][] = $post_data_1;
                $spotname = $result1["name"];
            } else {
                $spotname = "";
            }
        } else {
            $_SESSION["s_l_kankou_spots_id"][] = $post_data_1;
            $spotname = $result1["name"];
        }
        break;
    case "2":
        if (isset($_SESSION["l_d_kankou_spots_id"])) {
            if (!(in_array($post_data_1, $_SESSION["l_d_kankou_spots_id"]))) {
                $_SESSION["l_d_kankou_spots_id"][] = $post_data_1;
                $spotname = $result1["name"];
            } else {
                $spotname = "";
            }
        } else {
            $_SESSION["l_d_kankou_spots_id"][] = $post_data_1;
            $spotname = $result1["name"];
        }
        break;
    case "3":
        if (isset($_SESSION["d_g_kankou_spots_id"])) {
            if (!(in_array($post_data_1, $_SESSION["d_g_kankou_spots_id"]))) {
                $_SESSION["d_g_kankou_spots_id"][] = $post_data_1;
                $spotname = $result1["name"];
            } else {
                $spotname = "";
            }
        } else {
            $_SESSION["d_g_kankou_spots_id"][] = $post_data_1;
            $spotname = $result1["name"];
        }
        break;
}

//name_array = [["スポット名", "スポットID"]]を作り出す

try{
    switch ($post_data_2) {
        case "1":
            if (!isset($_SESSION["s_l_kankou_spots_id"])) {
                $spots_name_and_id = [["設定されていません", 0]];
            } else {
                foreach ($_SESSION["s_l_kankou_spots_id"] as $s_l) {
                    $stmt = $pdo->prepare("SELECT * FROM minatomirai_kankou_data WHERE id = :id");
                    $stmt->bindParam(":id", $s_l);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $spots_name_and_id[] = [$result["name"], $s_l];
                }
            }
            break;
        case "2":
            if (!isset($_SESSION["l_d_kankou_spots_id"])) {
                $spots_name_and_id = [["設定されていません", 0]];
            } else {
                foreach ($_SESSION["l_d_kankou_spots_id"] as $l_d) {
                    $stmt = $pdo->prepare("SELECT * FROM minatomirai_kankou_data WHERE id = :id");
                    $stmt->bindParam(":id", $l_d);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $spots_name_and_id[] = [$result["name"], $l_d];
                }
            }
            break;
        case "3":
            if (!isset($_SESSION["d_g_kankou_spots_id"])) {
                $spots_name_and_id = [["設定されていません", 0]];
            } else {
                foreach ($_SESSION["d_g_kankou_spots_id"] as $d_g) {
                    $stmt = $pdo->prepare("SELECT * FROM minatomirai_kankou_data WHERE id = :id");
                    $stmt->bindParam(":id", $d_g);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $spots_name_and_id[] = [$result["name"], $d_g];
                }
            }
            break;
    }

}catch(PDOException $e){
    echo $e->getMessage();
}

$return_array = array($spotname, $spots_name_and_id);

echo json_encode($return_array);
