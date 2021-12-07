<?php

require "frame.php";

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

        @media screen and (min-width:769px) and (max-width:1366px) {
            h3 {
                font-size: 18px;
            }

            #viewbox {
                width: 70vw;
                height: 70vh;
            }
        }

        @media screen and (max-width:768px) {
            h3 {
                font-size: 15px;
            }

            #viewbox {
                width: 110vw;
                height: 160vh;
                margin: 0px;
            }

            #viewbox #viewDiv {
                width: 85%;
                height: 85%;
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
            const MY_API_KEY = "AAPKfe5fdd5be2744698a188fcc0c7b7b1d742vtC5TsStg94fpwkldrfNo3SJn2jl_VuCOEEdcBiwR7dKOKxejIP_3EDj9IPSPg";
            //popup
            var lanch_Action = {
                title:"昼食に設定する",
                id: "lanch_id",
                className: "esri-icon-navigation"
            };

            var dinner_Action = {
                title:"夕食に設定する",
                id: "dinner_id",
                className: "esri-icon-navigation"
            };

            var start_Action = {
                title:"開始駅に設定する",
                id: "start_station_id",
                className: "esri-icon-navigation"
            };

            var goal_Action = {
                title:"終了駅に設定する",
                id: "goal_station_id",
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
                }]
            };

            //spotLayer
            var foodLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_shop_new_UTF_8/FeatureServer",
                id: "foodLayer",
                popupTemplate: food_template
            });

            var stationLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_station/FeatureServer",
                id: "stationLayer",
                popupTemplate: station_template
            });

            var spotLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_kankou_UTF_8/FeatureServer",
                id: "spotLayer",
                popupTemplate: spots_template
            });

            const map = new Map({
                basemap: "streets",
                layers: [stationLayer, foodLayer, spotLayer]
            });

            const view = new MapView({
                container: "viewDiv", // Reference to the scene div created in step 5
                map: map, // Reference to the map object created before the scene
                center: [139.635, 35.453],
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
                var spot_id = view.popup.selectedFeature.attributes.ID;
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

            var layerlist = new LayerList({
                view: view,
                listItemCreatedFunction: function(event) {
                    let item = event.item;
                    if (item.title === "Minatomirai kankou UTF 8") {
                        // open the list item in the LayerList
                        //item.open = true;
                        // change the title to something more descriptive
                        item.title = "観光スポット";
                    } else if (item.title === "Minatomirai shop new UTF 8") {
                        item.title = "飲食店";
                    } else if (item.title === "Minatomirai station data") {
                        item.title = "駅";
                    }
                }
            });
            layerlist.statusIndicatorsVisible = false;

            view.ui.add(layerlist, "bottom-right");
        });

        function toframe(data, id) {
            //frameの関数
            update_frame(data, id);
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
        <h3>スポット一覧</h3>
        <div id="viewDiv"></div>
    </div>
</body>

</html>