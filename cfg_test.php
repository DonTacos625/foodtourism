<?php

/*
$dsn = 'pgsql:dbname=mydb; host=127.0.0.1';
$username= 'postgres';
$password= 'fadsh80fejhj@';
$pdo = new PDO($dsn, $username, $password);


$db["host"] = '127.0.0.1';
$db["user"] = 'postgres';
$db["pass"] = 'fadsh80fejhj@';
$db["dbname"] = 'mydb';
*/

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



/*
$data_num = 8;
$reco_num = 10;
$spot_num = 173;
*/

//Data1:満足度
//Data2:人ごみ
//Data3:バリアフリー
//Data4:コストパフォーマンス
//Data5:雰囲気
//Data6:快適度/サービスの良さ
//Data7:おすすめ度

//Category
//1:名所・史跡
//2:飲食
//3:ショッピング
//4:芸術・博物館
//5:テーマパーク・公園
//6:その他
?>