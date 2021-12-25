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


    //謎 データベース接続するとセッション変数の配列の値が「Array」に変わってしまう不具合があった
    if (!isset($_SESSION["s_l_kankou_spots_id"])) {
        $s_l_spots_name = [["設定されていません", 0]];
    } else {
        foreach ($_SESSION["s_l_kankou_spots_id"] as $s_l) {
            $framestmt5 = $pdo->prepare("SELECT * FROM minatomirai_kankou_data WHERE id = :id");
            $framestmt5->bindParam(":id", $s_l);
            $framestmt5->execute();
            $frameresult5 = $framestmt5->fetch(PDO::FETCH_ASSOC);
            //$spot_count +=1;
            $s_l_spots_name[] = [$frameresult5["name"], $s_l];
        }
    }

    if (!isset($_SESSION["l_d_kankou_spots_id"])) {
        $l_d_spots_name = [["設定されていません", 0]];
    } else {
        foreach ($_SESSION["l_d_kankou_spots_id"] as $l_d) {
            $framestmt6 = $pdo->prepare("SELECT * FROM minatomirai_kankou_data WHERE id = :id");
            $framestmt6->bindParam(":id", $l_d);
            $framestmt6->execute();
            $frameresult6 = $framestmt6->fetch(PDO::FETCH_ASSOC);
            //$spot_count +=1;
            $l_d_spots_name[] = [$frameresult6["name"], $l_d];
        }
    }

    if (!isset($_SESSION["d_g_kankou_spots_id"])) {
        $d_g_spots_name = [["設定されていません", 0]];
    } else {
        foreach ($_SESSION["d_g_kankou_spots_id"] as $d_g) {
            $framestmt7 = $pdo->prepare("SELECT * FROM minatomirai_kankou_data WHERE id = :id");
            $framestmt7->bindParam(":id", $d_g);
            $framestmt7->execute();
            $frameresult7 = $framestmt7->fetch(PDO::FETCH_ASSOC);
            //$spot_count +=1;
            $d_g_spots_name[] = [$frameresult7["name"], $d_g];
        }
    }
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


function display_frame($name_row, $time)
{
    $count = 0;
    foreach ($name_row as $spot_name) {
        $count += 1;
        $frame_spot_name = " " . $count . ":" . $spot_name[0] . " ";
        print "
    <div id=\"frame_spot_name\">$frame_spot_name</div>
    <button class=\"btn2\" type=\"button\" id=\"removebtn\" value=$spot_name[1] onclick=\"remove_spot($time, value)\" title=\"このスポットを削除します\">×</button>
    <button type=\"button\" id=\"swapupbtn\" value=$spot_name[1] onclick=\"swap_spots($time, value, 'up')\" title=\"このスポットを一つ上に移動します\">↑</button>
    <button type=\"button\" id=\"swapdownbtn\" value=$spot_name[1] onclick=\"swap_spots($time, value, 'down')\" title=\"このスポットを一つ下に移動します\">↓</button><br>
    ";
    };
};

