<?php //日記を送信する場所
session_start();

$title = '';
$dating = '';
$yobi = '';
$sentence = '';
$errors = [];

if(isset($_SESSION["title"])){
    $title = $_SESSION["title"];
}
if(isset($_SESSION["dating"])){
    $dating = $_SESSION["dating"];
}
if(isset($_SESSION["yobi"])){
    $yobi = $_SESSION["yobi"];
}
if(isset($_SESSION["sentence"])){
    $sentence = $_SESSION["sentence"];
}
if(isset($_SESSION["image"])){
    $image = $_SESSION["image"];
}
if(isset($_SESSION["errors"])){
    $errors = $_SESSION["errors"];
}

function select_yobi($yobi,$value){
    if($yobi == $value){
        echo 'selected';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>日記を書く</title>
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
    <main>
        <?php if(count($errors) > 0){
                    foreach($errors as $error){
                        print('<p style="color:red;">・'.$error.'<p>');
                    }
                } ?>
        <form action="confirm.php" method="post" enctype="multipart/form-data">
            <ul>
                <li><label for="title">タイトル<small>:必須</small></label></li>
                <input type="text" name="title" id="title" placeholder="題名を入力" 
                value="<?php echo $title ?>">
            </ul>
            <ul>
                <li><label for="image">画像<small>:必須</small></label></li>
                <input type="file" name="image" id="image">
            </ul>
            <ul>
                <li><label for="dating">日付<small>:必須</small></label></li>
                <input type="date" name="dating" id="dating" value="<?php echo $dating ?>">
            </ul>
            <ul>
                <li><label for="yobi">曜日<small>:必須</small></label></li>
                <select name="yobi" id="yobi">
                    <option value="" disabled <?php select_yobi($yobi, ''); ?>>選択してください</option>
                    <option value="1" <?php select_yobi($yobi,1); ?>>日</option>
                    <option value="2" <?php select_yobi($yobi,2); ?>>月</option>
                    <option value="3" <?php select_yobi($yobi,3); ?>>火</option>
                    <option value="4" <?php select_yobi($yobi,4); ?>>水</option>
                    <option value="5" <?php select_yobi($yobi,5); ?>>木</option>
                    <option value="6" <?php select_yobi($yobi,6); ?>>金</option>
                    <option value="7" <?php select_yobi($yobi,7); ?>>土</option>
                </select>
            </ul>
            <ul>
                <li><label for="sentence">感想<small>:必須</small></label></li>
                <textarea name="sentence" id="sentence" placeholder="感想を入力"><?php echo trim($sentence); ?></textarea>
            </ul>
            <p><input type="submit" value="完成"></p>
        </form>
    </main>
</body>
</html>