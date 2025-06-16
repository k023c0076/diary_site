<?php //送信した日記をデータベースに登録
session_start();
include 'define.inc.php';

//データベースに接続する
$dsn = 'mysql:dbname='.DBNAME.';host='.DBHOST.';port='.DBPORT;
try{
    $dbh = new PDO($dsn,DBUSER,DBPASS);
}
catch(PDOException $e){
    print('DBに接続できません'.$e->getMessage());
    exit();
}

//セッションが無い場合、送信ページに戻る
if(count($_SESSION) < 1){
    header('Location:write.php');
    exit();
}

//データをセッションから取り出しデータベースに登録する
$title = $_SESSION["title"];
$image = $_SESSION["image"];
$dating = $_SESSION["dating"];
$yobi = $_SESSION["yobi"];
$sentence = $_SESSION["sentence"];

$sql = "INSERT INTO diary VALUES (NULL, ?, ?, ?, ?, ?)";
$stmt = $dbh->prepare($sql);
$stmt->execute(array($title, $image, $dating, $yobi, $sentence));

//セッションを消去し送信ページに戻る
session_destroy();
header("Location:write.php");
?>