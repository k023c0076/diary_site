<?php //日記の削除
include "define.inc.php";
$dsn = 'mysql:dbname='.DBNAME.';host='.DBHOST.';port='.DBPORT;
try{
    $dbh = new PDO($dsn,DBUSER,DBPASS);
}
catch(PDOException $e){
    print('DBに接続できません'.$e->getMessage());
    exit();
}

$delete_id = '';

if(isset($_POST) && ($_POST)){
    $delete_id = $_POST["delete_id"];

    $sql = "DELETE FROM diary WHERE id = :delete_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindparam(':delete_id',$delete_id,PDO::PARAM_INT);
    $stmt->execute();

    $_POST = array();
    header("Location:list3.php");
}
?>
