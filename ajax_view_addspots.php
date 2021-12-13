<?php
session_start();
require_once("connect_database.php");

$post_data_1 = $_POST['post_data_1'];
$post_data_2 = json_decode($_POST['post_data_2'], true);

try{

    $stmt1 = $pdo->prepare("SELECT * FROM minatomirai_shop_data where id = :id");
    $stmt1 -> bindParam(":id", $post_data_1, PDO::PARAM_INT);
    $stmt1 -> execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT * FROM minatomirai_station_data where id = :id");
    $stmt2 -> bindParam(":id", $post_data_1, PDO::PARAM_INT);
    $stmt2 -> execute();
    $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

}catch(PDOException $e){
    //デバッグ用
    echo $e->getMessage();
}

function unset_spots()
{
    if (isset($_SESSION["s_l_kankou_spots_id"])) {
        unset($_SESSION["s_l_kankou_spots_id"]);
    }
    if (isset($_SESSION["l_d_kankou_spots_id"])) {
        unset($_SESSION["l_d_kankou_spots_id"]);
    }
    if (isset($_SESSION["d_g_kankou_spots_id"])) {
        unset($_SESSION["d_g_kankou_spots_id"]);
    }
}

//post_data_2の値によって返す配列を変える
switch ($post_data_2) {
    case "1":
        $_SESSION["start_station_id"] = $post_data_1;
        $frame_id = "start_name";
        $spot_name = $result2["name"];
        break;
    case "2":
        $_SESSION["lanch_id"] = $post_data_1;
        $frame_id = "lanch_name";
        $spot_name = $result1["name"];
        break;
    case "3":
        $_SESSION["dinner_id"] = $post_data_1;
        $frame_id = "dinner_name";
        $spot_name = $result1["name"];
        break;
    case "4":
        $_SESSION["goal_station_id"] = $post_data_1;
        $frame_id = "goal_name";
        $spot_name = $result2["name"];
        break;
}
unset_spots();
$return_array = array($spot_name, $frame_id);

echo json_encode($return_array);
?>