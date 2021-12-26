<?php

require "frame.php";

$spot_id = $_GET["spot_id"];

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
$food_shop_id = [$lanch_shop_id, $dinner_shop_id];

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

    $stmt = $pdo->prepare("SELECT * FROM minatomirai_kankou_data where id = :id");
    $stmt->bindParam(":id", $spot_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    //デバッグ用
    echo $e->getMessage();
}

//keikakuの配列作成
$keikaku[] = $start_station_info;

$keikaku[] = $lanch_info;

$keikaku[] = $dinner_info;

$keikaku[] = $goal_station_info;

?>

<html>

<head>
    <meta charset="utf-8" />
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
    <title>観光スポット詳細</title>
    <style>
        #detailbox {
            position: relative;
            float: left;
            margin-left: 5px;
        }

        #detailbox h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        #detailbox #imgbox #viewbox {
            position: relative;
            float: left;
            width: 75vw;
            height: 40vw;
            margin-bottom: 15px;
            justify-content: center;
            align-items: center;
        }

        #detailbox #imgbox #viewbox #viewDiv {
            position: relative;
            padding: 0;
            margin: 0;
            height: 100%;
            width: 100%;
        }

        #detailbox #infobox {
            float: left;
            width: 75vw;
            margin-left: 5px;
        }

        .clearfix:after {
            display: block;
            clear: both;
            height: 0px;
            visibility: hidden;
            content: ".";
        }

        #detailbox #infobox table {
            width: 100%;
            border: solid 3px #FFFFFF;
        }

        #detailbox #infobox table th {
            white-space: nowrap;
            background: #EEEEEE;
        }

        #detailbox #infobox table td {
            background: #EEEEEE;
            padding: 3px;
        }

        #detailbox #infobox table td ul {
            margin: 0px;
        }

        #detailbox #infobox table td ul li {
            display: inline-block;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            h3 {
                margin: 0px;
                font-size: 18px;
            }
        }

        @media screen and (max-width:768px) {
            h3 {
                margin: 0px;
                font-size: 17px;
            }

            #detailbox {
                width: auto;
                margin: 0px;
                float: none;
            }

            #detailbox #imgbox #viewbox {
                position: relative;
                float: left;
                width: 95%;
                height: 50vh;
                margin-bottom: 15px;
                justify-content: center;
                align-items: center;
            }

            #detailbox #infobox {
                width: 95%;
                float: none;
            }

            #detailbox #infobox table {
                font-size: 13px;
            }

            #detailbox #viewbox {
                width: 100%;
                height: 80vw;
                float: none;
            }

        }
    </style>

    <link rel="stylesheet" href="https://js.arcgis.com/4.21/esri/themes/light/main.css" />
    <script src="https://js.arcgis.com/4.21/"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <script>
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
            "esri/symbols/PictureMarkerSymbol"
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
            PictureMarkerSymbol
        ) {

            // Point the URL to a valid routing service
            const routeUrl = "https://utility.arcgis.com/usrsvcs/servers/4550df58672c4bc6b17607b947177b56/rest/services/World/Route/NAServer/Route_World";
            //popup
            var s_l_Action = {
                title: "昼食前に訪れる",
                id: "s_l",
                image: "pop_icon1.png"
            };

            var l_d_Action = {
                title: "昼食後に訪れる",
                id: "l_d",
                image: "pop_icon2.png"
            };

            var d_g_Action = {
                title: "夕食後に訪れる",
                id: "d_g",
                image: "pop_icon3.png"
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
                }]
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
                }],
                actions: [s_l_Action, l_d_Action, d_g_Action]
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

            //飲食店のIDから表示するスポットを決める
            var result = <?php echo json_encode($result) ?>;
            var spots_feature_sql = "ID = " + result["id"];

            //spotLayer
            var foodLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_shop_new_UTF_8/FeatureServer",
                id: "foodLayer",
                popupTemplate: food_template,
                definitionExpression: food_feature_sql
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
                popupTemplate: spots_template,
                definitionExpression: spots_feature_sql
            });

            //選択したスポットの表示レイヤー
            const routeLayer = new GraphicsLayer();

            //選択したスポットの表示レイヤー
            const s_l_pointLayer = new GraphicsLayer();
            const l_d_pointLayer = new GraphicsLayer();
            const d_g_pointLayer = new GraphicsLayer();

            const center_pointLayer = new GraphicsLayer();

            const map = new Map({
                basemap: "streets",
                layers: [foodLayer, stationLayer, spotLayer, routeLayer, s_l_pointLayer, l_d_pointLayer, d_g_pointLayer, center_pointLayer]
            });

            const view = new MapView({
                container: "viewDiv", // Reference to the scene div created in step 5
                map: map, // Reference to the map object created before the scene
                center: [result["x"], result["y"]],
                zoom: 14,
                popup: {
                    dockEnabled: true,
                    dockOptions: {
                        breakpoint: false
                    }
                }
            });

            //中心地点にマーカーを
            function make_center_maker(pic, Layer, X, Y) {
                const point = {
                    type: "point",
                    x: X,
                    y: Y
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
                Layer.add(stop);
            }
            make_center_maker("./marker/red_pin.png", center_pointLayer, result["x"], result["y"])

            //phpの経路情報をjavascript用に変換           
            var keikaku = <?php echo json_encode($keikaku); ?>;

            function display_route(plan) {
                //最初に経路表示する処理
                //開始駅と終了駅が同じの場合のフラグを設定
                var start_point = plan[0];
                var goal_point = plan.slice(-1)[0];
                var mode_change = 0;
                if (start_point[0] == goal_point[0] && start_point[1] == goal_point[1]) {
                    mode_change = 1;
                }
                for (var j = 0; j < plan.length; j++) {
                    if (!(plan[j][0] == 0)) {
                        var point = {
                            type: "point",
                            x: plan[j][0],
                            y: plan[j][1]
                        };
                        if (plan[j].length > 2) {
                            if (plan[j][2] == "start") {
                                if (mode_change == 1) {
                                    pointpic = "./marker/start_and_goal.png";
                                } else {
                                    pointpic = "./marker/start.png";
                                }
                            } else if (plan[j][2] == "lanch") {
                                pointpic = "./marker/lanch.png";
                            } else if (plan[j][2] == "dinner") {
                                pointpic = "./marker/dinner.png";
                            } else if (plan[j][2] == "goal") {
                                if (mode_change == 1) {
                                    pointpic = "./marker/start_and_goal.png";
                                } else {
                                    pointpic = "./marker/goal.png";
                                }
                            } else if (plan[j][2] == 11) {
                                pointpic = "./marker/s_l_spot1.png";
                            } else if (plan[j][2] == 12) {
                                pointpic = "./marker/s_l_spot2.png";
                            } else if (plan[j][2] == 13) {
                                pointpic = "./marker/s_l_spot3.png";
                            } else if (plan[j][2] == 21) {
                                pointpic = "./marker/l_d_spot1.png";
                            } else if (plan[j][2] == 22) {
                                pointpic = "./marker/l_d_spot2.png";
                            } else if (plan[j][2] == 23) {
                                pointpic = "./marker/l_d_spot3.png";
                            } else if (plan[j][2] == 31) {
                                pointpic = "./marker/d_g_spot1.png";
                            } else if (plan[j][2] == 32) {
                                pointpic = "./marker/d_g_spot2.png";
                            } else if (plan[j][2] == 33) {
                                pointpic = "./marker/d_g_spot3.png";
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
                        routeLayer.add(stop);
                        /*
                        routeParams.stops.features.push(stop);
                        if (routeParams.stops.features.length >= 2) {
                            route.solve(routeUrl, routeParams).then(showRoute);
                        }
                        */
                    }
                }
            }
            display_route(keikaku);

            //押したボタンによって
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "s_l") {
                    hozon("1", s_l_pointLayer);
                }
                if (event.action.id === "l_d") {
                    hozon("2", l_d_pointLayer);
                }
                if (event.action.id === "d_g") {
                    hozon("3", d_g_pointLayer);
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

            function hozon(time, Layer) {
                //スポット取得
                const spot_id = view.popup.selectedFeature.attributes.id;
                jQuery(function($) {
                    $.ajax({
                        url: "./ajax_addspot.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: spot_id,
                            post_data_2: time
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("ajax通信に失敗しました");
                        },
                        success: function(response) {
                            //alert(response[0]);
                            //esriの関数の外へ
                            toframe(time, response[1]);

                            if (response[0] == "") {
                                alert("同じスポットは登録できません");
                            } else if (response[0] == "3") {
                                alert("各時間帯に登録できるスポットは3つまでです");
                            } else {
                                alert("「" + response[0] + "」を訪問する観光スポットに追加しました");
                                //選択したスポットの座標に印を
                                const point = {
                                    type: "point",
                                    x: view.popup.selectedFeature.attributes.X,
                                    y: view.popup.selectedFeature.attributes.Y
                                };
                                const stop = new Graphic({
                                    geometry: point,
                                    symbol: CheckSymbol
                                });
                                //Layer.removeAll();
                                Layer.add(stop);
                            }
                        }
                    });
                });
            };

        });

        //frame関数内のoverwriteを実行する
        function toframe(time, response) {
            //frameの関数
            overwrite(time, response, 0);
            overwrite(time, response, 1);
        }
    </script>

</head>

<body>
    <div class="container">
        <main>
            <div id="detailbox">
                <h3>観光スポットの詳細情報</h3>

                <div id="box" class="clearfix">

                    <div id="imgbox">
                        <div id="viewbox">
                            <div id="viewDiv"></div>
                        </div>
                    </div>

                    <div id="infobox">
                        <table>
                            <tr>
                                <th>名称</th>
                                <td><?php echo $result["name"]; ?></td>
                            </tr>

                            <tr>
                                <th>カテゴリー</th>
                                <td><?php echo $result["category"]; ?></td>
                            </tr>

                            <tr>
                                <th>ホームページURL</th>
                                <td>
                                    <?php
                                    if (!empty($result["homepage"])) {
                                        print "<a href = " . $result["homepage"] . " target=_blank>ホームページにアクセスする</a>";
                                    }
                                    ?>
                                </td>
                            </tr>

                        </table>
                        <li><a href="keiro.php">観光スポット選択に戻る</a></li>
                    </div>
                </div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>