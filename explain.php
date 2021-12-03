<?php
require "frame.php";
?>

<!doctype html>
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

    <title>使い方</title>

    <style>
        #explainbox {
            width: 60vw;
            float: left;
            margin-left: 5px;
        }

        #explainbox h2 {
            margin: 0px;
        }

        #explainbox h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            h2 {
                font-size: 20px;
            }

            h3 {
                font-size: 18px;
            }
        }

        @media screen and (max-width:768px) {
            h2 {
                font-size: 19px;
            }

            h3 {
                font-size: 17px;
            }

            #explainbox {
                width: auto;
                margin: 0px;
            }
        }
    </style>
</head>

<body>
    <div id="explainbox">
        <h2 id="index">当サイトの使い方</h2>
        <ul>
            <li>ページの見方</li>
            <ul>
                <li><a href="#userinfo">会員情報</a></li>
                <li><a href="#plan">現在の観光計画</a></li>
                <li><a href="#survey">アンケート</a></li>
            </ul>
            <li>観光計画作成</li>
            <ul>
                <li><a href="#set_station">開始・終了駅の設定</a></li>
                <li><a href="#search">飲食店検索・決定</a></li>
                <li><a href="#select_spots">観光スポット選択</a></li>
            </ul>
            <li>マイページ</li>
            <ul>
                <li><a href="#editpassword">パスワード変更</a></li>
            </ul>
        </ul>

        <p>
        <h3>ページの見方</h3>
        <p>
            ページの右部には常に「会員情報」、「現在の観光計画」、「アンケート」が表示されています。<br><br>
            <b id="userinfo">会員情報</b><br>
            &emsp;あなたのIDと登録情報が表示されています。パスワードの変更はマイページから行えます。<br><br>
            <b id="plan">現在の観光計画</b><br>
            &emsp;あなたが作成した、現在の観光計画が表示されています。また、設定した観光スポットをリセットすることができます。<br><br>
            <b id="survey">アンケート</b><br>
            &emsp;システムを１度以上ご利用いただいたのち、アンケートにご協力をお願いします。「回答する」ボタンを押すことでGoogleFormからアンケートに回答できます。<br>
            <a href="#index">▲ページ上部に戻る</a>
        </p><br>
        </p>

        <p>
        <h3>観光計画作成の手順</h3>
        <p>
            観光経路を作成するために、以下の3つの手順を行います。<br><br>

            <b id="set_station">(1)開始・終了駅の設定</b><br>
            &emsp;あなたが観光を開始する駅と終了する駅をコメントボックスから選択し、決定ボタンを押してください。<br><br>
            <b id="search">(2)飲食店の検索・決定</b><br>
            &emsp;画面上部にある検索フォームから飲食店を検索することができます。
            また、各種項目を設定することでより絞り込んだ検索を行えます。
            昼食、夕食時に訪れたい飲食店が決まったら、
            「昼食に設定する」、「夕食に設定する」を押してそれぞれに設定してください。<br><br>
            <b id="select_spots">(3)観光スポットの選択</b><br>
            &emsp;現在の観光経路周辺の観光スポットが地図上に表示されます。
            その中から訪れたい観光スポットのマーカーをクリック(タップ)し、
            「昼食前」、「昼食後」、「夕食後」の各アイコンを押すことで、
            どの時間に訪れるか選択することができます。
            スポットの設定を削除したい場合はページ右部の「現在の観光計画」
            から該当するスポットの「削除」を押すことで削除することができます。
            また、観光スポットを選択した後に開始・終了駅または昼食・夕食の飲食店を変更すると、
            <font color="red">設定した観光スポットがリセットされる</font>ためご注意ください。<br><br>

            最後に「観光経路更新」を押すことで最終的な観光経路が表示されます。<br>

            <a href="#index">▲ページ上部に戻る</a>
        </p><br>
        </p>

        <p>
        <h3>マイページ</h3>
        <p>
            <b>パスワードの変更</b><br>
            &emsp;パスワードの変更を行います
        <ul>
            <li>旧パスワード:現在のパスワードを入力</li>
            <li>新パスワード:半角英数字をそれぞれ1種類以上含む6~15文字で入力</li>
            <li>新パスワード(確認):新パスワードを再入力</li>
        </ul>
        入力後、「変更」ボタンを押す<br>
        <a href="#index">▲ページ上部に戻る</a>
        </p>
        </p><br>

    </div>
</body>

</html>