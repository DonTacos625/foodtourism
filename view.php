<?php

require "frame.php";

?>

<html>

<head>
    <meta charset="utf-8" />
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-131239045-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-131239045-1');
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
            var routeAction = {
                title: "ルートに追加する",
                id: "route",
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
                        visible: false
                    }, {
                        fieldName: "Y",
                        label: "緯度",
                        visible: false
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
                        visible: false
                    }, {
                        fieldName: "Y",
                        label: "緯度",
                        visible: false
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
                        visible: false
                    }, {
                        fieldName: "Y",
                        label: "緯度",
                        visible: false
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
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_station_data/FeatureServer",
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


            var layerlist = new LayerList({
                view: view,
                listItemCreatedFunction: function(event) {

                    // The event object contains properties of the
                    // layer in the LayerList widget.

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