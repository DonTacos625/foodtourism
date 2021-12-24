<?php

require "frame.php";

//stations_id設定
if (isset($_SESSION["start_station_id"])) {
    $start_station_id = $_SESSION["start_station_id"];
} else {
    $start_station_id = 0;
}
if (isset($_SESSION["goal_station_id"])) {
    $goal_station_id = $_SESSION["goal_station_id"];
} else {
    $goal_station_id = 0;
}
$station_id = [$start_station_id, $goal_station_id];

//food_shops_id設定
if (isset($_SESSION["lanch_id"])) {
    $lanch_shop_id = $_SESSION["lanch_id"];
} else {
    $lanch_shop_id = 0;
}
if (isset($_SESSION["dinner_id"])) {
    $dinner_shop_id = $_SESSION["dinner_id"];
} else {
    $dinner_shop_id = 0;
}
$food_shop_id = [$lanch_shop_id, $dinner_shop_id];

try {
    if ($start_station_id != 0) {
        $stmt1 = $pdo->prepare("SELECT * FROM minatomirai_station_data WHERE id = :id");
        $stmt1->bindParam(":id", $start_station_id);
        $stmt1->execute();
        $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $station_keikaku[] = [$result1["x"], $result1["y"], "start"];
    } else {
        $station_keikaku[] = [0, 0, "start"];
    }

    if ($lanch_shop_id != 0) {
        $stmt2 = $pdo->prepare("SELECT * FROM minatomirai_shop_data WHERE id = :id");
        $stmt2->bindParam(":id", $lanch_shop_id);
        $stmt2->execute();
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        $lanch_keikaku[] = [$result2["x"], $result2["y"], "lanch"];
    } else {
        $lanch_keikaku[] = [0, 0, "lanch"];
    }

    if ($dinner_shop_id != 0) {
        $stmt3 = $pdo->prepare("SELECT * FROM minatomirai_shop_data WHERE id = :id");
        $stmt3->bindParam(":id", $dinner_shop_id);
        $stmt3->execute();
        $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
        $dinner_keikaku[] = [$result3["x"], $result3["y"], "dinner"];
    } else {
        $dinner_keikaku[] = [0, 0, "dinner"];
    }

    if ($goal_station_id != 0) {
        $stmt4 = $pdo->prepare("SELECT * FROM minatomirai_station_data WHERE id = :id");
        $stmt4->bindParam(":id", $goal_station_id);
        $stmt4->execute();
        $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        $station_keikaku[] = [$result4["x"], $result4["y"], "goal"];
    } else {
        $station_keikaku[] = [0, 0, "goal"];
    }

    //提出された検索条件
    if (isset($_POST["yoyaku"])) {
        $yoyaku = $_POST["yoyaku"];
        $_SESSION["search_yoyaku"] = $_POST["yoyaku"];
    } else {
        $yoyaku = $_SESSION["search_yoyaku"];
    }
    if (isset($_POST["lanch_money"])) {
        $lanch_money = $_POST["lanch_money"];
        $_SESSION["search_lanch_money"] = $_POST["lanch_money"];
    } else {
        $lanch_money = $_SESSION["search_lanch_money"];
    }
    if (isset($_POST["dinner_money"])) {
        $dinner_money = $_POST["dinner_money"];
        $_SESSION["search_dinner_money"] = $_POST["dinner_money"];
    } else {
        $dinner_money = $_SESSION["search_dinner_money"];
    }
    if (isset($_POST["name_genre"])) {
        $name_genre = $_POST["name_genre"];
        $_SESSION["search_name_genre"] = $_POST["name_genre"];
    } else {
        $name_genre = $_SESSION["search_name_genre"];
    }
    if (isset($_POST["search_name"])) {
        $search_name = $_POST["search_name"];
        $_SESSION["search_name"] = $_POST["search_name"];
    } else {
        $search_name = $_SESSION["search_name"];
    }

    $keywordCondition = [];
    //posts = [["データベースのカラム名", "検索条件"]]
    $posts = [["yoyaku", $yoyaku], ["money", $lanch_money], ["money", $dinner_money]];

    $search_word = strtr($search_name, [
        '\\' => '\\\\',
        '%' => '\%',
        '_' => '\_',
    ]);

    //値が0じゃないデータを　keywordCondition　に格納
    foreach ($posts as $post) {
        if (!($post[1] == "0")) {
            $column = $post[0];
            if ($post[1] == "[昼]<3000") {
                $keywordCondition[] =  " $column LIKE '%[昼]%' AND $column NOT LIKE '%[昼]～￥999%' AND $column NOT LIKE '%[昼]￥1,000～￥1,999%' AND $column NOT LIKE '%[昼]￥2,000～￥2,999%' ";
            } else if ($post[1] == "[夜]<3000") {
                $keywordCondition[] =  " $column LIKE '%[夜]%' AND $column NOT LIKE '%[夜]～￥999%' AND $column NOT LIKE '%[夜]￥1,000～￥1,999%' AND $column NOT LIKE '%[夜]￥2,000～￥2,999%' ";
            } else {
                $keyword = $post[1];
                $keywordCondition[] =  " $column LIKE '%" . $keyword . "%' ";
            }
        }
    }
    //名前検索かジャンル検索か判定
    if ($name_genre == "0") {
        $column2 = "genre";
    } else {
        $column2 = "name";
    }
    $keywordCondition[] = " $column2 LIKE '%" . $search_word . "%' ";

    //var_dump($keywordCondition);
    // ここで、 
    // [ 'product_name LIKE "%hoge%"', 
    //   'product_name LIKE "%fuga%"', 
    //   'product_name LIKE "%piyo%"' ]
    // という配列ができあがっている。

    // これをANDでつなげて、文字列にする
    $keywordCondition = implode(' AND ', $keywordCondition);

    //sql文にする
    $sql = 'SELECT * FROM minatomirai_shop_data WHERE ' . $keywordCondition . ' ';

    //忘れ形見
    //$sql = " SELECT * FROM minatomirai_shop_data WHERE $column LIKE '%" . $search_name . "%' ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    echo "失敗:" . $e->getMessage() . "\n";
    exit();
}

