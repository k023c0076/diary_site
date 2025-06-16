<?php //送信した日記の確認
session_start();

if(isset($_POST) && ($_POST)){
    $title = $_POST["title"];
    $dating = $_POST["dating"];
    $yobi = $_POST["yobi"];
    $sentence = $_POST["sentence"];

    $errors = [];
    //画像のチェック
    if((isset($_FILES)) && ($_FILES["image"]) && ($_FILES["image"]["name"])){
        $image = $_FILES["image"];
    }
    else{
        $image = '';
    }
    if($image){
        $allowMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];
        $mimeType = mime_content_type($image['tmp_name']);
        if(!in_array($mimeType, $allowMimeTypes)){
            $errors["invalidFile"] = '不正なファイル形式です';
        }
        else{
            $filename = uniqid().'.'.pathinfo($image['name'],PATHINFO_EXTENSION);
            if(!move_uploaded_file($image['tmp_name'],'upload/'.$filename)){
                $errors["savefault"] = 'ファイルの保存に失敗しました';
            }
        }
    }
    //セッションに代入
    $_SESSION["title"] = $title;
    $_SESSION["dating"] = $dating;
    $_SESSION["yobi"] = $yobi;
    $_SESSION["sentence"] = $sentence;
    if($image){
        $_SESSION["image"] = $filename;
    }
    //タイトル、日付、曜日、感想のチェック
    if(strlen($title) < 1 || strlen($title) > 64){
        $errors["title"] = 'タイトルの入力が正しくありません!';
    }
    if(!$image){
        $errors["image"] = "画像が選択されていません!";
    }
    if(!$dating){
        $errors["dating"] = '日付が選択されていません!';
    }
    if(!$yobi){
        $errors["yobi"] = "曜日が選択されていません!";
    }
    if(strlen($sentence) < 1){
        $errors["sentence"] = "感想の入力が正しくありません!";
    }
    if(count($errors) > 0){
        $_SESSION["errors"] = $errors;
        header("Location:write.php");
        exit();
    }
    //日付の値を文字列に変換
    $day_of_the_week = ['日','月','火','水','木','金','土'];
    $yobi = $day_of_the_week[$yobi-1];
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>日記を記録</title>
    <link href="css/normalize.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <main>
        <div class="page">
            <p class="title1"><?php print(htmlspecialchars($title, ENT_QUOTES)); ?></p>
            <img src="upload/<?php print($filename); ?>" alt="" class=image1>
            <p class="date1"><?php print($dating.' <'.$yobi.'>'); ?></p>
            <p class="sentence1"><?php print(nl2br(htmlspecialchars(trim($sentence), ENT_QUOTES))); ?></p>
            <form action="complete.php" method="post">
                <input type="submit" value="記録">
            </form>
        </div>
    </main>
</body>
</html>