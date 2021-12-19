<?php
session_start();

//どの時間帯のスポットをいじるか
$post_data_1 = json_decode($_POST['post_data_1'], true);

//削除するスポットのid
$post_data_2 = $_POST['post_data_2'];

switch ($post_data_1) {
    case "1":
        if (isset($_SESSION["s_l_kankou_spots_id"])) {
            //post_data_2で指定されたidを持つ要素を削除
            $new_session = array_diff($_SESSION["s_l_kankou_spots_id"], array($post_data_2));
            //配列のキーを詰める
            $new_session = array_values($new_session);
            $_SESSION["s_l_kankou_spots_id"] = $new_session;
            //セッション変数の配列が空なら開放する
            if(empty($_SESSION["s_l_kankou_spots_id"])){
                unset($_SESSION["s_l_kankou_spots_id"]);
            }
            $return = "s_l_name";
        } else {$return = "";}
        break;
    case "2":
        if (isset($_SESSION["l_d_kankou_spots_id"])) {
            $new_session = array_diff($_SESSION["l_d_kankou_spots_id"], array($post_data_2));
            $new_session = array_values($new_session);
            $_SESSION["l_d_kankou_spots_id"] = $new_session;
            if(empty($_SESSION["l_d_kankou_spots_id"])){
                unset($_SESSION["l_d_kankou_spots_id"]);
            }
            $return = "l_d_name";
        } else {$return = "";}
        break;
    case "3":
        if (isset($_SESSION["d_g_kankou_spots_id"])) {
            $new_session = array_diff($_SESSION["d_g_kankou_spots_id"], array($post_data_2));
            $new_session = array_values($new_session);
            $_SESSION["d_g_kankou_spots_id"] = $new_session;
            if(empty($_SESSION["d_g_kankou_spots_id"])){
                unset($_SESSION["d_g_kankou_spots_id"]);
            }
            $return = "d_g_name";
        } else {$return = "";}
        break;
}

//name_array = [["スポット名", "スポットID"]]を作り出す
require_once("connect_database.php");

try{
    switch ($post_data_1) {
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

//echo json_encode($return);
echo json_encode($spots_name_and_id);

?>
