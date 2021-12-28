<?php

require "frame.php";

try {
    /*
    値段の設定
    if (!isset($_SESSION["search_dinner_min"])) {
        $_SESSION["search_dinner_min"] = "0";
    }
    if (!isset($_SESSION["search_dinner_max"])) {
        $_SESSION["search_dinner_max"] = "0";
    }

    if (isset($_POST["dinner_min"])) {
        $dinner_min = $_POST["dinner_min"];
        $_SESSION["search_dinner_min"] = $_POST["dinner_min"];
    } else {
        $dinner_min = $_SESSION["search_dinner_min"];
    }
    if (isset($_POST["dinner_max"])) {
        $dinner_max = $_POST["dinner_max"];
        $_SESSION["search_dinner_max"] = $_POST["dinner_max"];
    } else {
        $dinner_max = $_SESSION["search_dinner_max"];
    }
    */


    if (!isset($_SESSION["search_yoyaku"])) {
        $_SESSION["search_yoyaku"] = "0";
    }
    if (!isset($_SESSION["search_lanch_money"])) {
        $_SESSION["search_lanch_money"] = "0";
    }
    if (!isset($_SESSION["search_dinner_money"])) {
        $_SESSION["search_dinner_money"] = "0";
    }
    if (!isset($_SESSION["search_name_genre"])) {
        $_SESSION["search_name_genre"] = "0";
    }
    if (!isset($_SESSION["search_name"])) {
        $_SESSION["search_name"] = "";
        $search_word = "";
    } else {
        $search_word = $_SESSION["search_name"];
    }

    //提出されたデータ
    if (isset($_POST["yoyaku"])) {
        $yoyaku = $_POST["yoyaku"];
        $_SESSION["search_yoyaku"] = $_POST["yoyaku"];
    } else {
        $yoyaku = $_SESSION["search_yoyaku"];
    }
    if (isset($_POST["lanch_money"])) {
        $lanch_money = $_POST["lanch_money"];
        $_SESSION["search_lanch_money"] = $_POST["lanch_money"];
    } else {
        $lanch_money = $_SESSION["search_lanch_money"];
    }
    if (isset($_POST["dinner_money"])) {
        $dinner_money = $_POST["dinner_money"];
        $_SESSION["search_dinner_money"] = $_POST["dinner_money"];
    } else {
        $dinner_money = $_SESSION["search_dinner_money"];
    }
    if (isset($_POST["name_genre"])) {
        $name_genre = $_POST["name_genre"];
        $_SESSION["search_name_genre"] = $_POST["name_genre"];
    } else {
        $name_genre = $_SESSION["search_name_genre"];
    }
    if (isset($_POST["search_name"])) {
        $search_name = $_POST["search_name"];
        $_SESSION["search_name"] = $_POST["search_name"];
    } else {
        $search_name = $_SESSION["search_name"];
    }

    $keywordCondition = [];
    //posts = [["データベースのカラム名", "検索条件"]]
    $posts = [["yoyaku", $yoyaku], ["money", $lanch_money], ["money", $dinner_money]];

    $search_word = strtr($search_name, [
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
    if ($name_genre == "0") {
        $column2 = "genre";
    } else {
        $column2 = "name";
    }
    $keywordCondition[] = " $column2 LIKE '%" . $search_word . "%' ";

    /*
    //値段の設定
    $range = " $column > '%" . $keyword . "%' ";
    */

    //var_dump($keywordCondition);
    // ここで、 
    // [ 'product_name LIKE "%hoge%"', 
    //   'product_name LIKE "%fuga%"', 
    //   'product_name LIKE "%piyo%"' ]
    // という配列ができあがっている。

    // これをANDでつなげて、文字列にする
    $keywordCondition = implode(' AND ', $keywordCondition);

    //sql文にする
    $sql = 'SELECT * FROM minatomirai_shop_data WHERE ' . $keywordCondition . ' ';

    //忘れ形見
    //$sql = " SELECT * FROM minatomirai_shop_data WHERE $column LIKE '%" . $search_name . "%' ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    echo "失敗:" . $e->getMessage() . "\n";
    exit();
}

//検索条件の保存のため
function set_checked($session_name, $value)
{
    if ($value == $_SESSION[$session_name]) {
        //値がセッション変数と等しいとチェックされてる判定として返す
        print "checked=\"checked\"";
    } else {
        print "";
    }
}

$count = 0;

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
    <title>飲食店の検索・決定（一覧表示）</title>
    <style>
        h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        #detailbox {
            position: relative;
            float: left;
            margin-left: 0px;
        }

        #detailbox #infobox {
            float: left;
            width: 75vw;
            margin-left: 5px;
        }

        #detailbox #infobox table {
            width: 100%;
            border: solid 3px #ffffff;
        }

        #detailbox #infobox table th {
            text-align: left;
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

        #detailbox #infobox table td pre {
            white-space: pre-wrap;
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

            .search_form {
                font-size: 12px;
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

        }
    </style>
</head>

<script type="text/javascript">
    //昼食・夕食を決定
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
                    //alert("昼食IDは" + response);
                    //frameの関数
                    update_frame(response[0], response[1]);
                    if (mode == "1") {
                        alert("「" + response[0] + "」を昼食に設定しました");
                    } else {
                        alert("「" + response[0] + "」を夕食に設定しました");
                    }
                }
            });
        });
    };

    //セレクトボックスから選ばれたワードを検索ワードボックスに入れる　もっといい方法あるかも
    function input_search_name(word) {
        const update = document.getElementById("search_name");
        update.value = word;
    };
