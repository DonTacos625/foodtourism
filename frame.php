<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["user"])) {
    header("Location: logout.php");
    exit;
}

//DB接続
require "cfg_test.php";

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

    $framestmt = $pdo->prepare("SELECT * FROM userinfo WHERE id = :id");
    $framestmt->bindParam(":id", $_SESSION["user"]);
    $framestmt->execute();
    $frameresult = $framestmt->fetch(PDO::FETCH_ASSOC);

    $framestmt1 = $pdo->prepare("SELECT * FROM minatomirai_shop_data WHERE id = :id");
    $framestmt1->bindParam(":id", $_SESSION["lanch_id"]);
    $framestmt1->execute();
    $frameresult1 = $framestmt1->fetch(PDO::FETCH_ASSOC);

    $framestmt2 = $pdo->prepare("SELECT * FROM minatomirai_shop_data WHERE id = :id");
    $framestmt2->bindParam(":id", $_SESSION["dinner_id"]);
    $framestmt2->execute();
    $frameresult2 = $framestmt2->fetch(PDO::FETCH_ASSOC);

    $framestmt3 = $pdo->prepare("SELECT * FROM minatomirai_station_data WHERE id = :id");
    $framestmt3->bindParam(":id", $_SESSION["start_station_id"]);
    $framestmt3->execute();
    $frameresult3 = $framestmt3->fetch(PDO::FETCH_ASSOC);

    $framestmt4 = $pdo->prepare("SELECT * FROM minatomirai_station_data WHERE id = :id");
    $framestmt4->bindParam(":id", $_SESSION["goal_station_id"]);
    $framestmt4->execute();
    $frameresult4 = $framestmt4->fetch(PDO::FETCH_ASSOC);


    $framestmt5 = $pdo->prepare("SELECT * FROM minatomirai_kankou_data WHERE id = :id");
    $framestmt5->bindParam(":id", $_SESSION["s_l_kankou_spots_id"]);
    $framestmt5->execute();
    $frameresult5 = $framestmt5->fetch(PDO::FETCH_ASSOC);

    $framestmt6 = $pdo->prepare("SELECT * FROM minatomirai_kankou_data WHERE id = :id");
    $framestmt6->bindParam(":id", $_SESSION["l_d_kankou_spots_id"]);
    $framestmt6->execute();
    $frameresult6 = $framestmt6->fetch(PDO::FETCH_ASSOC);

    $framestmt7 = $pdo->prepare("SELECT * FROM minatomirai_kankou_data WHERE id = :id");
    $framestmt7->bindParam(":id", $_SESSION["d_g_kankou_spots_id"]);
    $framestmt7->execute();
    $frameresult7 = $framestmt7->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

if (!isset($_SESSION["lanch_id"])) {
    $lanch_name = "昼食地点を設定してください";
} else {
    $lanch_name = $frameresult1["name"];
}
if (!isset($_SESSION["dinner_id"])) {
    $dinner_name = "夕食地点を設定してください";
} else {
    $dinner_name = $frameresult2["name"];
}

if (!isset($_SESSION["start_station_id"])) {
    $start_station_name = "開始駅を設定してください";
} else {
    $start_station_name = $frameresult3["name"];
}
if (!isset($_SESSION["goal_station_id"])) {
    $goal_station_name = "終了駅を設定してください";
} else {
    $goal_station_name = $frameresult4["name"];
}

if (!isset($_SESSION["s_l_kankou_spots_id"])) {
    $s_l_spot_name = "設定されていません";
} else {
    $s_l_spot_name = $frameresult5["name"];
}
if (!isset($_SESSION["l_d_kankou_spots_id"])) {
    $l_d_spot_name = "設定されていません";
} else {
    $l_d_spot_name = $frameresult6["name"];
}
if (!isset($_SESSION["d_g_kankou_spots_id"])) {
    $d_g_spot_name = "設定されていません";
} else {
    $d_g_spot_name = $frameresult7["name"];
}
?>


<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>

    </style>
</head>

