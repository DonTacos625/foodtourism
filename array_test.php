<?php
session_start();

$s_l_kankou_spots_id[] = ["S_L1", "座標", 11];

$l_d_kankou_spots_id[] = ["L_D1", "座標", 21];

$d_g_kankou_spots_id[] = ["D_G1", "座標", 31];

//keikaku作成
$keikaku[] = ["START", "座標", "1"];
foreach ($s_l_kankou_spots_id as $s_l_add) {
    $keikaku[] = $s_l_add;
}
$keikaku[] = ["LANCH", "座標", "2"];
foreach ($l_d_kankou_spots_id as $l_d_add) {
    $keikaku[] = $l_d_add;
}
$keikaku[] = ["DINNER", "座標", "3"];
foreach ($d_g_kankou_spots_id as $d_g_add) {
    $keikaku[] = $d_g_add;
}
$keikaku[] = ["GOAL", "座標", "4"];

/*
$id=5;
$id2=2;

$_SESSION["s_l_kankou_spots_id"] = [$id];

$_SESSION["s_l_kankou_spots_id"][] = $id2;
*/

//spotのidを$post_data_2に、$modeの方向にswap
$post_data_2 = 41;
$mode = "up";

//swap
/*
$key1 = array_search($post_data_2, $_SESSION["s_l_kankou_spots_id"]);
if ($mode == "up") {
    $key2 = $key1 - 1;
} else if ($mode == "down") {
    $key2 = $key1 + 1;
}

$first = $_SESSION["s_l_kankou_spots_id"][$key1];
$second = $_SESSION["s_l_kankou_spots_id"][$key2];

$_SESSION["s_l_kankou_spots_id"][$key2] = $first;
$_SESSION["s_l_kankou_spots_id"][$key1] = $second;
*/

if (in_array(20, $_SESSION["s_l_kankou_spots_id"])){
    echo "10は存在する";
}

?>

<script>
    let keikaku = <?php echo json_encode($keikaku); ?>;
    let session = <?php echo json_encode($_SESSION["s_l_kankou_spots_id"]); ?>;

    /*
    for (var j = 0; j < keikaku.length; j++) {
        document.write(keikaku[j][0]);
    }
    */
    for (var j = 0; j < session.length; j++) {
        document.write(session[j] + ",");
    }

</script>

<html>

</html>