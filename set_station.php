<?php

require "frame.php";

if (!empty($_GET["not_set_station"])) {
    $message = "先に観光を開始・終了する駅を設定してください";
} else {
    $message = "";
}

try {

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

    //SQL文を実行して、結果を$stmtに代入する。
    $stmt = $pdo->prepare(" SELECT * FROM minatomirai_station_data ");
    $stmt->execute();

    $stmt2 = $pdo->prepare(" SELECT * FROM minatomirai_station_data ");
    $stmt2->execute();
} catch (PDOException $e) {
    echo "失敗:" . $e->getMessage() . "\n";
    exit();
}

?>

<html>

<head>
    <meta charset="UTF-8">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-214561408-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-214561408-1');
    </script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <title>開始・終了駅決定</title>

    <style>
        #editbox {
            float: left;
            width: 500px;
            margin-left: 5px;
        }

        #editbox h2 {
            margin: 0px;
        }

        #editbox h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        #editbox th {
            width: 100px;
            background: #0099FF;
            color: #fff;
            white-space: nowrap;
            margin: 3px;
            padding: 2px;
            border-left: 5px solid #000080;
        }

        #editbox td {
            text-align: center;
        }

        #editbox input {
            margin-left: 10px;
        }

        #editbox input:first-child {
            margin-left: 0px;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            h2 {
                font-size: 20px;
            }

            h3 {
                font-size: 18px;
            }
        }

        @media screen and (max-width:768px) {
            h2 {
                font-size: 19px;
            }

            h3 {
                font-size: 17px;
            }

            #editbox {
                width: auto;
                margin: 0px;
            }

            #editbox th {
                font-size: 15px;
            }
        }
    </style>

</head>

<script type="text/javascript">
    $start_id = "";
    $goal_id = "";

    function set_station(id, mode) {
        if (mode == "start") {
            $start_id = id;
        } else if (mode == "goal") {
            $goal_id = id;
        }
    };

    function stations() {
        if (!($start_id == "") && !($goal_id == "")) {
            post_stations($start_id, $goal_id);
        } else {
            alert("開始駅と終了駅の両方を設定してください");
        }
    };

    function post_stations(start_id, goal_id) {
        jQuery(function($) {
            $.ajax({
                url: "./ajax_station.php",
                type: "POST",
                dataType: "json",
                data: {
                    post_data_1: start_id,
                    post_data_2: goal_id
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert("ajax通信に失敗しました");
                },
                success: function(response) {
                    //frameの関数
                    update_frame(response[0], "start_name");
                    update_frame(response[1], "goal_name");
                }
            });
        });
    };

    /*
    //デバッグ用
    function debug() {
        const update = document.getElementById("debug");
        update.innerHTML = $start_id;
        console.log(update.innerHTML);
    };

    function debug2() {
        const update = document.getElementById("debug2");
        update.innerHTML = $goal_id;
        console.log(update.innerHTML);
    };
    */
</script>

<body>
    <!-- <button type="button" onclick="debug()">start更新</button>
    <button type="button" onclick="debug2()">goal更新</button>
    <div id="debug">start現在</div><br>
    <div id="debug2">goal現在</div>
-->
    <div>
        <font color="#ff0000"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></font>
    </div>
    <div id="editbox">
        <h2>開始駅を選択する：</h2>
        <select name="start_station_id" size="1" onclick="set_station(value, 'start')">
            <option value=""> 開始駅を選択してください </option>
            <?php foreach ($stmt as $row) : ?>
                <option value=<?php echo $row["id"]; ?>> <?php echo $row["name"]; ?> </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <h2>終了駅を選択する：</h2>
        <select name="goal_station_id" size="1" onclick="set_station(value, 'goal')">
            <option value=""> 終了駅を選択してください </option>
            <?php foreach ($stmt2 as $row2) : ?>
                <option value=<?php echo $row2["id"]; ?>> <?php echo $row2["name"]; ?> </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <button type="button" onclick="stations() ; change_href()">決定</button>
        <div><br>
            <a href="search_form.php" onclick="change_href()">飲食店の検索・決定へ</a>
</body>

</html>