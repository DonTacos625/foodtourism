<?php
session_start();

//どの時間帯のスポットをいじるか
$post_data_1 = json_decode($_POST['post_data_1'], true);

//入れ替えるスポットのid
$post_data_2 = $_POST['post_data_2'];

//上に入れ替えるか下に入れ替えるか
$post_data_3 = $_POST['post_data_3'];

//key2の初期値
$key2 = -1;
switch ($post_data_1) {
    case "1":
        if (isset($_SESSION["s_l_kankou_spots_id"])) {
            //入れ替える場所指定
            $key1 = array_search($post_data_2, $_SESSION["s_l_kankou_spots_id"]);
            if($post_data_3 == 'up' && $key1 != 0){
                $key2 = $key1 - 1;
            } else if($post_data_3 == 'down' && $key1 != count($_SESSION["s_l_kankou_spots_id"]) - 1 ) {
                $key2 = $key1 + 1;
            }
            //key2が設定されていればswap
            if ($key2 != -1) {
                $first = $_SESSION["s_l_kankou_spots_id"][$key1];
                $second = $_SESSION["s_l_kankou_spots_id"][$key2];
                $_SESSION["s_l_kankou_spots_id"][$key2] = $first;
                $_SESSION["s_l_kankou_spots_id"][$key1] = $second;
            }
            $return = "s_l_name";
        } else {$return = "";}
        break;
    case "2":
        if (isset($_SESSION["l_d_kankou_spots_id"])) {
            //入れ替える場所指定
            $key1 = array_search($post_data_2, $_SESSION["l_d_kankou_spots_id"]);
            if($post_data_3 == 'up' && $key1 != 0){
                $key2 = $key1 - 1;
            } else if($post_data_3 == 'down' && $key1 != count($_SESSION["l_d_kankou_spots_id"]) - 1 ) {
                $key2 = $key1 + 1;
            }
            //key2が設定されていればswap
            if ($key2 != -1) {
                $first = $_SESSION["l_d_kankou_spots_id"][$key1];
                $second = $_SESSION["l_d_kankou_spots_id"][$key2];
                $_SESSION["l_d_kankou_spots_id"][$key2] = $first;
                $_SESSION["l_d_kankou_spots_id"][$key1] = $second;
            }
            $return = "l_d_name";
        } else {$return = "";}
        break;
    case "3":
        if (isset($_SESSION["d_g_kankou_spots_id"])) {
            //入れ替える場所指定
            $key1 = array_search($post_data_2, $_SESSION["d_g_kankou_spots_id"]);
            if($post_data_3 == 'up' && $key1 != 0){
                $key2 = $key1 - 1;
            } else if($post_data_3 == 'down' && $key1 != count($_SESSION["d_g_kankou_spots_id"]) - 1 ) {
                $key2 = $key1 + 1;
            }
            //key2が設定されていればswap
            if ($key2 != -1) {
                $first = $_SESSION["d_g_kankou_spots_id"][$key1];
                $second = $_SESSION["d_g_kankou_spots_id"][$key2];
                $_SESSION["d_g_kankou_spots_id"][$key2] = $first;
                $_SESSION["d_g_kankou_spots_id"][$key1] = $second;
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
