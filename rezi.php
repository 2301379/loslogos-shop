<?php
 session_start();
 ?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホーム画面(ゲスト)</title>
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
       
        // 商品削除機能
        if (isset($_GET['remove_id'])) {
            $remove_id = filter_var($_GET['remove_id'], FILTER_SANITIZE_NUMBER_INT); // 入力値のサニタイズ
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['product_id'] == $remove_id) {
                    unset($_SESSION['cart'][$key]);
                    break;
                }
            }
            $_SESSION['cart'] = array_values($_SESSION['cart']); // インデックス再整理

            // 商品削除後リダイレクト
            header("Location: cart_view.php");
            exit;
        }

        $total = 0;

        if (empty($_SESSION['cart'])) {
            echo "<p>カートは空です。</p>";
        } else {
            echo "<h1>カートの中身</h1>";
            echo "<table border='1'>";
            echo "<thead>";
            echo "<tr><th>商品名</th><th>サイズ</th><th>価格</th><th>数量</th><th>小計</th><th>操作</th></tr>";
            echo "</thead>";
            echo "<tbody>";

            foreach ($_SESSION['cart'] as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;

                echo "<tr>";
                echo "<td>{$item['product_name']}</td>";
                echo "<td>{$item['size']}</td>";  // サイズ情報を表示
                echo "<td>{$item['price']}円</td>";
                echo "<td>{$item['quantity']}</td>";
                echo "<td>{$subtotal}円</td>";
                echo "<td><a href='?remove_id={$item['product_id']}'>削除</a></td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            echo "<h2>合計: {$total}円</h2>";
        }

        ?>

        <form action="confirm_purchase.php" method="post">
            <h1>購入者情報登録欄</h1>
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
            <button type="submit">購入する</button>
        </form>

    </div>
</body>

</html>