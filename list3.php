<?php //日記一覧
include 'define.inc.php';
$dsn = 'mysql:dbname='.DBNAME.';host='.DBHOST.';port='.DBPORT;
try{
    $dbh = new PDO($dsn,DBUSER,DBPASS);
}
catch(PDOException $e){
    print('DBに接続できません'.$e->getMessage());
    exit();
}
//いらないpostのリセット
if(isset($_POST["contents_id"]) && ($_POST["contents_id"])){
    $_POST = array();
}
elseif(isset($_POST["delete_id"]) && ($_POST["delete_id"])){
    $_POST = array();
}

$output2 = '';  //最終的な結果を出力するための変数
$num1 = 0;      //limitに使う変数
$num2 = 10;

$sql = "SELECT COUNT(*) AS max FROM diary"; //最大レコード数
$stmt = $dbh->prepare($sql);
$stmt->execute();
$max_row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!isset($_POST) || (!$_POST)){    //ページに初めて入った時の処理
    //$output2 = display($dbh,$num1,$num2);
    //$output2 .= '<h1>デジタル日記帳</h1>';
    $output2 = display($dbh,$num1,$max_row);
}
elseif(isset($_POST) && ($_POST)){
    $change = $_POST["change"]; //戻る、進むのボタンの判別に使う
    $num1 = intval($_POST["num1"]);

    if($change == 1){       //戻るボタン
        if($num1 == 0){
            $num2 = $num1 + 10;
            $output2 = display($dbh,$num1,$max_row);
        }
        else{
            $num2 = $num1;
            $num1 -= 10;
            $output2 = display($dbh,$num1,$max_row);
        }
    }
    elseif($change == 2){       //進むボタン
        $num1 += 10;
        if($num1 > $max_row["max"]){
            $num1 -= 10;
            $num2 = $max_row["max"];
            $output2 = display($dbh,$num1,$max_row);
        }
        elseif($num1 + 10 > $max_row["max"]){
            $num2 = $max_row["max"];
            $output2 = display($dbh,$num1,$max_row);
        }
        else{
            $num2 = $num1 +  10;
            $output2 = display($dbh,$num1,$max_row);
        }
    }
    $POST = array();
}
//一覧表示
function display($dbh,$num1,$max_row){
    $sql = "SELECT id, title, dating, yobi FROM diary ORDER BY id ASC LIMIT :num1, 10";
    $stmt = $dbh->prepare($sql);
    $stmt->bindparam(':num1', $num1, PDO::PARAM_INT);//bindvalue(1, $num, PDO::PARAM_INT)
    $stmt->execute();

    $output = '';
    $output .= '<h1>-日記一覧-</h1>';
    $output .= '<p class="list_num">総日記数:'.$max_row["max"].'件</P>';
    $num3 = 0;

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $output .= '<div class="contents">';
        $output .= '<div class="info">';
        $output .= '<p class="title2">'.escTag($row["title"]).'</p>';
        $output .= '<p class="date2">'.$row["dating"].'</p>';
        $day_of_the_week = ['日','月','火','水','木','金','土'];
        $yobi = $day_of_the_week[$row["yobi"]-1];
        $output .= '<p class="yobi2"><'.escTag($yobi).'></p>';
        $output .= '</div>';
        $output .= '<form action="contents2.php" method="post">';
        $output .= '<p class="display">';
        $output .= '<input type="submit" value="表示">';
        $output .= '<input type="hidden" name="contents_row" value="'.$num1+$num3.'">';
        $output .= '</p>';
        $output .= '</form>';
        $output .= '<form action="delete.php" method="post">';
        $output .= '<p class="delete">';
        $output .= '<input type="submit" value="削除">';
        $output .= '<input type="hidden" name="delete_id" value="'.$row["id"].'">';
        $output .= '</p>';
        $output .= '</form>';
        $output .= '</div>';

        $num3 += 1;
    }
    return $output;
}

function escTag($row){
    return htmlspecialchars($row, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

if(!$output2){
    $output2 .= '<p>まだ何も投稿されていません</p>';
}
/*
<?php var_dump($_POST); ?>
<?php var_dump($output2); ?>*/
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>日記帳一覧</title>
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
    <main class="list3">
        <?php print($output2); ?>
        <div class="turn_the_page">
            <form action="list3.php" method="post">
                <div>
                    <input type="submit" value="<<戻る">
                    <input type="hidden" name="change" value="1">
                    <input type="hidden" name="num1" value="<?php echo $num1?>">
                </div>
            </form>
            <p><?php print(1+$num1.' ~ '.$num2) ?></p>
            <form action="list3.php" method="post">
                <div>
                    <input type="submit" value="次へ>>">
                    <input type="hidden" name="change" value="2">
                    <input type="hidden" name="num1" value="<?php echo $num1?>">
                </div>
            </form>
        </div>
    </main>
</body>
</html>