$count = 0;
foreach ($stmt as $shop_id) {
    $food_shop_id[] = $shop_id["id"];
    $count += 1;
}

//検索条件の保存のため
function set_checked($session_name, $value)
{
    if ($value == $_SESSION[$session_name]) {
        //値がセッション変数と等しいとチェックされてる判定として返す
        print "checked=\"checked\"";
    } else {
        print "";
    }
}



//検索条件が初期の場合
$all_foodLayer_Flag = 0;
if ($yoyaku == "0" && $lanch_money == "0" && $dinner_money == "0" && $search_word == "") {
    $all_foodLayer_Flag = 1;
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
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>飲食店の検索・決定（地図上表示）</title>
    <style>
        h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        .move_box {
            position: relative;
            width: 76vw;
            float: left;
        }

        @media screen and (max-width:768px) {
            .container {
                display: flex;
                flex-direction: column;
                min-height: 250vh;
            }

            .move_box {
                width: 100%;
            }
        }
    </style>

    <link rel="stylesheet" href="https://js.arcgis.com/4.21/esri/themes/light/main.css" />
    <script src="https://js.arcgis.com/4.21/"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <script>
        var pointpic = "";
        require([
            "esri/Map",
            "esri/views/MapView",
            "esri/layers/WebTileLayer",
            "esri/layers/FeatureLayer",
            "esri/Graphic",
            "esri/layers/GraphicsLayer",
            "esri/rest/support/Query",
            "esri/rest/support/RouteParameters",
            "esri/rest/support/FeatureSet",
            "esri/symbols/PictureMarkerSymbol",
            "esri/symbols/CIMSymbol",
            "esri/widgets/LayerList"
        ], function(
            Map,
            MapView,
            WebTileLayer,
            FeatureLayer,
            Graphic,
            GraphicsLayer,
            Query,
            RouteParameters,
            FeatureSet,
            PictureMarkerSymbol,
            CIMSymbol,
            LayerList
        ) {

            // Point the URL to a valid routing service
            const routeUrl = "https://utility.arcgis.com/usrsvcs/servers/4550df58672c4bc6b17607b947177b56/rest/services/World/Route/NAServer/Route_World";
            //popup
            var lanch_Action = {
                title: "昼食に設定する",
                id: "lanch_id",
                image: "pop_lanch.png"
            };

            var dinner_Action = {
                title: "夕食に設定する",
                id: "dinner_id",
                image: "pop_dinner.png"
            };

            var detailAction = {
                title: "詳細",
                id: "detail",
                className: "esri-icon-documentation"
            };

            const food_template = {
                title: "{Name}",
                content: [{
                    type: "fields",
                    fieldInfos: [{
                        fieldName: "ID",
                        label: "ID",
                        visible: true
                    }, {
                        fieldName: "genre",
                        label: "ジャンル",
                        visible: true
                    }, {
                        fieldName: "time",
                        label: "営業時間",
                        visible: true
                    }, {
                        fieldName: "money",
                        label: "予算",
                        visible: true
                    }, {
                        fieldName: "yoyaku",
                        label: "予約可否",
                        visible: true
                    }, {
                        fieldName: "tel",
                        label: "電話番号",
                        visible: true
                    }, {
                        fieldName: "X",
                        label: "経度",
                        visible: true
                    }, {
                        fieldName: "Y",
                        label: "緯度",
                        visible: true
                    }]
                }],
                actions: [lanch_Action, dinner_Action, detailAction]
            };

            const station_template = {
                title: "{Name}",
                content: [{
                    type: "fields",
                    fieldInfos: [{
                        fieldName: "ID",
                        label: "ID",
                        visible: true
                    }, {
                        fieldName: "X",
                        label: "経度",
                        visible: true
                    }, {
                        fieldName: "Y",
                        label: "緯度",
                        visible: true
                    }]
                }]
            };

            const spots_template = {
                title: "{Name}",
                content: [{
                    type: "fields",
                    fieldInfos: [{
                        fieldName: "ID",
                        label: "ID",
                        visible: true
                    }, {
                        fieldName: "category",
                        label: "カテゴリー",
                        visible: true
                    }, {
                        fieldName: "homepage",
                        label: "ホームページ",
                        visible: true
                    }, {
                        fieldName: "X",
                        label: "経度",
                        visible: true
                    }, {
                        fieldName: "Y",
                        label: "緯度",
                        visible: true
                    }]
                }]
            };

            //スタートとゴールの駅を決める
            var station_id = <?php echo json_encode($station_id); ?>;
            var station_feature_sql = "";

            for (var i = 0; i < station_id.length; i++) {
                if (i != station_id.length - 1) {
                    station_feature_sql += "ID = "
                    station_feature_sql += station_id[i];
                    station_feature_sql += " OR "
                } else if (i == station_id.length - 1) {
                    station_feature_sql += "ID = "
                    station_feature_sql += station_id[i];
                }
            }

            //飲食店のIDから表示するスポットを決める
            var food_shop_id = <?php echo json_encode($food_shop_id); ?>;
            var food_feature_sql = "";

            for (var i = 0; i < food_shop_id.length; i++) {
                if (i != food_shop_id.length - 1) {
                    food_feature_sql += "ID = "
                    food_feature_sql += food_shop_id[i];
                    food_feature_sql += " OR "
                } else if (i == food_shop_id.length - 1) {
                    food_feature_sql += "ID = "
                    food_feature_sql += food_shop_id[i];
                }
            }
            //document.write(food_feature_sql);

            //spotLayer
            var foodLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_shop_new_UTF_8/FeatureServer",
                id: "foodLayer",
                popupTemplate: food_template,
                definitionExpression: food_feature_sql
            });

            var all_foodLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_shop_new_UTF_8/FeatureServer",
                id: "all_foodLayer",
                popupTemplate: food_template
            });

            var stationLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_station/FeatureServer",
                id: "stationLayer",
                popupTemplate: station_template,
                definitionExpression: station_feature_sql
            });

            var spotLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_kankou_UTF_8/FeatureServer",
                id: "spotLayer",
                popupTemplate: spots_template
            });

            //選択したスポットの表示レイヤー
            const station_pointLayer = new GraphicsLayer();
            const lanch_pointLayer = new GraphicsLayer();
            const dinner_pointLayer = new GraphicsLayer();

            //飲食店全体を表示するか検索結果を表示するか
            var foodLayer_Flag = <?php echo json_encode($all_foodLayer_Flag); ?>;
            if (foodLayer_Flag == 1) {
                $food = all_foodLayer;
            } else {
                $food = foodLayer;
            }

            const map = new Map({
                basemap: "streets",
                layers: [$food, stationLayer, station_pointLayer, lanch_pointLayer, dinner_pointLayer]
            });

            const view = new MapView({
                container: "viewDiv", // Reference to the scene div created in step 5
                map: map, // Reference to the map object created before the scene
                center: [139.635, 35.453],
                zoom: 14,
                popup: {
                    dockEnabled: true,
                    dockOptions: {
                        breakpoint: false
                    }
                }
            });

            //phpの経路情報をjavascript用に変換           
            var station_keikaku = <?php echo json_encode($station_keikaku); ?>;
            var lanch_keikaku = <?php echo json_encode($lanch_keikaku); ?>;
            var dinner_keikaku = <?php echo json_encode($dinner_keikaku); ?>;
            //開始駅と終了駅が同じの場合のフラグを設定
            var start_point = station_keikaku[0];
            var goal_point = station_keikaku.slice(-1)[0];
            var mode_change = 0;
            if (start_point[0] == goal_point[0] && start_point[1] == goal_point[1]) {
                mode_change = 1;
            }
            //最初に経路表示する処理
            function start_map(keikaku, Layer) {
                for (var j = 0; j < keikaku.length; j++) {
                    if (!(keikaku[j][0] == 0)) {
                        var point = {
                            type: "point",
                            x: keikaku[j][0],
                            y: keikaku[j][1]
                        };
                        if (keikaku[j].length > 2) {
                            if (keikaku[j][2] == "start") {
                                if (mode_change == 1) {
                                    pointpic = "./marker/start_and_goal.png";
                                } else {
                                    pointpic = "./marker/start.png";
                                }
                            } else if (keikaku[j][2] == "lanch") {
                                pointpic = "./marker/lanch.png";
                            } else if (keikaku[j][2] == "dinner") {
                                pointpic = "./marker/dinner.png";
                            } else if (keikaku[j][2] == "goal") {
                                if (mode_change == 1) {
                                    pointpic = "./marker/start_and_goal.png";
                                } else {
                                    pointpic = "./marker/goal.png";
                                }
                            } else {
                                pointpic = "./marker/ltblue.png";
                            }
                        }
                        var stopSymbol = new PictureMarkerSymbol({
                            url: pointpic,
                            width: "20px",
                            height: "31px"
                        });
                        var stop = new Graphic({
                            geometry: point,
                            symbol: stopSymbol
                        });
                        Layer.add(stop);
                    }
                }
            }
            start_map(station_keikaku, station_pointLayer);
            start_map(lanch_keikaku, lanch_pointLayer);
            start_map(dinner_keikaku, dinner_pointLayer);

            //ポップアップから追加
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "lanch_id") {
                    post_food(view.popup.selectedFeature.attributes.id, '1');
                    add_point("./marker/lanch.png", lanch_pointLayer);
                }
                if (event.action.id === "dinner_id") {
                    post_food(view.popup.selectedFeature.attributes.id, '2');
                    add_point("./marker/dinner.png", dinner_pointLayer);
                }
                if (event.action.id === "detail") {
                    shop_detail();
                }
            });

            function add_point(pic, Layer) {
                const point = {
                    type: "point",
                    x: view.popup.selectedFeature.attributes.X,
                    y: view.popup.selectedFeature.attributes.Y
                };
                var stopSymbol = new PictureMarkerSymbol({
                    url: pic,
                    width: "20px",
                    height: "31px"
                });
                var stop = new Graphic({
                    geometry: point,
                    symbol: stopSymbol
                });
                Layer.removeAll();
                Layer.add(stop);
            }

            //店の詳細ページに飛ぶときに送信するデータ
            function shop_detail() {
                var spot_id = view.popup.selectedFeature.attributes.id;
                var form = document.createElement('form');
                form.method = 'GET';
                form.action = './shopdetail2.php';
                var reqElm = document.createElement('input');
                reqElm.name = 'spot_id';
                reqElm.value = spot_id;
                form.appendChild(reqElm);
                document.body.appendChild(form);
                form.submit();
            };

        });

        function toframe(data, id) {
            //frameの関数
            update_frame(data, id);
        }

        //昼食・夕食を決定
        function post_food(food_shop_id, mode) {
            jQuery(function($) {
                $.ajax({
                    url: "ajax_food_shop.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        post_data_1: food_shop_id,
                        post_data_2: mode
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("ajax通信に失敗しました");
                    },
                    success: function(response) {
                        //alert("昼食IDは" + response);
                        //frameの関数
                        update_frame(response[0], response[1]);
                        if (mode == "1") {
                            alert("「" + response[0] + "」を昼食に設定しました");
                        } else {
                            alert("「" + response[0] + "」を夕食に設定しました");
                        }

                    }
                });
            });
        };

        function input_search_name(word) {
            const update = document.getElementById("search_name");
            update.value = word;
        };
    </script>

