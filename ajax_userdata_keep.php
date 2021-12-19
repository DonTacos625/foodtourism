<?php

/*
DB userinfo
id,pass,age,gender

DB userdata
start_id, s_l_ids, lanch_id, l_d_ids, dinner_id, d_g_ids, goal_id
(all integer)
*/
session_start();

//保存時の文面
$post_data_1 = $_POST['post_data_1'];

//DB接続
require "connect_database.php";

//user_idを格納
$user_id = $_SESSION["user"];

if (isset($_SESSION["start_station_id"]) && isset($_SESSION["lanch_id"]) && isset($_SESSION["dinner_id"]) && isset($_SESSION["goal_station_id"])) {
    $start_id = $_SESSION["start_station_id"];
    $lanch_id = $_SESSION["lanch_id"];
    $dinner_id = $_SESSION["dinner_id"];
    $goal_id = $_SESSION["goal_station_id"];

    try {
        //今までのテーブルをまずリセット
        $stmt0 = $pdo->prepare("DELETE FROM userdata." . $user_id . " ");
        $stmt0->execute();
        
        //ユーザー情報書き込み
        $stmt = $pdo->prepare("INSERT INTO userdata." . $user_id . "(start_id, lanch_id, dinner_id, goal_id) VALUES(:start_id, :lanch_id, :dinner_id, :goal_id)");
        $stmt->bindParam(":start_id", $start_id, PDO::PARAM_INT);
        $stmt->bindParam(":lanch_id", $lanch_id, PDO::PARAM_INT);
        $stmt->bindParam(":dinner_id", $dinner_id, PDO::PARAM_INT);
        $stmt->bindParam(":goal_id", $goal_id, PDO::PARAM_INT);
        $stmt->execute();

        if (isset($_SESSION["s_l_kankou_spots_id"])) {
            foreach ($_SESSION["s_l_kankou_spots_id"] as $s_l_id) {
                $stmt1 = $pdo->prepare("INSERT INTO userdata." . $user_id . "(s_l_ids) VALUES(:s_l_id)");
                $stmt1->bindParam(":s_l_id", $s_l_id);
                $stmt1->execute();
            }
        }
        if (isset($_SESSION["l_d_kankou_spots_id"])) {
            foreach ($_SESSION["l_d_kankou_spots_id"] as $l_d_id) {
                $stmt2 = $pdo->prepare("INSERT INTO userdata." . $user_id . "(l_d_ids) VALUES(:l_d_id)");
                $stmt2->bindParam(":l_d_id", $l_d_id);
                $stmt2->execute();
            }
        }
        if (isset($_SESSION["d_g_kankou_spots_id"])) {
            foreach ($_SESSION["d_g_kankou_spots_id"] as $d_g_id) {
                $stmt3 = $pdo->prepare("INSERT INTO userdata." . $user_id . "(d_g_ids) VALUES(:d_g_id)");
                $stmt3->bindParam(":d_g_id", $d_g_id);
                $stmt3->execute();
            }
        }
    } catch (PDOException $e) {
        //デバッグ用
        echo $e->getMessage();
    }
    $return = $post_data_1;
} else {
    $return = "開始・終了駅と昼・夕食店舗を設定してください";
}

echo json_encode($return);
