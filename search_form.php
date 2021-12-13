<?php

require "frame.php";

if (!empty($_GET["not_set_food"])) {
    $message = "先に昼食・夕食をする飲食店を設定してください";
} else {
    $message = "";
}

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

function set_checked($session_name, $value)
{
    if ($value == $_SESSION[$session_name]) {
        print "checked=\"checked\"";
    } else {
        print "";
    }
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
    <title>飲食店検索</title>
    <style>
        h2 {
            border-left: 5px solid #000080;
            margin: 0px;
        }
    </style>
</head>

<script>
    function input_search_name(word) {
        const update = document.getElementById("search_name");
        update.value = word;
    };
</script>

<body>
    <div>
        <font color="#ff0000"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></font>
    </div>
    <h2>飲食店検索</h2>
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
        <input type="text" value="" id="search_name" name="search_name">
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
        <input type="submit" name="submit" value="検索する"><br>
    </form>

    <br>
</body>

</html>