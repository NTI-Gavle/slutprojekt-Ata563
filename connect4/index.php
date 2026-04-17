<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4 i rad</title>
</head>
<body>
    <h1>4 i rad</h1>

    <button id="createGameBtn">Skapa spel</button>

    <br><br>

    <input type="number" id="joinGameId" placeholder="Skriv Game ID">
    <button id="joinGameBtn">Gå med i spel</button>

    <p id="result"></p>

    <script>
        document.getElementById("createGameBtn").addEventListener("click", function () {
            fetch("api/create_game.php")
                .then(response => response.json())
                .then(data => {
                    window.location.href = "game.php?game_id=" + data.game_id;
                });
        });

        document.getElementById("joinGameBtn").addEventListener("click", function () {
            const gameId = document.getElementById("joinGameId").value;

            fetch("api/join_game.php?game_id=" + gameId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "game.php?game_id=" + gameId;
                    } else {
                        document.getElementById("result").innerText = data.message;
                    }
                });
        });
    </script>
</body>
</html>