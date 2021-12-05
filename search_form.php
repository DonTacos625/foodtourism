<?php

require "frame.php";

if (!empty($_GET["not_set_food"])) {
    $message = "先に昼食・夕食をする飲食店を設定してください";
} else {
    $message = "";
}

?>

<html>

<head>
    <meta charset="UTF-8">
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
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <title>飲食店検索</title>
    <style>
        h2 {
            border-left: 5px solid #000080;
            margin: 0px;
        }
    </style>
</head>

<body>
    <div>
        <font color="#ff0000"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></font>
    </div>
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
    </form><br>
</body>

</html>