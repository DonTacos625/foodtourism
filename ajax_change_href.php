<?php
session_start();

//$post_data_1 = $_POST['post_data_1'];
$post_data_1 = json_decode($_POST['post_data_1'], true);

$not_set_station = "";
$not_set_food = "";
if (!isset($_SESSION["start_station_id"]) || !isset($_SESSION["goal_station_id"])) {
    $not_set_station = "1";
}
if (!isset($_SESSION["lanch_id"]) || !isset($_SESSION["dinner_id"])) {
    $not_set_food = "1";
}

$return_array = array($not_set_station, $not_set_food);

echo json_encode($return_array);
?>