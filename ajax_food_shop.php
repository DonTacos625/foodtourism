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

    $stmt1 = $pdo->prepare("SELECT * FROM minatomirai_shop_data where id = :id");
    $stmt1 -> bindParam(":id", $post_data_1, PDO::PARAM_INT);
    $stmt1 -> execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

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

switch ($post_data_2) {
    case "1":
        $_SESSION["lanch_id"] = $post_data_1;
        $frame_id = "lanch_name";
        unset_spots();
        break;
    case "2":
        $_SESSION["dinner_id"] = $post_data_1;
        $frame_id = "dinner_name";
        unset_spots();
        break;
}

$return_array = array($result1["name"], $frame_id);

echo json_encode($return_array);
?>