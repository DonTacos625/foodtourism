<?php

require "frame.php";

//stations_id設定
if (!isset($_SESSION["start_station_id"])) {
    $start_station_id = 0;
} else {
    $start_station_id = $_SESSION["start_station_id"];
}
if (!isset($_SESSION["goal_station_id"])) {
    $goal_station_id = 0;
} else {
    $goal_station_id = $_SESSION["goal_station_id"];
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

//DB接続
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

    $stmt1 = $pdo->prepare("SELECT * FROM test.minatomirai_station_data WHERE id = :id");
    $stmt1->bindParam(":id", $_SESSION["start_station_id"]);
    $stmt1->execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT * FROM test.minatomirai_shop_data WHERE id = :id");
    $stmt2->bindParam(":id", $_SESSION["lanch_id"]);
    $stmt2->execute();
    $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

    $stmt3 = $pdo->prepare("SELECT * FROM test.minatomirai_shop_data WHERE id = :id");
    $stmt3->bindParam(":id", $_SESSION["dinner_id"]);
    $stmt3->execute();
    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $stmt4 = $pdo->prepare("SELECT * FROM test.minatomirai_station_data WHERE id = :id");
    $stmt4->bindParam(":id", $_SESSION["goal_station_id"]);
    $stmt4->execute();
    $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);

    $stmt5 = $pdo->prepare("SELECT * FROM test.minatomirai_kankou_data WHERE id = :id");
    $stmt5->bindParam(":id", $_SESSION["s_l_kankou_spots_id"]);
    $stmt5->execute();
    $result5 = $stmt5->fetch(PDO::FETCH_ASSOC);

    $stmt6 = $pdo->prepare("SELECT * FROM test.minatomirai_kankou_data WHERE id = :id");
    $stmt6->bindParam(":id", $_SESSION["l_d_kankou_spots_id"]);
    $stmt6->execute();
    $result6 = $stmt6->fetch(PDO::FETCH_ASSOC);

    $stmt7 = $pdo->prepare("SELECT * FROM test.minatomirai_kankou_data WHERE id = :id");
    $stmt7->bindParam(":id", $_SESSION["d_g_kankou_spots_id"]);
    $stmt7->execute();
    $result7 = $stmt7->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

if (!isset($_SESSION["s_l_kankou_spots_id"])) {
    $s_l_kankou_spots_id = [0, 0];
} else {
    $s_l_kankou_spots_id = [$result5["x"], $result5["y"], "11"];
}
if (!isset($_SESSION["l_d_kankou_spots_id"])) {
    $l_d_kankou_spots_id = [0, 0];
} else {
    $l_d_kankou_spots_id = [$result6["x"], $result6["y"], "12"];
}
if (!isset($_SESSION["d_g_kankou_spots_id"])) {
    $d_g_kankou_spots_id = [0, 0];
} else {
    $d_g_kankou_spots_id = [$result7["x"], $result7["y"], "13"];
}

$keikaku = array(
    array($result1["X"], $result1["Y"], "1"), $s_l_kankou_spots_id,
    array($result2["X"], $result2["Y"], "2"), $l_d_kankou_spots_id,
    array($result3["X"], $result3["Y"], "3"), $d_g_kankou_spots_id,
    array($result4["X"], $result4["Y"], "4")
);

?>