<body>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script>
        /*
        function post_dinner(e) {
            jQuery(function($) {
                $.ajax({
                    url: 'ajax2.php',
                    type: "POST",
                    dataType: 'json',
                    data: {
                        post_data_1: e
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("ajax通信に失敗しました");
                    },
                    success: function(data) {
                        //alert("夜は" + data[0]);
                        $_SESSION["dinner"] = data[0];
                        var dinner_name = document.getElementById('dinner');
                        dinner_name.innerHTML = data[0];
                    }
                });
            });
        };
        */

        function update_frame(data, id) {
            const update = document.getElementById(id);
            update.innerHTML = data;
            console.log(update.innerHTML);
        }

        function remove_spot(time) {
            jQuery(function($) {
                $.ajax({
                    url: './ajax_remove_spot.php',
                    type: "POST",
                    dataType: 'json',
                    data: {
                        post_data_1: time
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("ajax通信に失敗しました");
                    },
                    success: function(data) {
                        //alert("返り値は" + data);
                        if(!(data == "")){
                        update_frame("設定されていません", data);
                        }
                    }
                });
            });
        };
    </script>


    <h1>横浜みなとみらいフードツーリズム計画作成システム</h1>

    <ul id="dropmenu">
        <li><a href="home.php">ホーム</a></li>

        <li><a href="explain.php">使い方</a></li>

        <li><a>観光計画作成</a>
            <ul>
                <li><a href="set_station.php">開始・終了地点決定</a></li>
                <li><a href="search_form.php">飲食店検索・決定</a></li>
                <li><a href="keiro.php">観光スポット選択・決定</a></li>
            </ul>
        </li>

        <li><a>マイページ</a>
            <ul>
                <li><a href="editpassword.php">パスワード変更</a></li>
            </ul>
        </li>

        <li><a href="logout.php">ログアウト</a></li>
    </ul>

    <div id="leftbox">
        <h2>会員情報</h2>
        <p>
            ID:<?php echo htmlspecialchars($_SESSION["user"], ENT_QUOTES); ?><br>

            年代:<?php echo htmlspecialchars($frameresult["age"], ENT_QUOTES); ?>代<br>

            性別:<?php if (!$frameresult["gender"]) { ?>
            男性
        <?php } elseif ($frameresult["gender"]) { ?>
            女性
        <?php } ?><br>
        </p>

        <h2>現在の観光計画</h2>

        開始駅：<div id="start_name"><?php echo htmlspecialchars($start_station_name, ENT_QUOTES); ?></div><br>

        経由地点1：<button type="button" id="remove_s_l" onclick="remove_spot('1')">削除</button><div id="s_l_name"><?php echo htmlspecialchars($s_l_spot_name, ENT_QUOTES); ?></div><br>

        昼食予定地:<div id="lanch_name"><?php echo htmlspecialchars($lanch_name, ENT_QUOTES); ?></div><br>

        経由地点2：<button type="button" id="remove_l_d" onclick="remove_spot('2')">削除</button><div id="l_d_name"><?php echo htmlspecialchars($l_d_spot_name, ENT_QUOTES); ?></div><br>

        夕食予定地:<div id="dinner_name"><?php echo htmlspecialchars($dinner_name, ENT_QUOTES); ?></div><br>

        経由地点3：<button type="button" id="remove_d_g" onclick="remove_spot('3')">削除</button><div id="d_g_name"><?php echo htmlspecialchars($d_g_spot_name, ENT_QUOTES); ?></div><br>

        終了駅：<div id="goal_name"><?php echo htmlspecialchars($goal_station_name, ENT_QUOTES); ?></div><br>

        <h2>アンケート</h2>
        <?php
        if ($frameresult["survey"]) {
            print "<form action=\"\" method=\"POST\">";
            print "<input type=\"submit\" id=\"survey\" name=\"survey\" value=\"回答する\" onClick=\"window.open('https://goo.gl/forms/ze7wIc42WS2JsPzk2','_blank')\"><br>";
            print "</form>";
            print "回答は<font color=\"red\">1回</font>のみです<br>";
            print "<b>システムを1度以上利用してからご回答ください</b>";
        } else {
            print "ご回答ありがとうございました";
        }
        ?>
    </div>

    <div id="toggle_menu">
        <label for="menu_label">≡メニュー</label>
        <input type="checkbox" id="menu_label" />

        <div id="menu">
            <ul>
                <li><a href="home.php">ホーム</a></li>

                <li><a href="explain.php">使い方</a></li>

                <li><a>観光計画作成</a>
                    <ul>
                        <li><a href="set_station.php">開始・終了地点決定</a></li>
                        <li><a href="search_form.php">飲食店検索・決定</a></li>
                        <li><a href="keiro.php">観光スポット選択・決定</a></li>
                    </ul>
                </li>

                <li><a>マイページ</a>
                    <ul>
                        <li><a href="editpassword.php">パスワード変更</a></li>
                    </ul>
                </li>

                <li><a href="logout.php">ログアウト</a></li>
            </ul>
        </div>
    </div>
</body>

</html>