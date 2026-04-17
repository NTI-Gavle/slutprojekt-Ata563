<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spel - 4 i rad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        #board {
            display: grid;
            grid-template-columns: repeat(7, 60px);
            gap: 5px;
            justify-content: center;
            margin-top: 20px;
        }

        .cell {
            width: 60px;
            height: 60px;
            background-color: lightgray;
            border-radius: 50%;
            border: 1px solid black;
        }

        .player1 {
            background-color: red;
        }

        .player2 {
            background-color: gold;
        }
    </style>
</head>
<body>
    <h1>4 i rad</h1>
    <p id="status">Laddar spel...</p>
    <div id="board"></div>

    <script>
        const params = new URLSearchParams(window.location.search);
        const gameId = params.get("game_id");

        function loadGame() {
            fetch("api/get_game.php?game_id=" + gameId)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        document.getElementById("status").innerText = data.message;
                        return;
                    }

                    document.getElementById("status").innerText = "Nuvarande spelare: " + data.current_player;

                    const board = document.getElementById("board");
                    board.innerHTML = "";

                    for (let row = 0; row < data.board.length; row++) {
                        for (let col = 0; col < data.board[row].length; col++) {
                            const cell = document.createElement("div");
                            cell.classList.add("cell");

                            if (data.board[row][col] == 1) {
                                cell.classList.add("player1");
                            } else if (data.board[row][col] == 2) {
                                cell.classList.add("player2");
                            }

                            board.appendChild(cell);
                        }
                    }
                });
        }

        loadGame();
        setInterval(loadGame, 1000);
    </script>
</body>
</html>