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
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        
        gtag('config', 'UA-131239045-1');
        </script>

        <title>ホーム</title>

        <style>
            #homebox{
                width: 50vw;
                float: left;
                margin-left: 5px;
            }

            #homebox h2{
                margin: 0px;
            }

            #homebox h3{
                border-left: 5px solid #000080;
                margin: 0px;
            }

            @media screen and (min-width:769px) and (max-width:1366px){
                h2{
                    font-size: 20px;
                }

                h3{
                    font-size: 18px;
                }
            }
            
            @media screen and (max-width:768px){
                h2{
                    font-size: 19px;
                }

                h3{
                    font-size: 17px;
                }

                #homebox{
                    width: auto;
                    margin: 0px;
                }
            }
        </style>
    </head>

    <body>
        <div id="homebox">
            <h2>ホーム</h2>

            <h2><font color=#000080>&emsp;試験運用中</font></h2>

            <h3>目的</h3>
            <p>
                飲食を主な目的とした観光計画の作成を支援することを目的としたシステムです。
            </p><br>

            <h3>更新履歴</h3>
            <p>
                2021/12/ 試験運用開始
            </p><br>

            <h3>使い方</h3>
            <p>
                ページ上部の観光計画作成から情報を登録することで観光計画を作成することが可能です。<br>
                <br>
                地図上のアイコン(新規スポット投稿では地図上)を押すことでポップアップが表示されます。<br>
                詳細は<a href="explain.php">こちら</a><br>
                <font color=#000080><big>利用後、アンケートへのご回答をお願いします。</big>></font>
            </p><br>

            <h3>連絡先</h3>
            <p>
                不具合等ございましたら、下記のメールアドレスまでご連絡下さい。<br>
                作成者:平野<br>
                mhirano_0730[アットマーク]yahoo.co.jp<br>
                ※[アットマーク]を@へ置換して下さい。<br>
            </p>
        <div>
    </body>
</html>