<?php
// 環境フラグ
$is_local = ($_SERVER['SERVER_NAME'] === 'localhost');

if ($is_local) {
    // ローカル環境用接続設定
    $dsn = "mysql:host=localhost;dbname=loslogos;charset=utf8mb4";
    $username = "root";
    $password = "";
} else {
    // ロリポップ環境用接続設定
    $dsn = "mysql:host=mysql313.phy.lolipop.lan;dbname=LAA1557214-loslogos;charset=utf8mb4";
    $username = "LAA1557214";
    $password = "kurato331";
}

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
} catch (PDOException $e) {
    echo 'データベース接続エラー: ' . $e->getMessage();
}
?>
