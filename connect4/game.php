<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spel - 4 i rad</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=3">
</head>
<body>

    <div class="container text-center mt-4">
        <h1>4 i rad</h1>
        <p id="status">Laddar spel...</p>
        <div id="board"></div>
    </div>

    <div id="confetti"></div>

    <div id="winnerPopup" class="winner-popup">
        <div class="winner-box">
            <div class="winner-ring"></div>
            <div class="winner-title" id="winnerTitle">Victory</div>
            <div id="winnerText" class="winner-text">Spelare 1 vann!</div>
            <div class="popup-btns popup-btns-row">
                <button id="playAgainBtn" class="popup-small-btn">Spela igen</button>
                <button id="closePopupBtn" class="popup-small-btn">Stäng</button>
            </div>
        </div>
    </div>

    <script src="assets/js/game.js?v=3"></script>
</body>
</html>