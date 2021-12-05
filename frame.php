<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["user"])) {
    header("Location: logout.php");
    exit;
}

//DB接続
require "connect_database.php";

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
    <!--<link rel="stylesheet" href="style.css">-->
    <style>
        body {
            background: linear-gradient(90deg, #99ffff, #ffffff);
        }

        h1 {
            margin: 0px;
        }

        #dropmenu {
            list-style-type: none;
            position: relative;
            width: 78vw;
            height: 35px;
            padding: 0;
            background: #0099ff;
            border-bottom: 5px solid #00ffff;
            border-radius: 3px 3px 0 0;
            z-index: 3;
        }

        #dropmenu li {
            position: relative;
            width: 20%;
            float: left;
            margin: 0;
            padding: 0;
            text-align: center;
            border-right: 1px solid #FFFFFF;
            box-sizing: border-box;
        }

        #dropmenu li a {
            display: block;
            margin: 0;
            padding: 13px 0 11px;
            color: #FFFFFF;
            font-size: 17px;
            font-weight: bold;
            line-height: 1;
            text-decoration: none;
        }

        #dropmenu li ul {
            list-style: none;
            position: absolute;
            top: 100%;
            left: 0;
            margin: 0;
            padding: 0;
            border-radius: 0 0 3px 3px;
        }

        #dropmenu li ul li {
            overflow: hidden;
            width: 100%;
            height: 0;
            color: #fff;
            -moz-transition: .2s;
            -webkit-transition: .2s;
            -o-transition: .2s;
            -ms-transition: .2s;
            transition: .2s;
        }

        #dropmenu li ul li a {
            padding: 6px 8px;
            background: #0099FF;
            text-align: left;
            font-size: 15px;
            font-weight: normal;
        }

        #dropmenu li:hover>a {
            background: #0066ff;
        }

        #dropmenu>li:hover>a {
            border-radius: 3px 3px 0 0;
        }

        #dropmenu li:hover ul li {
            overflow: visible;
            height: 30px;
            border-bottom: 3px solid #0066ff;
            border-right: 0px;
        }

        #dropmenu li:hover ul li:last-child a {
            border-radius: 0 0 3px 3px;
        }

        #leftbox {
            position: relative;
            float: right;
            width: 20vw;
            border-right: 3px solid #0099FF;
            z-index: 2;
        }

        #leftbox h2 {
            background: #0099FF;
            color: #FFFFFF;
            margin-right: 5px;
            border-left: 5px solid #000080;
        }

        #leftbox p {
            margin-left: 10px;
        }

        @media screen and (min-width:769px) {
            #toggle_menu {
                display: none;
            }
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            h1 {
                font-size: 25px;
            }

            #dropmenu {
                width: 70vw;
                height: 30px;
                border-bottom: 4px solid #000080;
            }

            #dropmenu li a {
                padding: 7px 0 9px;
                font-size: 16px;
            }

            #dropmenu li ul li a {
                padding: 4px 6px;
                font-size: 13px;
            }

            #dropmenu li:hover ul li {
                height: 23px;
                border-bottom: 2px solid #000080;
            }

            #leftbox h2 {
                background: #0099FF;
                color: #FFFFFF;
                margin-right: 4px;
                border-left: 4px solid #000080;
                font-size: 17px;
            }
        }

        @media screen and (max-width:768px) {
            h1 {
                font-size: 22px;
            }

            h2 {
                margin: 0px;
                font-size: 19px;
            }

            #dropmenu {
                display: none;
            }

            #leftbox {
                display: none;
            }

            #toggle_menu {
                padding: 0px;
                margin-bottom: 5px;
                border-bottom: 1px solid #000000;
                ;
            }

            #toggle_menu label {
                font-weight: bold;
                border: solid 2px black;
                cursor: pointer;
            }

            #toggle_menu>input {
                display: none;
            }

            #toggle_menu #menu {
                height: 0;
                padding: 0;
                overflow: hidden;
                opacity: 0;
                transition: 0.2s;
            }

            #toggle_menu input:checked~#menu {
                height: auto;
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script>
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
                        if (!(data == "")) {
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
                <li><a href="set_station.php">開始・終了駅の設定</a></li>
                <li><a href="search_form.php">飲食店の検索・決定</a></li>
                <li><a id="keiro" href="">観光スポット選択</a></li>
            </ul>
        </li>
        <li><a href="view.php">スポット一覧</a></li>

        <li><a>マイページ</a>
            <ul>
                <li><a href="editpassword.php">パスワード変更</a></li>
                <li><a href="logout.php">ログアウト</a></li>
            </ul>
        </li>

    </ul>

    <div id="leftbox">
        <h2>会員情報</h2>
        <p>
            <b>ID:</b><?php echo htmlspecialchars($_SESSION["user"], ENT_QUOTES); ?><br>

            <b>年代:</b><?php echo htmlspecialchars($frameresult["age"], ENT_QUOTES); ?>代<br>

            <b>性別:</b><?php if (!$frameresult["gender"]) { ?>
                男性
            <?php } elseif ($frameresult["gender"]) { ?>
                女性
            <?php } ?><br>
        </p>

        <h2>現在の観光計画</h2>

        <b>開始駅：</b>
        <div id="start_name"><?php echo htmlspecialchars($start_station_name, ENT_QUOTES); ?></div><br>

        <b>経由地点1：</b><button type="button" id="remove_s_l" onclick="remove_spot('1')">削除</button>
        <div id="s_l_name"><?php echo htmlspecialchars($s_l_spot_name, ENT_QUOTES); ?></div><br>

        <b>昼食予定地:</b>
        <div id="lanch_name"><?php echo htmlspecialchars($lanch_name, ENT_QUOTES); ?></div><br>

        <b>経由地点2：</b><button type="button" id="remove_l_d" onclick="remove_spot('2')">削除</button>
        <div id="l_d_name"><?php echo htmlspecialchars($l_d_spot_name, ENT_QUOTES); ?></div><br>

        <b>夕食予定地:</b>
        <div id="dinner_name"><?php echo htmlspecialchars($dinner_name, ENT_QUOTES); ?></div><br>

        <b>経由地点3：</b><button type="button" id="remove_d_g" onclick="remove_spot('3')">削除</button>
        <div id="d_g_name"><?php echo htmlspecialchars($d_g_spot_name, ENT_QUOTES); ?></div><br>

        <b>終了駅：</b>
        <div id="goal_name"><?php echo htmlspecialchars($goal_station_name, ENT_QUOTES); ?></div><br>

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
                        <li><a href="set_station.php">開始・終了駅の設定</a></li>
                        <li><a href="search_form.php">飲食店の検索・決定</a></li>
                        <li><a id="keiro" href="">観光スポット選択</a></li>
                    </ul>
                </li>

                <li><a href="view.php">スポット一覧</a></li>

                <li><a>マイページ</a>
                    <ul>
                        <li><a href="editpassword.php">パスワード変更</a></li>
                        <li><a href="logout.php">ログアウト</a></li>
                    </ul>
                </li>


            </ul>
        </div>
    </div>

    <div id="toggle_menu">
        <label for="menu_label2">≡設定情報</label>
        <input type="checkbox" id="menu_label2" />

        <div id="menu">
            <ul>
                <li>
                    <h2>会員情報</h2>
                    <ul>
                        <li><b>ID:</b><?php echo htmlspecialchars($_SESSION["user"], ENT_QUOTES); ?></li>

                        <li><b>年代:</b><?php echo htmlspecialchars($frameresult["age"], ENT_QUOTES); ?>代</a></li>
                        <li><b>性別:</b><?php if (!$frameresult["gender"]) { ?>
                                男性
                            <?php } elseif ($frameresult["gender"]) { ?>
                                女性
                            <?php } ?></li>
                    </ul>
                </li>

                <li>
                    <h2>現在の観光計画</h2>
                    <ul>
                        <li><b>開始駅：</b>
                            <div id="start_name"><?php echo htmlspecialchars($start_station_name, ENT_QUOTES); ?></div>
                        </li>

                        <li><b>経由地点1：</b><button type="button" id="remove_s_l" onclick="remove_spot('1')">削除</button>
                            <div id="s_l_name"><?php echo htmlspecialchars($s_l_spot_name, ENT_QUOTES); ?></div>
                        </li>

                        <li><b>昼食予定地:</b>
                            <div id="lanch_name"><?php echo htmlspecialchars($lanch_name, ENT_QUOTES); ?></div>
                        </li>

                        <li><b>経由地点2：</b><button type="button" id="remove_l_d" onclick="remove_spot('2')">削除</button>
                            <div id="l_d_name"><?php echo htmlspecialchars($l_d_spot_name, ENT_QUOTES); ?></div>
                        </li>

                        <li><b>夕食予定地:</b>
                            <div id="dinner_name"><?php echo htmlspecialchars($dinner_name, ENT_QUOTES); ?></div>
                        </li>

                        <li><b>経由地点3：</b><button type="button" id="remove_d_g" onclick="remove_spot('3')">削除</button>
                            <div id="d_g_name"><?php echo htmlspecialchars($d_g_spot_name, ENT_QUOTES); ?></div>
                        </li>

                        <li>
                            <div id="d_g_name"><b>終了駅：</b>
                                <div id="goal_name"><?php echo htmlspecialchars($goal_station_name, ENT_QUOTES); ?></div>
                        </li>
                    </ul>
                </li>

                <li>
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
                </li>

            </ul>
        </div>
    </div>

    <script>
        function change_href() {
            jQuery(function($) {
                var dummy ="1";
                $.ajax({
                    url: './ajax_change_href.php',
                    type: "POST",
                    dataType: 'json',
                    data: {
                        post_data_1: dummy
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("ajax通信に失敗しました");
                    },
                    success: function(data) {
                        //alert("返り値は" + data[0]);
                        $not_set_station = data[0];
                        $not_set_food = data[1];
                        const target = document.getElementById("keiro");
                        $url = "keiro.php";
                        if ($not_set_station == "1") {
                            $url = "set_station.php?not_set_station=1";
                        } else if ($not_set_food == "1") {
                            $url = "search_form.php?not_set_food=1";
                        } else {
                            $url = "keiro.php";
                        }
                        //alert($url);
                        target.href = $url;
                    }
                });
            });
        };
        
        change_href();
    </script>
</body>

</html>