</script>

<body>
    <div class="container">
        <main>
            <div id="detailbox">
                <h3 id="search_start">飲食店の検索・決定</h3>
                <a id="view_result" name="view_result" href="search_viewmode.php">地図上で結果を表示</a><br>
                <div class="search_form">
                    <form action="search.php" method="post">
                        予約の可否：
                        <input type="radio" id="yoyaku" name="yoyaku" value="0" <?php set_checked("search_yoyaku", "0"); ?>>指定なし
                        <input type="radio" id="yoyaku" name="yoyaku" value="予約可" <?php set_checked("search_yoyaku", "予約可"); ?>>予約可
                        <input type="radio" id="yoyaku" name="yoyaku" value="予約不可" <?php set_checked("search_yoyaku", "予約不可"); ?>>予約不可<br>

                        昼食の予算：
                        <input type="radio" id="lanch_money" name="lanch_money" value="0" <?php set_checked("search_lanch_money", "0"); ?>>指定なし
                        <input type="radio" id="lanch_money" name="lanch_money" value="[昼]～￥999" <?php set_checked("search_lanch_money", "[昼]～￥999"); ?>>～￥999
                        <input type="radio" id="lanch_money" name="lanch_money" value="[昼]￥1,000～￥1,999" <?php set_checked("search_lanch_money", "[昼]￥1,000～￥1,999"); ?>>￥1,000～￥1,999
                        <input type="radio" id="lanch_money" name="lanch_money" value="[昼]￥2,000～￥2,999" <?php set_checked("search_lanch_money", "[昼]￥2,000～￥2,999"); ?>>￥2,000～￥2,999
                        <input type="radio" id="lanch_money" name="lanch_money" value="[昼]<3000" <?php set_checked("search_lanch_money", "[昼]<3000"); ?>>￥3,000～<br>

                        夕食の予算：
                        <input type="radio" id="dinner_money" name="dinner_money" value="0" <?php set_checked("search_dinner_money", "0"); ?>>指定なし
                        <input type="radio" id="dinner_money" name="dinner_money" value="[夜]～￥999" <?php set_checked("search_dinner_money", "[夜]～￥999"); ?>>～￥999
                        <input type="radio" id="dinner_money" name="dinner_money" value="[夜]￥1,000～￥1,999" <?php set_checked("search_dinner_money", "[夜]￥1,000～￥1,999"); ?>>￥1,000～￥1,999
                        <input type="radio" id="dinner_money" name="dinner_money" value="[夜]￥2,000～￥2,999" <?php set_checked("search_dinner_money", "[夜]￥2,000～￥2,999"); ?>>￥2,000～￥2,999
                        <input type="radio" id="dinner_money" name="dinner_money" value="[夜]<3000" <?php set_checked("search_dinner_money", "[夜]<3000"); ?>>￥3,000～<br>

                        検索の設定：
                        <input type="radio" id="name_genre" name="name_genre" value="0" <?php set_checked("search_name_genre", "0"); ?>>ジャンルで検索
                        <input type="radio" id="name_genre" name="name_genre" value="1" <?php set_checked("search_name_genre", "1"); ?>>店名で検索<br>

                        検索ワード：
                        <input type="text" value="<?php echo $search_word; ?>" id="search_name" name="search_name">
                        <select name="genre_example" size="1" onclick="input_search_name(value)">
                            <option value=""> ワードを入力するか以下から選択してください </option>
                            <option value="中華"> 中華 </option>
                            <option value="和食"> 和食 </option>
                            <option value="洋食"> 洋食 </option>
                            <option value="イタリアン"> イタリアン </option>
                            <option value="フレンチ"> フレンチ </option>
                            <option value="居酒屋"> 居酒屋 </option>
                            <option value="バイキング"> バイキング </option>
                            <option value="カフェ"> カフェ </option>
                        </select>
                        <br>
                        <input type="submit" name="submit" value="検索する">
                    </form>
                </div><br>
                <div class="move_box">
                    <a class="prev_page" name="prev_station" href="set_station.php">開始・終了駅選択に戻る</a>
                    <a class="next_page" name="next_keiro" href="keiro.php">観光スポット選択へ</a><br>
                </div>

                <?php foreach ($stmt as $row) : ?>
                    <?php $count += 1; ?>
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
                                        print "<a href = " . $row["homepage"] . " target=_blank>ホームページにアクセスする</a>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>設定する</th>
                                <td>
                                    <button type="button" id="lanch_id" name="lanch_id" value=<?php echo $row["id"]; ?> onclick="post_food(value, '1')">昼食に設定する</button>
                                    <button type="button" id="dinner_id" name="dinner_id" value=<?php echo $row["id"]; ?> onclick="post_food(value, '2')">夕食に設定する</button>
                                </td>
                            </tr>
                            <tr>
                                <th>地図付き詳細ページへ</th>
                                <td>
                                    <a href="shopdetail.php?spot_id=<?php echo $row["id"]; ?>">詳細ページに移動する</a>
                                </td>
                            </tr>
                        </table>
                        <a href="#search_start">▲ページ上部に戻る</a>
                    </div><br>
                <?php endforeach; ?>
                <?php
                if ($count == 0) {
                    echo "検索条件に該当する飲食店はありませんでした";
                }
                ?>
            </div>
            <br>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>