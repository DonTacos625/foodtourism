<?php

require "frame.php";

$spot_id = $_GET["spot_id"];

try {

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
    <title>スポット一覧</title>
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
            width: 40vw;
            height: 20vw;
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
            width: 40vw;
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

            #detailbox #infobox {
                width: 100%;
                float: none;
            }

            #detailbox #infobox table {
                font-size: 13px;
            }

            #detailbox #viewbox {
                width: 100%;
                height: 70vw;
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
            const MY_API_KEY = "AAPKfe5fdd5be2744698a188fcc0c7b7b1d742vtC5TsStg94fpwkldrfNo3SJn2jl_VuCOEEdcBiwR7dKOKxejIP_3EDj9IPSPg";
            //popup
            var lanch_Action = {
                title: "昼食に設定する",
                id: "lanch_id",
                className: "esri-icon-navigation"
            };

            var dinner_Action = {
                title: "夕食に設定する",
                id: "dinner_id",
                className: "esri-icon-navigation"
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

            var result1 = <?php echo json_encode($result1) ?>;
            var food_feature_sql = "ID = " + result1["id"];

            //spotLayer
            var foodLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_shop_new_UTF_8/FeatureServer",
                id: "foodLayer",
                popupTemplate: food_template,
                definitionExpression: food_feature_sql
            });

            const map = new Map({
                basemap: "streets",
                layers: [foodLayer]
            });

            const view = new MapView({
                container: "viewDiv", // Reference to the scene div created in step 5
                map: map, // Reference to the map object created before the scene
                center: [result1["x"], result1["y"]],
                zoom: 14
            });

            //ポップアップから追加
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "start_station_id") {
                    add_spots("1");
                }
                if (event.action.id === "lanch_id") {
                    add_spots("2");
                }
                if (event.action.id === "dinner_id") {
                    add_spots("3");
                }
                if (event.action.id === "goal_station_id") {
                    add_spots("4");
                }
            });

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
                            //alert(response[0]);
                            //esriの関数の外へ
                            toframe(response[0], response[1]);
                        }
                    });
                });
            };

        });

        function toframe(data, id) {
            //frameの関数
            update_frame(data, id);
        }
    </script>

</head>

<body>
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
                            if (!empty($row["homepage"])) {
                                print "<a href = " . $row["homepage"] . " target=_blank>ホームページにアクセスする</a>";
                            }
                            ?>
                        </td>
                    </tr>
                </table>
                <li><a href="search_form.php">飲食店検索に戻る</a></li>
            </div>
        </div>

    </div>
</body>

</html>