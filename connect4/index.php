<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4 i rad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="menu-card text-center">
            <h1>4 i rad</h1>
            <p id="result" class="mb-4">Välj spelläge.</p>

            <button id="createGameBtn" class="game-btn">Spela mot spelare</button>
            <button id="createAiGameBtn" class="game-btn">Spela mot AI</button>

            <div class="mt-4">
                <input type="number" id="joinGameId" class="form-control menu-input" placeholder="Skriv Game ID">
                <button id="joinGameBtn" class="game-btn">Gå med i spel</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("createGameBtn").addEventListener("click", function () {
            fetch("api/create_game.php")
                .then(response => response.json())
                .then(data => {
                    window.location.href = "game.php?game_id=" + data.game_id + "&player=1";
                });
        });

        document.getElementById("createAiGameBtn").addEventListener("click", function () {
            fetch("api/create_game.php?ai=1")
                .then(response => response.json())
                .then(data => {
                    window.location.href = "game.php?game_id=" + data.game_id + "&player=1";
                });
        });

        document.getElementById("joinGameBtn").addEventListener("click", function () {
            const gameId = document.getElementById("joinGameId").value;

            fetch("api/join_game.php?game_id=" + gameId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "game.php?game_id=" + gameId + "&player=2";
                    } else {
                        document.getElementById("result").innerText = data.message;
                    }
                });
        });
    </script>
</body>
</html>