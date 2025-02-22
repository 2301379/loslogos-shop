<?php
session_start();
require_once('db_connection.php'); // DB接続
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カート</title>
    <link rel="stylesheet" href="./css/cart.css">
</head>

<body>
    <div class="container">
        <div class="top-bar">
            <a href="index.php">
                <span class="site-title">𝓛𝓸𝓼𝓵𝓸𝓰𝓸𝓼</span>
            </a>
        </div>

        <?php
        

        // 商品削除機能（セッション & データベースから削除）
        if (isset($_GET['remove_id'])) {
            $remove_id = filter_var($_GET['remove_id'], FILTER_SANITIZE_NUMBER_INT); // 入力値のサニタイズ

            // セッションから該当商品を削除
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['product_id'] == $remove_id) {
                    unset($_SESSION['cart'][$key]);
                    break;
                }
            }
            $_SESSION['cart'] = array_values($_SESSION['cart']); // インデックス再整理

            // データベースから削除
            try {
                $stmt = $pdo->prepare("DELETE FROM cart WHERE product_id = ?");
                $stmt->execute([$remove_id]);
            } catch (PDOException $e) {
                echo "エラー: " . $e->getMessage();
            }

            // 商品削除後リダイレクト
            header("Location: cart_view.php");
            exit;
        }

        // カートの中身を取得
        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            echo "<h1>カートの中身</h1>";
            echo "<table border='1'>";
            echo "<thead><tr><th>商品名</th><th>サイズ</th><th>価格</th><th>数量</th><th>小計</th><th>操作</th></tr></thead>";
            echo "<tbody>";

            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
                echo "<tr>";
                echo "<td>{$item['product_name']}</td>";
                echo "<td>{$item['size']}</td>"; // サイズを表示
                echo "<td>{$item['price']}円</td>";
                echo "<td>{$item['quantity']}</td>";
                echo "<td>{$subtotal}円</td>";
                echo "<td><a href='?remove_id={$item['product_id']}'>削除</a></td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            echo "<h2>合計: {$total}円</h2>";
        } else {
            echo "<p>カートは空です。</p>";
        }
        ?>

        <div class="back-button-container">
            <a href="index.php">
                <button class="back-button">ホームへ戻る</button>
            </a>
            <a href="rezi.php">
                <button class="back-button">レジに進む</button>
            </a>
        </div>
    </div>
</body>

</html>
