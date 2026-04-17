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

    <p id="result"></p>

    <script>
        document.getElementById("createGameBtn").addEventListener("click", function () {
            fetch("api/create_game.php")
                .then(response => response.json())
                .then(data => {
                    document.getElementById("result").innerText = "Game ID: " + data.game_id;
                })
                .catch(error => {
                    document.getElementById("result").innerText = "Något gick fel.";
                });
        });
    </script>
</body>
</html>