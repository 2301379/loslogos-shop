<?php
session_start();
require_once('db_connection.php'); // DB接続

// POSTデータの受け取り
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 必要なデータが送信されているか確認
    if (isset($_POST['product_id'], $_POST['product_name'], $_POST['price'], $_POST['quantity'])) {
        // 商品情報を整理
        $product_id = (int) $_POST['product_id'];
        $product_name = htmlspecialchars($_POST['product_name']);
        $price = (int) $_POST['price'];
        $quantity = (int) $_POST['quantity'];

        // データベースに情報を保存
        try {
            // 商品情報をカートテーブルに挿入
            $stmt = $pdo->prepare("INSERT INTO cart (product_id, product_name, price, quantity, user_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$product_id, $product_name, $price, $quantity, 1]);
            
            
            // カートが空の場合は初期化
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // 既存のカート商品を確認（同じ商品があれば数量を更新）
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['product_id'] === $product_id) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }

            // カートに新しい商品を追加
            if (!$found) {
                $_SESSION['cart'][] = [
                    'product_id' => $product_id,
                    'product_name' => $product_name,
                    'price' => $price,
                    'quantity' => $quantity
                ];
            }

            // カートページにリダイレクト
            header("Location: cart_view.php");
            exit;
        } catch (PDOException $e) {
            echo 'データベース接続エラー: ' . $e->getMessage();
        }
    } else {
        echo "商品情報が正しく送信されていません。";
    }
}
?>
