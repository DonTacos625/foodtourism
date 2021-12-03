<?php

require "frame.php";

/*
$station_id = array();
if(!empty($station_id[0])){
    $_SESSION["start_station_id"] = $station_id[0];
}
if(!empty($station_id[1])){
    $_SESSION["goal_station_id"] = $station_id[1];
}

$station_id[0] = 1;
$station_id[1] = 2;


$reco_id = array();
for($i=0; $i<2; $i++){
    if(!empty($station_id[$i])){
    $reco_id[$i] = $station_id[$i];
    }
}
*/

//$_SESSION["start_station_id"] = $_POST["start_station_id"];
//$_SESSION["goal_station_id"] = $_POST["goal_station_id"];

//$station_id = [1,2];

//$_SESSION["start_station_id"] = 1;
//$_SESSION["goal_station_id"] = 1;
//$_SESSION["s_l_kankou_spots_id"] = 12;

//stations_id
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

//food_shops_id
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
} catch (PDOException $e) {
}

//kankou_spots_id
if (!isset($_SESSION["s_l_kankou_spots_id"])) {
    $s_l_kankou_spots_id = [0, 0];
} else {
    $s_l_kankou_spots_id = [$result5["x"], $result5["y"]];
}

$s_l = array(array($result1["X"], $result1["Y"]), $s_l_kankou_spots_id, array($result2["X"], $result2["Y"]));
$l_d = array(array($result2["X"], $result2["Y"]), array($result3["X"], $result3["Y"]));
$d_g = array(array($result3["X"], $result3["Y"]), array($result4["X"], $result4["Y"]));

?>