</head>

<body>
    <div class="container">
        <main>
            <h3 id="search_start">飲食店の検索・決定</h3>
            <a id="list_result" name="list_result" href="search.php">一覧で結果を表示</a><br>
            <div class="search_form">
                <form action="search_viewmode.php" method="post">
                    予約の可否：
                    <input type="radio" id="yoyaku" name="yoyaku" value="0" <?php set_checked("search_yoyaku", "0"); ?>>指定なし
                    <input type="radio" id="yoyaku" name="yoyaku" value="予約可" <?php set_checked("search_yoyaku", "予約可"); ?>>予約可
                    <input type="radio" id="yoyaku" name="yoyaku" value="予約不可" <?php set_checked("search_yoyaku", "予約不可"); ?>>予約不可<br>

                    昼食の予算：
                    <input type="radio" id="lanch_money" name="lanch_money" value="0" <?php set_checked("search_lanch_money", "0"); ?>>指定なし
                    <input type="radio" id="lanch_money" name="lanch_money" value="[昼]～￥999" <?php set_checked("search_lanch_money", "[昼]～￥999"); ?>>～￥999
                    <input type="radio" id="lanch_money" name="lanch_money" value="[昼]￥1,000～￥1,999" <?php set_checked("search_lanch_money", "[昼]￥1,000～￥1,999"); ?>>￥1,000～￥1,999
                    <input type="radio" id="lanch_money" name="lanch_money" value="[昼]￥2,000～￥2,999" <?php set_checked("search_lanch_money", "[昼]￥2,000～￥2,999"); ?>>￥2,000～￥2,999
                    <input type="radio" id="lanch_money" name="lanch_money" value="[昼]<3000" <?php set_checked("search_lanch_money", "[昼]<3000"); ?>>￥3,000～<br>

                    夕食の予算：
                    <input type="radio" id="dinner_money" name="dinner_money" value="0" <?php set_checked("search_dinner_money", "0"); ?>>指定なし
                    <input type="radio" id="dinner_money" name="dinner_money" value="[夜]～￥999" <?php set_checked("search_dinner_money", "[夜]～￥999"); ?>>～￥999
                    <input type="radio" id="dinner_money" name="dinner_money" value="[夜]￥1,000～￥1,999" <?php set_checked("search_dinner_money", "[夜]￥1,000～￥1,999"); ?>>￥1,000～￥1,999
                    <input type="radio" id="dinner_money" name="dinner_money" value="[夜]￥2,000～￥2,999" <?php set_checked("search_dinner_money", "[夜]￥2,000～￥2,999"); ?>>￥2,000～￥2,999
                    <input type="radio" id="dinner_money" name="dinner_money" value="[夜]<3000" <?php set_checked("search_dinner_money", "[夜]<3000"); ?>>￥3,000～<br>

                    検索の設定：
                    <input type="radio" id="name_genre" name="name_genre" value="0" <?php set_checked("search_name_genre", "0"); ?>>ジャンルで検索
                    <input type="radio" id="name_genre" name="name_genre" value="1" <?php set_checked("search_name_genre", "1"); ?>>店名で検索<br>

                    検索ワード：
                    <input type="text" value="<?php echo $search_word; ?>" id="search_name" name="search_name">
                    <select name="genre_example" size="1" onclick="input_search_name(value)">
                        <option value=""> ワードを入力するか以下から選択してください </option>
                        <option value="中華"> 中華 </option>
                        <option value="和食"> 和食 </option>
                        <option value="洋食"> 洋食 </option>
                        <option value="イタリアン"> イタリアン </option>
                        <option value="フレンチ"> フレンチ </option>
                        <option value="居酒屋"> 居酒屋 </option>
                        <option value="バイキング"> バイキング </option>
                        <option value="カフェ"> カフェ </option>
                    </select>
                    <br>
                    <input type="submit" name="submit" value="検索する"><br>
                </form>
            </div>
            <div class="move_box">
                <a class="prev_page" name="prev_station" href="set_station.php">開始・終了駅選択に戻る</a>
                <a class="next_page" name="next_keiro" href="keiro.php">観光スポット選択へ</a><br>
            </div>
            <?php
            if ($count == 0) {
                echo "検索条件に該当する飲食店はありませんでした";
            }
            ?>
            <div id="viewbox">
                <div id="viewDiv"></div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>