?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/copyright.css">
    <link rel="stylesheet" type="text/css" href="css/viewbox.css">
    <style>
        h1 {
            margin: 0px;
        }

        .search_form {
            line-height: 200%;
        }

        #dropmenu {
            list-style-type: none;
            position: relative;
            width: 77vw;
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
            border-right: 1px solid #99ffff;
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
            top: -70px;
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

        /*
        button.btn2 {
            color: #fff;
            background-color: #eb6100;
        }

        button.btn2:hover {
            color: #fff;
            background: #f56500;
        }

        button.btn2 {
            -webkit-box-shadow: 0 3px 5px rgba(0, 0, 0, 0.3);
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.3);
        }

        button.btn2 {
            border-radius: 50%;
            line-height: 100px;
            width: 20px;
            height: 20px;
            padding: 0;
        }
        */

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
                width: 75vw;
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
        //leftの情報を上書きする
        function update_frame(data, id) {
            const update = document.getElementById(id);
            update.innerHTML = data;
            //console.log(update.innerHTML);
        }

        //time(1,2,3)の時間帯のidが一致するスポットを削除する
        function remove_spot(time, id) {
            jQuery(function($) {
                $.ajax({
                    url: './ajax_remove_spot.php',
                    type: "POST",
                    dataType: 'json',
                    data: {
                        post_data_1: time,
                        post_data_2: id,
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("ajax通信に失敗しました");
                    },
                    success: function(response) {
                        //alert("削除されたのは" + response[0][0]);
                        /*
                        if (response.length < 1) {
                            alert("「" + response[0][0] + "」が削除されました");
                        }
                        */
                        overwrite(time, response, 0);
                        overwrite(time, response, 1);

                        //keiroの関数
                        kousin();
                    }
                });
            });
        };

        //time(1,2,3)の時間帯のidが一致するスポットをswapmode(up,down)によって上か下に入れ替える
        function swap_spots(time, id, swapmode) {
            jQuery(function($) {
                $.ajax({
                    url: './ajax_swap_spot.php',
                    type: "POST",
                    dataType: 'json',
                    data: {
                        post_data_1: time,
                        post_data_2: id,
                        post_data_3: swapmode
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("ajax通信に失敗しました");
                    },
                    success: function(response) {
                        //alert("返り値は" + response);
                        /*
                        if (!(response == "")) {
                            update_frame("設定されていません", response);
                        }
                        */
                        overwrite(time, response, 0);
                        overwrite(time, response, 1);

                        //keiroの関数
                        kousin();
                    }
                });
            });
        };

        //name_array = [["スポット名", "スポットID"]]
        //time(1,2,3)の時間帯のleftboxの内容を上書きする
        function overwrite(time, name_array, toggle) {
            //alert(time);
            if (toggle == 0) {
                if (time == 1) {
                    $div1 = document.getElementById("s_l_spots_line");
                } else if (time == 2) {
                    $div1 = document.getElementById("l_d_spots_line");
                } else if (time == 3) {
                    $div1 = document.getElementById("d_g_spots_line");
                }
            } else {
                if (time == 1) {
                    $div1 = document.getElementById("toggle_s_l_spots_line");
                } else if (time == 2) {
                    $div1 = document.getElementById("toggle_l_d_spots_line");
                } else if (time == 3) {
                    $div1 = document.getElementById("toggle_d_g_spots_line");
                }
            }
            $div1.innerHTML = "";
            for (var i = 0; i < name_array.length; i++) {
                //alert(name_array[i][1]);
                const newDiv = document.createElement("div");
                var j = i + 1;
                newDiv.innerHTML = j + ":" + name_array[i][0];
                const removeBtn = document.createElement("button");
                removeBtn.innerHTML = "×";
                removeBtn.title = "このスポットを削除します";
                removeBtn.className = 'btn2';
                const swapupBtn = document.createElement("button");
                swapupBtn.innerHTML = "↑";
                swapupBtn.title = "このスポットを一つ上に移動します";
                const swapdownBtn = document.createElement("button");
                swapdownBtn.innerHTML = "↓";
                swapdownBtn.title = "このスポットを一つ下に移動します";

                const spot_id = name_array[i][1];
                removeBtn.onclick = () => {
                    remove_spot(time, spot_id, toggle);
                }
                swapupBtn.onclick = () => {
                    swap_spots(time, spot_id, 'up', toggle);
                }
                swapdownBtn.onclick = () => {
                    swap_spots(time, spot_id, 'down', toggle);
                }

                //ボタン間の隙間
                const newa1 = document.createElement("a");
                const newa2 = document.createElement("a");
                newa1.innerHTML = " ";
                newa2.innerHTML = " ";

                newDiv.appendChild(document.createElement("br"));
                newDiv.appendChild(removeBtn);
                newDiv.appendChild(newa1);
                newDiv.appendChild(swapupBtn);
                newDiv.appendChild(newa2);
                newDiv.appendChild(swapdownBtn);

                $div1.appendChild(newDiv);
            }

        }
    </script>


    <h1>横浜みなとみらいフードツーリズム計画作成システム</h1>

    <ul id="dropmenu">
        <li><a href="home.php">ホーム</a></li>

        <li><a href="explain.php">使い方</a></li>

        <li><a>観光計画作成</a>
            <ul>
                <li><a href="set_station.php">開始・終了駅の設定</a></li>
                <li><a href="search.php">飲食店の検索・決定</a></li>
                <li><a id="keiro" name="keiro" href="keiro.php">観光スポット選択</a></li>
            </ul>
        </li>
        <li><a href="view.php">スポット一覧</a></li>

        <li><a>マイページ</a>
            <ul>
                <li><a id="see_myroute" name="see_myroute" href="see_myroute.php">作成した観光計画を見る</a></li>
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
        <img id="pin" width="20" height="30" src="./marker/start.png" alt="開始駅のアイコン" title="開始駅">
        <div id="start_name"><?php echo htmlspecialchars($start_station_name, ENT_QUOTES); ?></div><br>

        <b>昼食前に訪れる観光スポット：</b>
        <img id="pin" width="20" height="30" src="./marker/s_l_icon_explain.png" alt="昼食前に訪れる観光スポットのアイコン" title="昼食前に訪れる観光スポット">
        <div id="s_l_spots_line">
            <?php display_frame($s_l_spots_name, 1) ?>
        </div>
        <br>

        <b>昼食予定地:</b>
        <img id="pin" width="20" height="30" src="./marker/lanch.png" alt="昼食予定地のアイコン" title="昼食予定地">
        <div id="lanch_name"><?php echo htmlspecialchars($lanch_name, ENT_QUOTES); ?></div><br>

        <b>昼食後に訪れる観光スポット：</b>
        <img id="pin" width="20" height="30" src="./marker/l_d_icon_explain.png" alt="昼食後に訪れる観光スポットのアイコン" title="昼食後に訪れる観光スポット">
        <div id="l_d_spots_line">
            <?php display_frame($l_d_spots_name, 2) ?>
        </div>
        <br>

        <b>夕食予定地:</b>
        <img id="pin" width="20" height="30" src="./marker/dinner.png" alt="夕食予定地のアイコン" title="夕食予定地">
        <div id="dinner_name"><?php echo htmlspecialchars($dinner_name, ENT_QUOTES); ?></div><br>

        <b>夕食前に訪れる観光スポット：</b>
        <img id="pin" width="20" height="30" src="./marker/d_g_icon_explain.png" alt="夕食後に訪れる観光スポットのアイコン" title="夕食後に訪れる観光スポット">
        <div id="d_g_spots_line">
            <?php display_frame($d_g_spots_name, 3) ?>
        </div>
        <br>

        <b>終了駅：</b>
        <img id="pin" width="20" height="30" src="./marker/goal.png" alt="終了駅のアイコン" title="終了駅">
        <div id="goal_name"><?php echo htmlspecialchars($goal_station_name, ENT_QUOTES); ?></div>


        <h2>アンケート</h2>
        <?php
        if ($frameresult["survey"]) {
            print "<form action=\"\" method=\"POST\">";
            print "<input type=\"submit\" id=\"survey\" name=\"survey\" value=\"回答する\" onClick=\"window.open('https://forms.gle/aQtfhGavNZKtX52J7','_blank')\"><br>";
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
                        <li><a href="search.php">飲食店の検索・決定</a></li>
                        <li><a id="toggle_keiro" name="toggle_keiro" href="keiro.php">観光スポット選択</a></li>
                    </ul>
                </li>

                <li><a href="view.php">スポット一覧</a></li>

                <li><a>マイページ</a>
                    <ul>
                        <li><a id="toggle_see_myroute" name="toggle_see_myroute" href="see_myroute.php">作成した観光計画を見る</a></li>
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
                            <img id="pin" width="20" height="20" src="./pop_start.png" alt="開始駅のアイコン" title="開始駅">
                            <div id="start_name"><?php echo htmlspecialchars($start_station_name, ENT_QUOTES); ?></div>
                        </li><br>

                        <li>
                            <b>昼食前に訪れる観光スポット：</b>
                            <img id="pin" width="20" height="20" src="./marker/pop_icon1_f.png" alt="昼食前に訪れる観光スポットのアイコン" title="昼食前に訪れる観光スポット">
                            <div id="toggle_s_l_spots_line">
                                <?php display_frame($s_l_spots_name, 1) ?>
                            </div>
                        </li><br>

                        <li><b>昼食予定地:</b>
                            <img id="pin" width="20" height="20" src="./pop_lanch.png" alt="昼食予定地のアイコン" title="昼食予定地">
                            <div id="lanch_name"><?php echo htmlspecialchars($lanch_name, ENT_QUOTES); ?></div>
                        </li><br>

                        <li>
                            <b>昼食後に訪れる観光スポット：</b>
                            <img id="pin" width="20" height="20" src="./marker/pop_icon2_f.png" alt="昼食後に訪れる観光スポットのアイコン" title="昼食後に訪れる観光スポット">
                            <div id="toggle_l_d_spots_line">
                                <?php display_frame($l_d_spots_name, 2) ?>
                            </div>
                        </li><br>

                        <li><b>夕食予定地:</b>
                            <img id="pin" width="20" height="20" src="./pop_dinner.png" alt="夕食予定地のアイコン" title="夕食予定地">
                            <div id="dinner_name"><?php echo htmlspecialchars($dinner_name, ENT_QUOTES); ?></div>
                        </li><br>

                        <li>
                            <b>夕食前に訪れる観光スポット：</b>
                            <img id="pin" width="20" height="20" src="./marker/pop_icon3_f.png" alt="夕食後に訪れる観光スポットのアイコン" title="夕食後に訪れる観光スポット">
                            <div id="toggle_d_g_spots_line">
                                <?php display_frame($d_g_spots_name, 3) ?>
                            </div>
                        </li><br>

                        <li><b>終了駅：</b>
                            <img id="pin" width="20" height="20" src="./pop_goal.png" alt="終了駅のアイコン" title="終了駅">
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

</body>

</html>