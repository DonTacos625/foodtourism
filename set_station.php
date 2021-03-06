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

try {

    if ($start_station_id != 0) {
        $stmt1 = $pdo->prepare("SELECT * FROM minatomirai_station_data WHERE id = :id");
        $stmt1->bindParam(":id", $start_station_id);
        $stmt1->execute();
        $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $start_station_keikaku[] = [$result1["x"], $result1["y"], "start"];
        $start_keep_name = [$start_station_id, $result1["name"]];
    } else {
        $start_station_keikaku[] = [0, 0, "start"];
        $start_keep_name = [0, "開始駅を選択してください"];
    }

    if ($goal_station_id != 0) {
        $stmt4 = $pdo->prepare("SELECT * FROM minatomirai_station_data WHERE id = :id");
        $stmt4->bindParam(":id", $goal_station_id);
        $stmt4->execute();
        $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        $goal_station_keikaku[] = [$result4["x"], $result4["y"], "goal"];
        $goal_keep_name = [$goal_station_id, $result4["name"]];
    } else {
        $goal_station_keikaku[] = [0, 0, "goal"];
        $goal_keep_name = [0, "終了駅を選択してください"];
    }

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
    <title>開始・終了駅の設定</title>

    <style>
        h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        #editbox {
            position: relative;
            float: left;
            width: 400px;
            margin-left: 5px;
        }

        #editbox #start_box {
            float: left;
        }

        #editbox #goal_box {
            float: right;
        }

        #editbox #post_station {
            float: right;
        }

        .move_box {
            position: relative;
            width: 76vw;
            float: left;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            h2 {
                font-size: 20px;
            }
        }

        @media screen and (max-width:768px) {
            h2 {
                font-size: 19px;
            }

            .move_box {
                width: 100%;
            }

            #editbox {
                width: 100%;
            }
        }
    </style>


    <link rel="stylesheet" href="https://js.arcgis.com/4.21/esri/themes/light/main.css" />
    <script src="https://js.arcgis.com/4.21/"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <script type="text/javascript">
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

            //popup
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
            var stationLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_station/FeatureServer",
                id: "stationLayer",
                popupTemplate: station_template,
                labelingInfo: [labelClass]
            });

            //選択したスポットの表示レイヤー
            const start_station_pointLayer = new GraphicsLayer();
            const goal_station_pointLayer = new GraphicsLayer();

            const map = new Map({
                basemap: "streets",
                layers: [stationLayer, start_station_pointLayer, goal_station_pointLayer]
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

            //マップ上にポイントを追加
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

            //マップ上から開始駅・終了駅を設定する
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
                            }
                            //esriの関数の外へ
                            toframe(response[0], response[1]);
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


        //駅の初期値
        $start_id = "";
        $goal_id = "";

        function set_station(id, mode) {
            if (mode == "start") {
                $start_id = id;
            } else if (mode == "goal") {
                $goal_id = id;
            }
        };

        //セレクトボックスから駅を設定
        function add_spots_select(id, num) {
            if (id != "0") {
                jQuery(function($) {
                    $.ajax({
                        url: "./ajax_view_addspots.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: id,
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
                            }
                            update_frame(response[0], response[1]);
                            location.reload();
                        }
                    });
                });
            }
        };
    </script>

</head>

<body>
    <div class="container">
        <main>
            <h3>開始・終了駅の設定</h3>
            <div id="editbox">
                <div id="start_box">
                    開始駅を選択する：<br>
                    <select name="start_station_id" size="1" onchange="add_spots_select(value, '1')">
                        <option value=<?php echo $start_keep_name[0]; ?>> <?php echo $start_keep_name[1]; ?> </option>
                        <?php foreach ($stmt as $row) : ?>
                            <option value=<?php echo $row["id"]; ?>> <?php echo $row["name"]; ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="goal_box">
                    終了駅を選択する：<br>
                    <select name="goal_station_id" size="1" onchange="add_spots_select(value, '4')">
                        <option value=<?php echo $goal_keep_name[0]; ?>> <?php echo $goal_keep_name[1]; ?> </option>
                        <?php foreach ($stmt2 as $row2) : ?>
                            <option value=<?php echo $row2["id"]; ?>> <?php echo $row2["name"]; ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <br><br><br>

                <div class="move_box">
                    <a class="prev_page" name="prev_explain" href="explain.php#set_station">使い方に戻る</a>
                    <a class="next_page" name="next_search" href="search.php">飲食店の検索・決定へ</a>
                </div><br>
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