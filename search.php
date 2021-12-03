<?php

require "frame.php";

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

    //提出されたデータ
    $keywordCondition = [];
    $posts = [["yoyaku", $_POST["yoyaku"]], ["money", $_POST["lanch_money"]], ["money", $_POST["dinner_money"]]];

    $search_name = strtr($_POST["search_name"], [
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
    if ($_POST["name_genre"] == "0") {
        $column2 = "genre";
    } else {
        $column2 = "name";
    }
    $keywordCondition[] = " $column2 LIKE '%" . $search_name . "%' ";

    //var_dump($keywordCondition);
    // ここで、 
    // [ 'product_name LIKE "%hoge%"', 
    //   'product_name LIKE "%fuga%"', 
    //   'product_name LIKE "%piyo%"' ]
    // という配列ができあがっている。

    // これをANDでつなげて、文字列にする
    $keywordCondition = implode(' AND ', $keywordCondition);

    //sql文にする
    $sql = 'SELECT * FROM test.minatomirai_shop_data WHERE ' . $keywordCondition . ' ';
    //$sql = " SELECT * FROM test.minatomirai_shop_data WHERE $column LIKE '%" . $search_name . "%' ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    echo "失敗:" . $e->getMessage() . "\n";
    exit();
}



?>

<html>

<head>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <title>飲食店検索</title>
    <style>
        #detailbox {
            position: relative;
            float: left;
            margin-left: 25px;
        }

        #detailbox h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        #detailbox #infobox {
            float: left;
            width: 60vw;
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
            width: 15vw;
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

        #detailbox #infobox:hover {
            background: #000080;
            border: solid 1px #FFFFFF;
        }

        #detailbox #infobox table td pre {
            white-space: pre-wrap;
        }

        
            #detailbox #viewbox{
                float: left;
                width: 20vw;
                height: 20vw;
                margin-left: 5px;
            }

            #detailbox #viewbox #spotview{
                width: 100%;
                height: 100%;
            }

             
            #detailbox #commentbox{
                float: left;
                width: 40vw;
                height: 20vw;
                height: auto;
                margin-left: 5px;
                border: 1px solid #000080;
                overflow: scroll;
            }

            @media screen and (min-width:769px) and (max-width:1366px){
                h3{
                    margin: 0px;
                    font-size: 18px;
                }
            }
            
            @media screen and (max-width:768px){
                h3{
                    margin: 0px;
                    font-size: 17px;
                }

                #detailbox{
                    width: auto;
                    margin: 0px;
                    float: none;
                }

                #detailbox #imgbox{
                    width: 100%;
                    height: auto;
                    float: none;
                }

                #detailbox #infobox{
                    width: 100%;
                    float: none;
                }

                #detailbox #infobox table{
                    font-size: 13px;
                }

                #detailbox #viewbox{
                    width: 100%;
                    height: 90vw;
                    float: none;
                }

                #detailbox #commentbox{
                    width: 100%;
                    float: none;
                }
            }
            
    </style>
</head>

<script type="text/javascript">
    function post_food(lanch_id, mode) {
        jQuery(function($) {
            $.ajax({
                url: "ajax_food_shop.php",
                type: "POST",
                dataType: "json",
                data: {
                    post_data_1: lanch_id,
                    post_data_2: mode
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert("ajax通信に失敗しました");
                },
                success: function(response) {
                    //frameの関数
                    //alert("昼食IDは" + response);
                    update_frame(response[0], response[1]);

                }
            });
        });
    };
</script>

<body>
    <h2>検索</h2>
    <form action="search.php" method="post">
        検索の設定：
        <input type="radio" id="name_genre" name="name_genre" value="0" checked="checked">ジャンルで検索
        <input type="radio" id="name_genre" name="name_genre" value="1">店名で検索<br>

        予約の可否：
        <input type="radio" id="yoyaku" name="yoyaku" value="0" checked="checked">指定なし
        <input type="radio" id="yoyaku" name="yoyaku" value="予約可">予約可
        <input type="radio" id="yoyaku" name="yoyaku" value="予約不可">予約不可<br>

        昼食の予算：
        <input type="radio" id="lanch_money" name="lanch_money" value="0" checked="checked">指定なし
        <input type="radio" id="lanch_money" name="lanch_money" value="[昼]～￥999">￥～999
        <input type="radio" id="lanch_money" name="lanch_money" value="[昼]￥1,000～￥1,999">￥1,000～￥1,999
        <input type="radio" id="lanch_money" name="lanch_money" value="[昼]￥2,000～￥2,999">￥2,000～￥2,999
        <input type="radio" id="lanch_money" name="lanch_money" value="[昼]<3000">￥3,000～<br>

        夕食の予算：
        <input type="radio" id="dinner_money" name="dinner_money" value="0" checked="checked">指定なし
        <input type="radio" id="dinner_money" name="dinner_money" value="[夜]～￥999">￥～999
        <input type="radio" id="dinner_money" name="dinner_money" value="[夜]￥1,000～￥1,999">￥1,000～￥1,999
        <input type="radio" id="dinner_money" name="dinner_money" value="[夜]￥2,000～￥2,999">￥2,000～￥2,999
        <input type="radio" id="dinner_money" name="dinner_money" value="[夜]<3000">￥3,000～<br>

        <!-- 任意の<input>要素＝入力欄などを用意する -->
        <input type="text" name="search_name">
        <!-- 送信ボタンを用意する -->
        <input type="submit" name="submit" value="検索する"><br>
    </form>
    <a href="keiro.php">観光スポットの選択へ</a><br><br>

    <?php foreach ($stmt as $row) : ?>
        <div id="detailbox">

            <div id="box" class="clearfix">

                <div id="infobox" value=<?php echo $row["id"]; ?>>
                    <table>
                        <tr>
                            <th>店舗名</th>
                            <td><?php echo $row["name"]; ?></td>
                        </tr>

                        <tr>
                            <th>ジャンル</th>
                            <td><?php echo $row["genre"]; ?></td>
                        </tr>

                        <tr>
                            <th>営業時間</th>
                            <td><?php echo nl2br($row["time"]); ?></td>
                        </tr>

                        <tr>
                            <th>予算</th>
                            <td><?php echo $row["money"]; ?></td>
                        </tr>

                        <tr>
                            <th>予約</th>
                            <td><?php echo nl2br($row["yoyaku"]); ?></td>
                        </tr>

                        <tr>
                            <th>お問い合わせ</th>
                            <td><?php echo $row["tel"]; ?></td>
                        </tr>

                        <tr>
                            <th>ホームページURL</th>
                            <td>
                                <?php
                                if (!empty($row["homepage"])) {
                                    print "<a href = " . $row[7] . " target=_blank>" . $row[7] . "</a>";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>設定する</th>
                            <td>
                                <button type="button" name="lanch_id" value=<?php echo $row["id"]; ?> onclick="post_food(value, '1')">昼食に設定する</button>
                                <button type="button" name="dinner_id" value=<?php echo $row["id"]; ?> onclick="post_food(value, '2')">夕食に設定する</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</body>

</html>