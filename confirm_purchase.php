<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loslogos</title>
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
        <?php
        session_start();
        require_once('db_connection.php'); // DB接続

        // カートが空の場合、index.phpにリダイレクト
        if (empty($_SESSION['cart'])) {
            header("Location: index.php");
            exit;
        }

        // POSTデータの受け取り
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 必要なデータが送信されているか確認
            if (isset($_POST['name'], $_POST['postal_code'], $_POST['address'], $_POST['phone'], $_POST['email'])) {
                // 購入者情報を整理
                $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
                $postal_code = htmlspecialchars($_POST['postal_code'], ENT_QUOTES, 'UTF-8');
                $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
                $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
                $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');

                // 購入情報をデータベースに保存
                try {
                    // 購入者情報を注文テーブルに挿入
                    $stmt = $pdo->prepare("INSERT INTO orders (name, postal_code, address, phone, email) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $postal_code, $address, $phone, $email]);

                    // 注文IDを取得
                    $order_id = $pdo->lastInsertId();

                    // カート内の商品情報を注文商品テーブルに保存
                    foreach ($_SESSION['cart'] as $item) {
                        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, size) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$order_id, $item['product_id'], $item['product_name'], $item['price'], $item['quantity'], $item['size']]);
                    }

                    // セッションをクリアして完了ページにリダイレクト
                    unset($_SESSION['cart']);
                    header("Location: pay.php"); // 正しいリダイレクト先
                    exit;
                } catch (PDOException $e) {
                    echo "エラー: " . $e->getMessage();
                }
            } else {
                echo "購入者情報が正しく送信されていません。";
            }
        }
        ?>




    </div>

    <script src="./js/script.js"></script>
</body>

</html>