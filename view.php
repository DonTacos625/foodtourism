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
        $start_station_keikaku[] = [$result1["x"], $result1["y"], "start"];
    } else {
        $start_station_keikaku[] = [0, 0, "start"];
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
        $goal_station_keikaku[] = [$result4["x"], $result4["y"], "goal"];
    } else {
        $goal_station_keikaku[] = [0, 0, "goal"];
    }
} catch (PDOException $e) {
    echo "失敗:" . $e->getMessage() . "\n";
    exit();
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
    <title>スポット一覧</title>
    <style>
        h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        .icon_explain {
            position: relative;
            float: left;
            width: 100%;
            height: 15%;
        }

        .pin_list1 {
            width: 315px;
            height: 75px;
        }

        .pin_list2 {
            width: 390px;
            height: 75px;
        }

        .pin_list3 {
            width: 192px;
            height: 75px;
        }

        @media screen and (max-width:768px) {

            .icon_explain {
                width: 95vw;
            }

            .pin_list1 {
                width: 90%;
                height: 90%;
            }

            .pin_list2 {
                width: 95%;
                height: 95%;
            }

            .pin_list3 {
                width: 60%;
                height: 60%;
            }

            #viewbox {
                position: relative;
                float: left;
                width: 95vw;
                height: 85vh;
                margin: 0px;
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

            var start_Action = {
                title: "開始駅に設定する",
                id: "start_station_id",
                image: "pop_start.png"
            };

            var goal_Action = {
                title: "終了駅に設定する",
                id: "goal_station_id",
                image: "pop_goal.png"
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
                }],
                actions: [start_Action, goal_Action]
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

            // スポット名を表示するラベルを定義
            var labelClass = {
                symbol: {
                    type: "text",
                    color: "white",
                    haloColor: "black",
                    haloSize: 1
                },
                font: {
                    size: 15,
                    widget: "bold"
                },
                labelPlacement: "above-center",
                labelExpressionInfo: {
                    expression: "$feature.name"
                }
            };

            //spotLayer
            var foodLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_shop_new_UTF_8/FeatureServer",
                id: "foodLayer",
                popupTemplate: food_template,
                labelingInfo: [labelClass]
            });

            var stationLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_station/FeatureServer",
                id: "stationLayer",
                popupTemplate: station_template,
                labelingInfo: [labelClass]
            });

            var spotLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_kankou_UTF_8/FeatureServer",
                id: "spotLayer",
                popupTemplate: spots_template,
                labelingInfo: [labelClass]
            });

            //選択したスポットの表示レイヤー
            const start_station_pointLayer = new GraphicsLayer();
            start_station_pointLayer.listMode = "hide";
            const goal_station_pointLayer = new GraphicsLayer();
            goal_station_pointLayer.listMode = "hide";
            const lanch_pointLayer = new GraphicsLayer();
            lanch_pointLayer.listMode = "hide";
            const dinner_pointLayer = new GraphicsLayer();
            dinner_pointLayer.listMode = "hide";

            const map = new Map({
                basemap: "streets",
                //layers: [stationLayer, foodLayer, spotLayer],
                layers: [foodLayer, stationLayer, spotLayer, start_station_pointLayer, goal_station_pointLayer, lanch_pointLayer, dinner_pointLayer]
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
            var start_station_keikaku = <?php echo json_encode($start_station_keikaku); ?>;
            var goal_station_keikaku = <?php echo json_encode($goal_station_keikaku); ?>;
            var lanch_keikaku = <?php echo json_encode($lanch_keikaku); ?>;
            var dinner_keikaku = <?php echo json_encode($dinner_keikaku); ?>;
            //開始駅と終了駅が同じの場合のフラグを設定
            var start_point = start_station_keikaku[0];
            var goal_point = goal_station_keikaku[0];
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
            start_map(start_station_keikaku, start_station_pointLayer);
            start_map(goal_station_keikaku, goal_station_pointLayer);
            start_map(lanch_keikaku, lanch_pointLayer);
            start_map(dinner_keikaku, dinner_pointLayer);


            //ポップアップから追加
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "start_station_id") {
                    add_spots("1");
                    add_point("./marker/start.png", start_station_pointLayer);
                }
                if (event.action.id === "lanch_id") {
                    add_spots("2");
                    add_point("./marker/lanch.png", lanch_pointLayer);
                }
                if (event.action.id === "dinner_id") {
                    add_spots("3");
                    add_point("./marker/dinner.png", dinner_pointLayer);
                }
                if (event.action.id === "goal_station_id") {
                    add_spots("4");
                    add_point("./marker/goal.png", goal_station_pointLayer);
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

            function add_spots(num) {
                //minatomirai_shopとminatomirai_kankouのレイヤーのIDは小文字のid
                var spot_id = view.popup.selectedFeature.attributes.id;
                jQuery(function($) {
                    $.ajax({
                        url: "./ajax_view_addspots.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: spot_id,
                            post_data_2: num
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("ajax通信に失敗しました");
                        },
                        success: function(response) {
                            if (num == 1) {
                                alert("「" + response[0] + "」を開始駅に設定しました");
                            } else if (num == 4) {
                                alert("「" + response[0] + "」を終了駅に設定しました");
                            } else if (num == 2) {
                                alert("「" + response[0] + "」を昼食に設定しました");
                            } else if (num == 3) {
                                alert("「" + response[0] + "」を夕食に設定しました");
                            }
                            //esriの関数の外へ
                            toframe(response[0], response[1]);
                        }
                    });
                });
            };


            var layerlist = new LayerList({
                view: view,
                listItemCreatedFunction: function(event) {
                    if (event.item.title != "") {
                        let item = event.item;
                        if (item.title === "Minatomirai shop new UTF 8") {
                            item.title = "飲食店";
                        } else if (item.title === "Minatomirai kankou UTF 8") {
                            item.title = "観光スポット";
                        } else if (item.title === "Minatomirai station") {
                            item.title = "駅";
                        }
                    }
                }
            });
            layerlist.statusIndicatorsVisible = false;

            var windowWidth = $(window).width();
            var windowSm = 768;
            if (windowWidth <= windowSm) {
                view.ui.add(layerlist, "top-right");
            } else {
                view.ui.add(layerlist, "bottom-right");
            };
            view.ui.add(layerlist, "top-right");
        });


        function toframe(data, id) {
            //frameの関数
            update_frame(data, id);
        }
    </script>

</head>

<body>
    <div class="container">
        <main>
            <h3>スポット一覧</h3>
            <div class="icon_explain">
                <img class="pin_list1" src="./marker/icon_explain_s_f.png" alt="昼食予定地のアイコン" title="アイコン説明1">
                <img class="pin_list2" src="./marker/icon_explain_spots.png" alt="昼食予定地のアイコン" title="アイコン説明2">
                <img class="pin_list3" src="./marker/icon_explain_view.png" alt="昼食予定地のアイコン" title="アイコン説明3">
            </div>
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