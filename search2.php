<?php 
include 'define.inc.php';
$dsn = 'mysql:dbname='.DBNAME.';host='.DBHOST.';port='.DBPORT;
try{
    $dbh = new PDO($dsn,DBUSER,DBPASS);
}
catch(PDOException $e){
    print('DBに接続できません'.$e->getMessage());
    exit();
}

$keyword = ''; //検索キーワード
$output = ''; //最終的な出力に

if(isset($_POST) && ($_POST)){
    $keyword = $_POST["keyword"];
    if($keyword == ''){
        $output = '<p>キーワードに一致する日記のタイトルがありませんでした</p>';
    }
    else{
        $Keyword = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $keyword);
        $keyword = '%'.$keyword.'%';

        $sql0 = "SELECT COUNT(*) AS hit FROM diary WHERE title LIKE :keyword";//ヒットした件数を数える
        $stmt0 = $dbh->prepare($sql0);
        $stmt0->bindparam(':keyword',$keyword,PDO::PARAM_STR);
        $stmt0->execute();

        $hit = $stmt0->fetch(PDO::FETCH_ASSOC);
        $output .= '<p class="list_num">キーワードの該当レコード件数は'.$hit["hit"].'件です</p>';

        $sql1 = "SELECT id, title, dating, yobi FROM diary WHERE title LIKE :keyword";//データを探す
        $stmt1 = $dbh->prepare($sql1);
        $stmt1->bindparam(':keyword',$keyword,PDO::PARAM_STR);
        $stmt1->execute();

        $sql2 = "SELECT id FROM diary";//何行目かを探すために使う
        $stmt2 = $dbh->prepare($sql2);
        $stmt2->execute();

        while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){
            $num1 = 0;
            while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){ //データベースで何行目かを探す
                if($row1["id"] == $row2["id"]){
                    break;
                }
                else{
                    $num1 += 1;
                }
            }
            $output .= '<div class="contents">';//ヒットしたものを表示
            $output .= '<div class="info">';
            $output .= '<p class="title2"><nobr>'.escTag($row1["title"]).'</nobr></p>';
            $output .= '<p class="date2">'.escTag($row1["dating"]).'</p>';
            $day_of_the_week = ['日','月','火','水','木','金','土'];
            $yobi = $day_of_the_week[$row1["yobi"]-1];
            $output .= '<p class="yobi2"><'.escTag($yobi).'></p>';
            $output .= '</div>';
            $output .= '<form action="contents2.php" method="post">';
            $output .= '<p>';
            $output .= '<input type="submit" value="表示">';
            $output .= '<input type="hidden" name="contents_row" value="'.$num1.'">';
            $output .= '</p>';
            $output .= '</form>';
            $output .= '<form action="delete.php" method="post">';
            $output .= '<p>';
            $output .= '<input type="submit" value="削除">';
            $output .= '<input type="hidden" name="delete_id" value="'.$row1["id"].'">';
            $output .= '</p>';
            $output .= '</form>';
            $output .= '</div>';
        }

        if(!$output){
            $output = '<p>キーワードに一致する日記のタイトルがありませんでした</p>';
        }
    }
}

function escTag($row1){
    return htmlspecialchars($row1, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>日記検索</title>
    <link href="css/normalize.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <header class="head">
        <h1>デジタル日記帳</h1>
        <nav class="bottan">
            <ul>
                <li><a href="index.php">表紙</a></li>
                <li><a href="write.php">日記を付ける</a></li>
                <li><a href="list3.php">日記を見る</a></li>
                <li><a href="search2.php">日記を探す</a></li>
            </ul>
        </nav>
    </header>
    <div>
        <form action="search2.php" method="post" class="search">
            <p>検索欄:</p>
            <input type="text" name="keyword" placeholder="キーワードを入力" 
            value="<?php echo $Keyword = str_replace(['%'], [''], $keyword); ?>">
            <input type="submit" value="検索">
        </form>
    </div>
    <main class="search1">
        <?php print($output); ?>
    </main>
</body>
</html>