<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>観光スポット選択 | 経路作成</title>
    <style>
        #viewbox {
            position: absolute;
            float: left;
            width: 80vw;
            height: 80vh;
            margin-left: 5px;
        }

        #viewbox h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        #viewbox #viewDiv {
            position: relative;
            padding: 0;
            margin: 0;
            height: 85%;
            width: 80%;
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
            "esri/rest/route",
            "esri/rest/support/RouteParameters",
            "esri/rest/support/FeatureSet",
            "esri/symbols/PictureMarkerSymbol",
            "esri/symbols/CIMSymbol"
        ], function(
            Map,
            MapView,
            WebTileLayer,
            FeatureLayer,
            Graphic,
            GraphicsLayer,
            Query,
            route,
            RouteParameters,
            FeatureSet,
            PictureMarkerSymbol,
            CIMSymbol
        ) {

            // Point the URL to a valid routing service
            const routeUrl = "https://utility.arcgis.com/usrsvcs/servers/4550df58672c4bc6b17607b947177b56/rest/services/World/Route/NAServer/Route_World";
            const MY_API_KEY = "AAPKfe5fdd5be2744698a188fcc0c7b7b1d742vtC5TsStg94fpwkldrfNo3SJn2jl_VuCOEEdcBiwR7dKOKxejIP_3EDj9IPSPg";
            //popup
            var routeAction = {
                title: "ルートに追加する",
                id: "route",
                className: "esri-icon-navigation"
            };

            var s_l_Action = {
                title: "昼食前に訪れる",
                id: "s_l",
                className: "esri-icon-map-pin"
            };

            var l_d_Action = {
                title: "昼食後に訪れる",
                id: "l_d",
                className: "esri-icon-map-pin"
            };

            var d_g_Action = {
                title: "夕食後に訪れる",
                id: "d_g",
                className: "esri-icon-map-pin"
            };

            var food_template = {
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
                        fieldName: "x",
                        label: "経度",
                        visible: true
                    }, {
                        fieldName: "y",
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

            //spotLayer
            var foodLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_shop_new_UTF_8/FeatureServer",
                id: "foodLayer",
                popupTemplate: food_template,
                definitionExpression: food_feature_sql
            });

            var stationLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_station_data/FeatureServer",
                id: "stationLayer",
                popupTemplate: station_template,
                definitionExpression: station_feature_sql
            });

            var spotLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_kankou_UTF_8/FeatureServer",
                id: "spotLayer"
            });

            //ルート表示のレイヤー
            const routeLayer = new GraphicsLayer();
            //周辺スポットのレイヤー
            const resultsLayer = new GraphicsLayer();

            const s_l_pointLayer = new GraphicsLayer();
            const l_d_pointLayer = new GraphicsLayer();
            const d_g_pointLayer = new GraphicsLayer();

            // Setup the route parameters
            const routeParams = new RouteParameters({
                // An authorization string used to access the routing service
                apiKey: MY_API_KEY,
                attributeParameterValues: [{
                    parameterName: "Restriction Usage",
                    attributeName: "Walking",
                    value: "PROHIBITED"
                }, {
                    parameterName: "Restriction Usage",
                    attributeName: "Preferred for Pedestrians",
                    value: "PREFER_LOW"
                }],
                restrictionAttributes: ["Walking", "Preferred for Pedestrians"],

                stops: new FeatureSet(),
                outSpatialReference: {
                    // autocasts as new SpatialReference()
                    wkid: 3857
                }
            });

            // Define the symbology used to display the stops
            const stopSymbol1 = {
                type: "simple-marker", // autocasts as new SimpleMarkerSymbol()
                style: "cross",
                size: 15,
                outline: {
                    // autocasts as new SimpleLineSymbol()
                    width: 4
                }
            };

            // Define the symbology used to display the route
            const routeSymbol = {
                type: "simple-line", // autocasts as SimpleLineSymbol()
                color: [0, 0, 255, 0.5],
                width: 3
            };

            const routeArrowSymbol = new CIMSymbol({
                data: {
                    type: "CIMSymbolReference",
                    symbol: {
                        type: "CIMLineSymbol",
                        symbolLayers: [{
                                // black 1px line symbol
                                type: "CIMSolidStroke",
                                enable: true,
                                width: 1,
                                color: [
                                    0,
                                    0,
                                    0,
                                    255
                                ]
                            },
                            {
                                // arrow symbol
                                type: "CIMVectorMarker",
                                enable: true,
                                size: 5,
                                markerPlacement: {
                                    type: "CIMMarkerPlacementAlongLineSameSize", // places same size markers along the line
                                    endings: "WithMarkers",
                                    placementTemplate: [69.5], // determines space between each arrow
                                    angleToLine: true // symbol will maintain its angle to the line when map is rotated
                                },
                                frame: {
                                    xmin: -5,
                                    ymin: -5,
                                    xmax: 5,
                                    ymax: 5
                                },
                                markerGraphics: [{
                                    type: "CIMMarkerGraphic",
                                    geometry: {
                                        rings: [
                                            [
                                                [
                                                    -8,
                                                    -5.47
                                                ],
                                                [
                                                    -8,
                                                    5.6
                                                ],
                                                [
                                                    1.96,
                                                    -0.03
                                                ],
                                                [
                                                    -8,
                                                    -5.47
                                                ]
                                            ]
                                        ]
                                    },
                                    symbol: {
                                        // black fill for the arrow symbol
                                        type: "CIMPolygonSymbol",
                                        symbolLayers: [{
                                            type: "CIMSolidFill",
                                            enable: true,
                                            color: [
                                                0,
                                                0,
                                                0,
                                                255
                                            ]
                                        }]
                                    }
                                }]
                            }
                        ]
                    }
                }
            });

            const map = new Map({
                basemap: "streets",
                layers: [resultsLayer, stationLayer, foodLayer, routeLayer, s_l_pointLayer, l_d_pointLayer, d_g_pointLayer]
            });

            const view = new MapView({
                container: "viewDiv", // Reference to the scene div created in step 5
                map: map, // Reference to the map object created before the scene
                center: [139.635, 35.453],
                zoom: 14
            });

            //最初から経路表示のサンプル            
            var keikaku = <?php echo json_encode($keikaku); ?>;

            for (var j = 0; j < keikaku.length; j++) {
                if (!(keikaku[j][0] == 0)) {
                    var point = {
                        type: "point",
                        x: keikaku[j][0],
                        y: keikaku[j][1]
                    };
                    if (keikaku[j].length > 2) {
                        if (keikaku[j][2] == 1) {
                            pointpic = "./marker/start.png";
                        } else if (keikaku[j][2] == 2) {
                            pointpic = "./marker/lanch.png";
                        } else if (keikaku[j][2] == 3) {
                            pointpic = "./marker/dinner.png";
                        } else if (keikaku[j][2] == 4) {
                            pointpic = "./marker/goal.png";
                        } else if (keikaku[j][2] == 11) {
                            pointpic = "./marker/spot1.png";
                        } else if (keikaku[j][2] == 12) {
                            pointpic = "./marker/spot2.png";
                        } else if (keikaku[j][2] == 13) {
                            pointpic = "./marker/spot3.png";
                        } else {
                            pointpic = "./marker/ltblue.png";
                        }
                    }
                    var stopSymbol = new PictureMarkerSymbol({
                        url: pointpic,
                        width: "25px",
                        height: "39px"
                    });
                    var stop = new Graphic({
                        geometry: point,
                        symbol: stopSymbol
                    });
                    routeLayer.add(stop);
                    routeParams.stops.features.push(stop);
                    if (routeParams.stops.features.length >= 2) {
                        route.solve(routeUrl, routeParams).then(showRoute);
                    }
                }
            }

            //実験場
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "s_l") {
                    hozon("1", s_l_pointLayer);
                }
            });

            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "l_d") {
                    hozon("2", l_d_pointLayer);
                }
            });

            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "d_g") {
                    hozon("3", d_g_pointLayer);
                }
            });

            function hozon(num, Layer) {
                //スポット取得
                const spot_id = view.popup.selectedFeature.attributes.id;
                const time = num;
                jQuery(function($) {
                    $.ajax({
                        url: "./ajax_spot.php",
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
                            toframe(response[0], response[1]);
                            //選択したスポットの座標に印を
                            const point = {
                                type: "point",
                                x: view.popup.selectedFeature.attributes.X,
                                y: view.popup.selectedFeature.attributes.Y
                            };
                            const stop = new Graphic({
                                geometry: point,
                                symbol: stopSymbol1
                            });
                            Layer.removeAll();
                            Layer.add(stop);
                        }
                    });
                });
            };

            //ポップアップからレイヤーに追加
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "route") {
                    popadd();
                }
            });

            function popadd(event) {
                const point = {
                    type: "point",
                    x: view.popup.selectedFeature.attributes.X,
                    y: view.popup.selectedFeature.attributes.Y
                };
                const stop = new Graphic({
                    geometry: point,
                    symbol: stopSymbol
                });
                routeLayer.add(stop);
                routeParams.stops.features.push(stop);
                if (routeParams.stops.features.length >= 2) {
                    route.solve(routeUrl, routeParams).then(showRoute);
                }
            }

            //ルート形状沿いの観光地検索
            queryAroundSpot = (geom) => {
                //ルート形状から 100m のバッファ内の観光スポットを検索するための query式を作成
                let query = spotLayer.createQuery();
                query.geometry = geom;
                query.outFields = ["*"]
                query.distance = 200;
                query.units = "meters";
                //観光スポットに対する検索の実行
                spotLayer.queryFeatures(query).then(function(featureSet) {
                    var result_fs = featureSet.features;

                    //検索結果が0件だったら、何もしない
                    if (result_fs.length === 0) {
                        return;
                    }

                    //前回の検索結果を、グラフィックスレイヤーから削除
                    resultsLayer.removeAll();

                    //検索結果に対する設定
                    var features = result_fs.map(function(graphic) {
                        //シンボル設定
                        graphic.symbol = {
                            type: "simple-marker",
                            style: "diamond",
                            size: 8.5,
                            color: "darkorange"
                        };
                        //ポップアップ設定
                        graphic.popupTemplate = spots_template;
                        return graphic;
                    });
                    //今回のクリックによる検索結果を、グラフィックスレイヤーに登録（マップに表示）
                    resultsLayer.addMany(features);
                })
            };

            // ルート表示用のレイヤーにデータを追加　＋　周辺スポット表示関数呼び出し
            function showRoute(data) {
                const routeResult = data.routeResults[0].route;
                routeResult.symbol = routeArrowSymbol;
                routeLayer.add(routeResult);
                queryAroundSpot(routeResult.geometry)
            }

        });

        function toframe(data, id) {
            //frameの関数
            update_frame(data, id);
        }

        function kousin() {
            location.reload();
        }
    </script>

</head>

<body>
    <!--デバッグ用
    <input type="text" name="Result" value=<?php //echo $_SESSION["s_l_kankou_spots_id"] 
                                            ?>><br>
    <input type="text" name="Result" value=<?php //echo $_SESSION["l_d_kankou_spots_id"] 
                                            ?>><br>
    <input type="text" name="Result" value=<?php //echo $_SESSION["d_g_kankou_spots_id"] 
                                            ?>><br>
    -->
    <div id="viewbox">
        <h3>観光ルート</h3>
        <div id="viewDiv"></div>
        <button type="button" onclick="kousin()">観光経路更新</button>
    </div>
</body>

</html>