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

$output2 = '';  //最終的な結果を出力するための変数
$cont_row = ''; //レコードの番号、idとは違う
$change = '';

$sql = "SELECT COUNT(*) AS max FROM diary"; //最大レコード数
$stmt = $dbh->prepare($sql);
$stmt->execute();
$max_row = $stmt->fetch(PDO::FETCH_ASSOC);

if(isset($_POST["contents_row"])){  //ページに最初に飛んできた時の処理
    $cont_row = $_POST["contents_row"];
    $output2 .= display($dbh,$cont_row);
}
elseif(isset($_POST) && ($_POST)){
    $change = $_POST["change"];
    $cont_row = intval($_POST["cont_row"]);

    if($change == 1){   //戻るボタン
        if($cont_row == 0){
            $output2 .= display($dbh,$cont_row);
        }
        else{
            $cont_row -= 1;
            $output2 .= display($dbh,$cont_row);
        }
    }
    elseif($change == 2){   //進むボタン
        if($cont_row == $max_row["max"] - 1){
            $output2 .= display($dbh,$cont_row);
        }
        else{
            $cont_row += 1;
            $output2 .= display($dbh,$cont_row);
        }
    }
}


function display($dbh,$cont_row){
    $sql = "SELECT * FROM diary ORDER BY id ASC LIMIT :cont_row, 1";
    $stmt = $dbh->prepare($sql);
    $stmt->bindparam(':cont_row', $cont_row, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $output = '';
    $output .= '<div class="page">';
    $output .= '<p class="title1">'.escTag($row["title"]).'</p>';
    $output .= '<img src="upload/'.escTag($row["imagefile"]).'" alt="" class=image1>';
    $day_of_the_week = ['日','月','火','水','木','金','土'];
    $yobi = $day_of_the_week[$row["yobi"]-1];
    $output .= '<p class="date1">'.$row["dating"].' '.$yobi.'</p>';
    $output .= '<p class="sentence1">'.nl2br(escTag($row["sentence"])).'</p>';
    $output .= '</div>';

    return $output;
}

function escTag($row){
    return htmlspecialchars($row, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>日記を見る</title>
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
    <main class="contents2">
        <?php print($output2); ?>
    </main>
    <div class="turn_the_page">
        <form action="contents2.php" method="post">
            <div>
                <input type="submit" value="戻る">
                <input type="hidden" name="change" value="1">
                <input type="hidden" name="cont_row" value="<?php echo $cont_row ?>">
            </div>
        </form>
        <p><?php print(1+$cont_row.'/'.$max_row["max"]); ?></p>
        <form action="contents2.php" method="post">
            <div>
                <input type="submit" value="次へ">
                <input type="hidden" name="change" value="2">
                <input type="hidden" name="cont_row" value="<?php echo $cont_row ?>">
            </div>
        </form>
    </div>
</body>
</html>