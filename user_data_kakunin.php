<?php
session_start();
require "connect_database.php";

$user_id = $_SESSION["user"];

try {
    //ユーザー情報書き込み
    $userdatastmt = $pdo->prepare("SELECT * FROM userdata.$user_id WHERE start_id IS NOT NULL ");
    $userdatastmt->execute();

    $userdatastmt1 = $pdo->prepare("SELECT * FROM userdata.$user_id WHERE s_l_ids IS NOT NULL ");
    $userdatastmt1->execute();
    $userdatastmt2 = $pdo->prepare("SELECT * FROM userdata.$user_id WHERE l_d_ids IS NOT NULL ");
    $userdatastmt2->execute();
    $userdatastmt3 = $pdo->prepare("SELECT * FROM userdata.$user_id WHERE d_g_ids IS NOT NULL ");
    $userdatastmt3->execute();
} catch (PDOException $e) {
    //デバッグ用
    echo $e->getMessage();
}


foreach ($userdatastmt as $row){
    $start[] = $row["start_id"];
    $lanch[] = $row["lanch_id"];
    $dinner[] = $row["dinner_id"];
    $goal[] = $row["goal_id"];
}

foreach ($userdatastmt1 as $row1){
    $s_ls[] = $row1["s_l_ids"];
}
foreach ($userdatastmt2 as $row2){
    $l_ds[] = $row2["l_d_ids"];
}
foreach ($userdatastmt3 as $row3){
    $d_gs[] = $row3["d_g_ids"];
}

?>

<script>
    
    let start = <?php echo json_encode($start); ?>;
    let lanch = <?php echo json_encode($lanch); ?>;
    let dinner = <?php echo json_encode($dinner); ?>;
    let goal = <?php echo json_encode($goal); ?>;

    let result1 = <?php echo json_encode($s_ls); ?>;
    let result2 = <?php echo json_encode($l_ds); ?>;
    let result3 = <?php echo json_encode($d_gs); ?>;

    for (var j = 0; j < 1; j++) {
        document.write(start[j] + ",");
        document.write(lanch[j] + ",");
        document.write(dinner[j] + ",");
        document.write(goal[j] + ",");
    }
    document.write("\n");

    for (var j = 0; j < result3.length; j++) {
        document.write(result3[j] + ",");
    }
    
    
</script>

<html>

<head>

</head>

</html>