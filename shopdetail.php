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

try {

    if ($start_station_id != 0) {
        $stmt2 = $pdo->prepare("SELECT * FROM minatomirai_station_data WHERE id = :id");
        $stmt2->bindParam(":id", $start_station_id);
        $stmt2->execute();
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        $station_keikaku[] = [$result2["x"], $result2["y"], "start"];
    } else {
        $station_keikaku[] = [0, 0, "start"];
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

    $stmt1 = $pdo->prepare("SELECT * FROM minatomirai_shop_data where id = :id");
    $stmt1->bindParam(":id", $spot_id, PDO::PARAM_INT);
    $stmt1->execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    //デバッグ用
    echo $e->getMessage();
}
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
    <title>飲食店詳細</title>
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
            text-align: left;
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
                actions: [lanch_Action, dinner_Action]
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
            var result1 = <?php echo json_encode($result1) ?>;
            var food_feature_sql = "ID = " + result1["id"];

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

            //選択したスポットの表示レイヤー
            const station_pointLayer = new GraphicsLayer();
            const food_pointLayer = new GraphicsLayer();
            
            const center_pointLayer = new GraphicsLayer();

            const map = new Map({
                basemap: "streets",
                layers: [foodLayer, stationLayer, station_pointLayer, food_pointLayer, center_pointLayer]
            });

            const view = new MapView({
                container: "viewDiv", // Reference to the scene div created in step 5
                map: map, // Reference to the map object created before the scene
                center: [result1["x"], result1["y"]],
                zoom: 14,
                popup: {
                    dockEnabled: true,
                    dockOptions: {
                        breakpoint: false
                    }
                }
            });

            //中心地点にマーカーを
            function make_center_maker(pic,Layer,X,Y) {
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
            make_center_maker("./marker/red_pin.png",center_pointLayer,result1["x"], result1["y"])

            //phpの経路情報をjavascript用に変換           
            var station_keikaku = <?php echo json_encode($station_keikaku); ?>;
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

            //ポップアップから追加
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "lanch_id") {
                    add_spots("2");
                    add_point("./marker/lanch.png", food_pointLayer);
                }
                if (event.action.id === "dinner_id") {
                    add_spots("3");
                    add_point("./marker/dinner.png", food_pointLayer);
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

            //マップ上から昼食・夕食を設定する
            function add_spots(mode) {
                //minatomirai_shopとminatomirai_kankouのレイヤーのIDは小文字のid
                var spot_id = view.popup.selectedFeature.attributes.id;
                jQuery(function($) {
                    $.ajax({
                        url: "./ajax_view_addspots.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: spot_id,
                            post_data_2: mode
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("ajax通信に失敗しました");
                        },
                        success: function(response) {
                            //alert(response[0]);
                            //esriの関数の外へ
                            toframe(response[0], response[1]);
                            if (mode == "2") {
                                alert("「" + response[0] + "」を昼食に設定しました");
                            } else if (mode == "3") {
                                alert("「" + response[0] + "」を夕食に設定しました");
                            }
                        }
                    });
                });
            };

        });

        //表示を更新する
        function toframe(data, id) {
            //frameの関数
            update_frame(data, id);
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
                                <td><?php echo $result1["name"]; ?></td>
                            </tr>

                            <tr>
                                <th>ジャンル</th>
                                <td><?php echo $result1["genre"]; ?></td>
                            </tr>

                            <tr>
                                <th>営業時間</th>
                                <td><?php echo nl2br($result1["time"]); ?></td>
                            </tr>

                            <tr>
                                <th>予算</th>
                                <td><?php echo nl2br($result1["money"]); ?></td>
                            </tr>

                            <tr>
                                <th>予約</th>
                                <td><?php echo nl2br($result1["yoyaku"]); ?></td>
                            </tr>

                            <tr>
                                <th>お問い合わせ</th>
                                <td><?php echo $result1["tel"]; ?></td>
                            </tr>

                            <tr>
                                <th>ホームページURL</th>
                                <td>
                                    <?php
                                    if (!empty($result1["homepage"])) {
                                        print "<a href = " . $result1["homepage"] . " target=_blank>ホームページにアクセスする</a>";
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <li><a href="search.php">飲食店検索に戻る</a></li>
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