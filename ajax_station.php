<?php
session_start();
require_once("cfg_test.php");

$post_data_1 = $_POST['post_data_1'];
$post_data_2 = $_POST['post_data_2'];

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
    
    $stmt1 = $pdo->prepare("SELECT * FROM minatomirai_station_data where id = :id");
    $stmt1 -> bindParam(":id", $post_data_1, PDO::PARAM_INT);
    $stmt1 -> execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT * FROM minatomirai_station_data where id = :id");
    $stmt2 -> bindParam(":id", $post_data_2, PDO::PARAM_INT);
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

$_SESSION["start_station_id"] = $post_data_1;
$_SESSION["goal_station_id"] = $post_data_2;
unset_spots();

$return_array = array($result1["name"], $result2["name"]);

echo json_encode($return_array);
?>