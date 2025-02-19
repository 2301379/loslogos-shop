<?php
session_start();
require_once('db_connection.php'); // DB接続

// カートの中身を取得（セッション情報）
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// カートが空の場合はリダイレクト（オプション）
if (empty($cart_items)) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 購入者情報を受け取る
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $postal_code = htmlspecialchars($_POST['postal_code'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');

    // 購入者情報をデータベースに挿入
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (name, postal_code, address, phone, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $postal_code, $address, $phone, $email]);
        $order_id = $pdo->lastInsertId(); // 挿入した注文のID

        // カートの商品情報をorder_itemsテーブルに挿入
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, size) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['product_name'],
                $item['price'],
                $item['quantity'],
                $item['size'] // サイズ情報も挿入
            ]);
        }

        // 購入処理後、カートを空にする
        unset($_SESSION['cart']);

        // 支払いページにリダイレクト
        header("Location: payment.php");
        exit;
    } catch (PDOException $e) {
        echo "エラー: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>購入確認</title>
    <link rel="stylesheet" href="./css/syousai.css">
</head>

<body>
    <div class="container">
        <div class="top-bar">
            <a href="index.php">
                <span class="site-title">𝓛𝓸𝓼𝓵𝓸𝓰𝓸𝓼</span>
            </a>
            <a href="cart_view.php">
                <button class="cart-btn">🛒</button>
            </a>
        </div>

        <!-- 商品リストを表示 -->
        <div class="cart-items">
            <h2>カートの中身</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>商品名</th>
                        <th>価格</th>
                        <th>数量</th>
                        <th>サイズ</th>
                        <th>小計</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($cart_items as $item) {
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                        echo "<tr>";
                        echo "<td>{$item['product_name']}</td>";
                        echo "<td>{$item['price']}円</td>";
                        echo "<td>{$item['quantity']}</td>";
                        echo "<td>{$item['size']}</td>";
                        echo "<td>{$subtotal}円</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <h3>合計: <?php echo $total; ?>円</h3>
        </div>

        <!-- 購入者情報入力フォーム -->
        <form action="confirm_purchase.php" method="post">
            <div class="info">
                <div class="info-row">
                    <label for="name">名前：</label>
                    <input type="text" id="name" name="name" placeholder="名前" required>
                </div>
                <div class="info-row">
                    <label for="postal-code">郵便番号：</label>
                    <input type="text" id="postal-code" name="postal_code" placeholder="郵便番号" required>
                </div>
                <div class="info-row">
                    <label for="address">住所：</label>
                    <input type="text" id="address" name="address" placeholder="住所" required>
                </div>
                <div class="info-row">
                    <label for="phone">電話番号：</label>
                    <input type="text" id="phone" name="phone" placeholder="電話番号" required>
                </div>
                <div class="info-row">
                    <label for="email">メールアドレス：</label>
                    <input type="email" id="email" name="email" placeholder="メールアドレス" required>
                </div>
            </div>
            <button type="submit">購入を確定する</button>
        </form>

    </div>

    <script src="./js/script.js"></script>
</body>

</html>