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

echo json_encode($return);
?>
