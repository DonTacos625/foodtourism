<?php
session_start();

//$post_data_1 = $_POST['post_data_1'];
$post_data_1 = json_decode($_POST['post_data_1'], true);

switch ($post_data_1) {
    case "1":
        if (isset($_SESSION["s_l_kankou_spots_id"])) {
            unset($_SESSION["s_l_kankou_spots_id"]);
            $return = "s_l_name";
        } else {$return = "";}
        break;
    case "2":
        if (isset($_SESSION["l_d_kankou_spots_id"])) {
            unset($_SESSION["l_d_kankou_spots_id"]);
            $return = "l_d_name";
        } else {$return = "";}
        break;
    case "3":
        if (isset($_SESSION["d_g_kankou_spots_id"])) {
            unset($_SESSION["d_g_kankou_spots_id"]);
            $return = "d_g_name";
        } else {$return = "";}
        break;
}

echo json_encode($return);
?>
