<?php
session_start();

//$post_data_1 = $_POST['post_data_1'];
$post_data_1 = json_decode($_POST['post_data_1'], true);
//$post_data_2 = json_decode($_POST['post_data_2'], true);
$post_data_2 = $_POST['post_data_2'];

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

echo json_encode($return);
?>