<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <style>
        html,
        body,
        #viewbox {
            position: relative;
            float: left;
            width: 80vw;
            height: 80vh;
            margin-left: 5px;
        }

        #viewbox #viewDiv {
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
        //スタート駅表示関数
        var $station_feature_sql = "ID = ";

        function post_station(e) {
            jQuery(function($) {
                $.ajax({
                    url: 'ajax1.php',
                    type: "POST",
                    dataType: 'json',
                    data: {
                        post_data_1: e
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("ajax通信に失敗しました");
                    },
                    success: function(data) {
                        alert("夜は" + data[0]);
                    }
                });
            });
        };

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

            var startAction = {
                title: "開始地点に設定",
                id: "start",
                className: "esri-icon-right-arrow-circled"
            };

            var goalAction = {
                title: "終了地点に設定",
                id: "goal",
                className: "esri-icon-left-arrow-circled"
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
            const routeLayer_s_l = new GraphicsLayer();
            const routeLayer_l_d = new GraphicsLayer();
            const routeLayer_d_g = new GraphicsLayer();

            //周辺スポットのレイヤー
            const resultsLayer_s_l = new GraphicsLayer();
            const resultsLayer_l_d = new GraphicsLayer();
            const resultsLayer_d_g = new GraphicsLayer();

            //ルート計算の設定
            const s_l_routeParams = new RouteParameters({
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
            const l_d_routeParams = new RouteParameters({
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
            const d_g_routeParams = new RouteParameters({
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
            const stopSymbol2 = {
                type: "simple-marker", // autocasts as new SimpleMarkerSymbol()
                style: "cross",
                size: 15,
                outline: {
                    // autocasts as new SimpleLineSymbol()
                    width: 4
                }
            };

            const stopSymbol = new PictureMarkerSymbol({
                url: "./marker/purple.png",
                width: "30px",
                height: "30px"
            });

            // Define the symbology used to display the route
            const routeSymbol1 = {
                type: "simple-line", // autocasts as SimpleLineSymbol()
                color: [0, 0, 255, 0.5],
                width: 5
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
                                    placementTemplate: [19.5], // determines space between each arrow
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
            })

            const routeSymbol2 = {
                type: "simple-line", // autocasts as SimpleLineSymbol()
                color: [0, 255, 0, 0.5],
                width: 5
            };

            const routeSymbol3 = {
                type: "simple-line", // autocasts as SimpleLineSymbol()
                color: [255, 0, 0, 0.5],
                width: 5
            };

            //マップとレイヤーの定義
            const map = new Map({
                basemap: "streets",
                layers: [stationLayer, foodLayer, routeLayer_s_l, routeLayer_l_d, routeLayer_d_g, resultsLayer_s_l, resultsLayer_l_d, resultsLayer_d_g]
            });
            const view = new MapView({
                container: "viewDiv", // Reference to the scene div created in step 5
                map: map, // Reference to the map object created before the scene
                center: [139.635, 35.453],
                zoom: 15
            });

            var s_l_Action = {
                title: "ルートに追加",
                id: "s_l_add",
                className: "esri-icon-left-arrow-circled"
            };
            var l_d_Action = {
                title: "ルートに追加",
                id: "l_d_add",
                className: "esri-icon-left-arrow-circled"
            };
            var d_g_Action = {
                title: "ルートに追加",
                id: "d_g_add",
                className: "esri-icon-left-arrow-circled"
            };

            //new popudd
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "s_l_add") {
                    popadd(event, routeLayer_s_l, s_l_routeParams, "1");
                }
            });
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "l_d_add") {
                    popadd(event, routeLayer_l_d, l_d_routeParams, "2");
                }
            });
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "d_g_add") {
                    popadd(event, routeLayer_d_g, d_g_routeParams, "3");
                }
            });

            function popadd(event, r_Layer, r_Params, num) {
                const point = {
                    type: "point",
                    x: view.popup.selectedFeature.attributes.X,
                    y: view.popup.selectedFeature.attributes.Y
                };
                const stop = new Graphic({
                    geometry: point,
                    symbol: stopSymbol
                });
                r_Layer.add(stop);
                r_Params.stops.features.splice(1, 0, stop);
                if (num == "1") {
                    route.solve(routeUrl, r_Params).then(showRoute1);
                } else if (num == "2") {
                    route.solve(routeUrl, r_Params).then(showRoute2);
                } else if (num == "3") {
                    route.solve(routeUrl, r_Params).then(showRoute3);
                }
            }


            //実験場
            var hozonAction = {
                title: "保存",
                id: "hozon",
                className: "esri-icon-left-arrow-circled"
            };
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "hozon") {
                    hozon();
                }
            });

            function hozon() {
                //スポット・駅座標取得
                const spot_id = view.popup.selectedFeature.attributes.id;
                jQuery(function($) {
                    $.ajax({
                        url: "ajax3.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: spot_id
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("ajax通信に失敗しました");
                        },
                        success: function(response) {
                            alert(response[0]);
                        }
                    });
                });
            };

            //new world start
            $s_l = <?php echo json_encode($s_l); ?>;
            $l_d = <?php echo json_encode($l_d); ?>;
            $d_g = <?php echo json_encode($d_g); ?>;

            addlayer($s_l, routeLayer_s_l, s_l_routeParams, "1");
            addlayer($l_d, routeLayer_l_d, l_d_routeParams, "2");
            addlayer($d_g, routeLayer_d_g, d_g_routeParams, "3");

            //各レイヤーに目的地を入れる
            function addlayer(points, Layer, Params, num) {
                for (var j = 0; j < points.length; j++) {
                    if (!(points[j][0] == 0)) {
                        const point = {
                            type: "point",
                            x: points[j][0],
                            y: points[j][1]
                        };
                        const stop = new Graphic({
                            geometry: point,
                            symbol: stopSymbol
                        });
                        Layer.add(stop);
                        Params.stops.features.push(stop);
                        if (Params.stops.features.length >= 2) {
                            if (num == "1") {
                                route.solve(routeUrl, Params).then(showRoute1);
                            } else if (num == "2") {
                                route.solve(routeUrl, Params).then(showRoute2);
                            } else if (num == "3") {
                                route.solve(routeUrl, Params).then(showRoute3);
                            }
                        }
                    }
                }
            };

            //経路場周辺のスポットを表示
            AroundSpots = (geom, Layer, num) => {
                //ルート形状から 150m のバッファ内の観光スポットを検索するための query式を作成
                let query = spotLayer.createQuery();
                query.geometry = geom;
                query.outFields = ["*"]
                query.distance = 100;
                query.units = "meters";
                //観光スポットに対する検索の実行
                spotLayer.queryFeatures(query).then(function(featureSet) {
                    var result_fs = featureSet.features;
                    //検索結果が0件だったらリセット
                    if (result_fs.length === 0) {
                        Layer.removeAll();
                    }
                    //前回の検索結果を、グラフィックスレイヤーから削除
                    Layer.removeAll();
                    //検索結果に対する設定
                    var features = result_fs.map(function(graphic) {
                        //シンボル設定 + ポップアップ設定
                        if (num == "1") {
                            graphic.symbol = {
                                type: "simple-marker",
                                style: "diamond",
                                size: 10.5,
                                color: "darkorange"
                            };
                            graphic.popupTemplate = {
                                title: "{name}",
                                content: [{
                                    type: "fields",
                                    fieldInfos: [{
                                        fieldName: "name",
                                        label: "スポット名",
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
                                        visible: false
                                    }, {
                                        fieldName: "Y",
                                        label: "緯度",
                                        visible: false
                                    }, {
                                        fieldName: "id",
                                        label: "id",
                                        visible: false
                                    }]
                                }],
                                actions: [s_l_Action, hozonAction]
                            }
                        } else if (num == "2") {
                            graphic.symbol = {
                                type: "simple-marker",
                                style: "diamond",
                                size: 10.5,
                                color: "darkorange"
                            };
                            graphic.popupTemplate = {
                                title: "{name}",
                                content: [{
                                    type: "fields",
                                    fieldInfos: [{
                                        fieldName: "name",
                                        label: "スポット名",
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
                                        visible: false
                                    }, {
                                        fieldName: "Y",
                                        label: "緯度",
                                        visible: false
                                    }, {
                                        fieldName: "id",
                                        label: "id",
                                        visible: false
                                    }]
                                }],
                                actions: [l_d_Action, hozonAction]
                            }
                        } else if (num == "3") {
                            graphic.symbol = {
                                type: "simple-marker",
                                style: "diamond",
                                size: 10.5,
                                color: "darkorange"
                            };
                            graphic.popupTemplate = {
                                title: "{name}",
                                content: [{
                                    type: "fields",
                                    fieldInfos: [{
                                        fieldName: "name",
                                        label: "スポット名",
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
                                        visible: false
                                    }, {
                                        fieldName: "Y",
                                        label: "緯度",
                                        visible: false
                                    }, {
                                        fieldName: "id",
                                        label: "id",
                                        visible: false
                                    }]
                                }],
                                actions: [d_g_Action, hozonAction]
                            }
                        }
                        return graphic;
                    });
                    Layer.addMany(features);
                })
            };

            //ルート表示のレイヤーにデータを入れ、周辺スポット表示関数を呼び出す
            function showRoute1(data) {
                const routeResult = data.routeResults[0].route;
                routeResult.symbol = routeArrowSymbol;
                routeLayer_s_l.add(routeResult);
                AroundSpots(routeResult.geometry, resultsLayer_s_l, "1");
            };

            function showRoute2(data) {
                const routeResult = data.routeResults[0].route;
                routeResult.symbol = routeSymbol2;
                routeLayer_l_d.add(routeResult);
                AroundSpots(routeResult.geometry, resultsLayer_l_d, "2");
            };

            function showRoute3(data) {
                const routeResult = data.routeResults[0].route;
                routeResult.symbol = routeSymbol3;
                routeLayer_d_g.add(routeResult);
                AroundSpots(routeResult.geometry, resultsLayer_d_g, "3");
            };
            //new world end


            /*
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "start") {
                    start_station();
                }
            });

            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "goal") {
                    goal_station();
                }
            });

            function start_station(event) {
                const station_id = view.popup.selectedFeature.attributes.ID;
                station_feature_sql = "ID = ";
                station_feature_sql += station_id;
                alert(station_feature_sql);
            }

            function goal_station(event) {
                const station_id = view.popup.selectedFeature.attributes.ID;
                station_feature_sql = "ID = ";
                station_feature_sql += station_id;
            }

            function getData() {
                //document.getElementById("Result").value = "問い合わせ中です…";
                var data = {
                    "code": document.getElementById("stations").value
                }
                var json = JSON.stringify(data);
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "ajax3.php");
                xhr.setRequestHeader("content-type", "application/x-www-form-urlencoded;charset=UTF-8");
                xhr.send(json);
                
                xhr.onreadystatechange = function() {
                    try {
                        if (xhr.readyState == 4) {
                            if (xhr.status == 200) {
                                var result = JSON.parse(xhr.response);
                                document.getElementById("Result").value = result.value == 0 ? "選択してください" : result.value;
                            } else {}
                        } else {}
                    } catch (e) {}
                };
                
            }
            */

        });
    </script>

    <?php
    //選択駅のIDをセッション変数化
    if (isset($_POST["start_station_id"])) {
        $_SESSION["start_station_id"] = $_POST["start_station_id"];
    }
    if (isset($_POST["goal_station_id"])) {
        $_SESSION["goal_station_id"] = $_POST["goal_station_id"];
    }
    ?>

</head>

<body>
    <p>
        開始駅を選択する：
    <form method="" action="" onsubmit="return false">
        <select name="stations" size="1" id="stations" onchange="post_station(value)">
            <option value="1">みなとみらい駅</option>
            <option value="2">サンプル2</option>
            <option value="3">サンプル3</option>
        </select>
    </form>
    </p>
    <div id="viewbox">
        <div id="viewDiv"></div>
        <button type="button" onclick="syuui()">周辺</button>
    </div>
</body>

</html>