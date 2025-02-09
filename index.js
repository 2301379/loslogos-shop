// フェードアウト遷移の処理
function navigateWithFadeOut(url) {
    const body = document.body;

    // フェードアウトのクラスを追加
    body.classList.remove("fade-in");
    body.classList.add("fade-out");

    // 1.5秒後に画面を遷移
    setTimeout(() => {
        window.location.href = url;
    }, 1500);
}

// ランダムに画像を選んで背景を設定
function setRandomBackground() {
    const images = ['./css/back2.jpg', './css/back5.jpg'];  // 画像のパスを正しく指定
    const randomIndex = Math.floor(Math.random() * images.length);  // ランダムなインデックスを生成
    const selectedImage = images[randomIndex];  // ランダムに選ばれた画像

    // コンテナの背景画像に設定
    document.querySelector('.container').style.backgroundImage = `url('${selectedImage}')`;
}


// ページ読み込み時に背景を設定
window.onload = function () {
    setRandomBackground();
};

// 全リンクにフェードアウト処理を適用
document.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", (e) => {
        e.preventDefault();
        const url = e.currentTarget.href;
        navigateWithFadeOut(url);
    });
});
