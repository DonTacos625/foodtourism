<?php
session_start();
require_once("connect_database.php");

$post_data_1 = $_POST['post_data_1'];

//stations_id設定
if (!isset($_SESSION["start_station_id"])) {
    $start_station_id = 0;
} else {
    $start_station_id = $_SESSION["start_station_id"];
}
if (!isset($_SESSION["goal_station_id"])) {
    $goal_station_id = 0;
} else {
    $goal_station_id = $_SESSION["goal_station_id"];
}

//food_shops_id設定
if (!isset($_SESSION["lanch_id"])) {
    $lanch_shop_id = 0;
} else {
    $lanch_shop_id = $_SESSION["lanch_id"];
}
if (!isset($_SESSION["dinner_id"])) {
    $dinner_shop_id = 0;
} else {
    $dinner_shop_id = $_SESSION["dinner_id"];
}

//kankou_spots_id設定
if (!isset($_SESSION["s_l_kankou_spots_id"])) {
    $s_l_ids = [0];
} else {
    $s_l_ids = $_SESSION["s_l_kankou_spots_id"];
}
if (!isset($_SESSION["l_d_kankou_spots_id"])) {
    $l_d_ids = [0];
} else {
    $l_d_ids = $_SESSION["l_d_kankou_spots_id"];
}
if (!isset($_SESSION["d_g_kankou_spots_id"])) {
    $d_g_ids = [0];
} else {
    $d_g_ids = $_SESSION["d_g_kankou_spots_id"];
}

//DB接続
try {
    if (!isset($_SESSION["start_station_id"])) {
        $start_station_info = [0, 0, "start"];
    } else {
        $stmt1 = $pdo->prepare("SELECT * FROM minatomirai_station_data WHERE id = :id");
        $stmt1->bindParam(":id", $start_station_id);
        $stmt1->execute();
        $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $start_station_info = [$result1["x"], $result1["y"], "start"];
    }

    if (!isset($_SESSION["lanch_id"])) {
        $lanch_info = [0, 0, "lanch"];
    } else {
        $stmt2 = $pdo->prepare("SELECT * FROM minatomirai_shop_data WHERE id = :id");
        $stmt2->bindParam(":id", $lanch_shop_id);
        $stmt2->execute();
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        $lanch_info = [$result2["x"], $result2["y"], "lanch"];
    }

    if (!isset($_SESSION["dinner_id"])) {
        $dinner_info = [0, 0, "dinner"];
    } else {
        $stmt3 = $pdo->prepare("SELECT * FROM minatomirai_shop_data WHERE id = :id");
        $stmt3->bindParam(":id", $dinner_shop_id);
        $stmt3->execute();
        $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
        $dinner_info = [$result3["x"], $result3["y"], "dinner"];
    }

    if (!isset($_SESSION["goal_station_id"])) {
        $goal_station_info = [0, 0, "goal"];
    } else {
        $stmt4 = $pdo->prepare("SELECT * FROM minatomirai_station_data WHERE id = :id");
        $stmt4->bindParam(":id", $goal_station_id);
        $stmt4->execute();
        $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        $goal_station_info = [$result4["x"], $result4["y"], "goal"];
    }

    $spot_count = 10;
    if (!isset($_SESSION["s_l_kankou_spots_id"])) {
        $s_l_kankou_spots_id = [[0, 0, 0]];
    } else {
        foreach ($_SESSION["s_l_kankou_spots_id"] as $s_l) {
            $stmt5 = $pdo->prepare("SELECT * FROM minatomirai_kankou_data WHERE id = :id");
            $stmt5->bindParam(":id", $s_l);
            $stmt5->execute();
            $result5 = $stmt5->fetch(PDO::FETCH_ASSOC);
            $spot_count += 1;
            $s_l_kankou_spots_id[] = [$result5["x"], $result5["y"], $spot_count];
        }
    }
    $spot_count = 20;
    if (!isset($_SESSION["l_d_kankou_spots_id"])) {
        $l_d_kankou_spots_id = [[0, 0, 0]];
    } else {
        foreach ($_SESSION["l_d_kankou_spots_id"] as $l_d) {
            $stmt6 = $pdo->prepare("SELECT * FROM minatomirai_kankou_data WHERE id = :id");
            $stmt6->bindParam(":id", $l_d);
            $stmt6->execute();
            $result6 = $stmt6->fetch(PDO::FETCH_ASSOC);
            $spot_count += 1;
            $l_d_kankou_spots_id[] = [$result6["x"], $result6["y"], $spot_count];
        }
    }
    $spot_count = 30;
    if (!isset($_SESSION["d_g_kankou_spots_id"])) {
        $d_g_kankou_spots_id = [[0, 0, 0]];
    } else {
        foreach ($_SESSION["d_g_kankou_spots_id"] as $d_g) {
            $stmt7 = $pdo->prepare("SELECT * FROM minatomirai_kankou_data WHERE id = :id");
            $stmt7->bindParam(":id", $d_g);
            $stmt7->execute();
            $result7 = $stmt7->fetch(PDO::FETCH_ASSOC);
            $spot_count += 1;
            $d_g_kankou_spots_id[] = [$result7["x"], $result7["y"], $spot_count];
        }
    }
} catch (PDOException $e) {
}

//keikakuは目的地の配列
//keikakuの配列作成
$keikaku[] = $start_station_info;
foreach ($s_l_kankou_spots_id as $s_l_add) {
    $keikaku[] = $s_l_add;
}
$keikaku[] = $lanch_info;
foreach ($l_d_kankou_spots_id as $l_d_add) {
    $keikaku[] = $l_d_add;
}
$keikaku[] = $dinner_info;
foreach ($d_g_kankou_spots_id as $d_g_add) {
    $keikaku[] = $d_g_add;
}
$keikaku[] = $goal_station_info;

$return_array = $keikaku;

echo json_encode($return_array);
?>