const params = new URLSearchParams(window.location.search);
const gameId = params.get("game_id");
const player = parseInt(params.get("player"));

let winnerShown = false;
let previousBoard = null;

function findNewPiece(oldBoard, newBoard) {
    if (!oldBoard) return null;

    for (let row = 0; row < newBoard.length; row++) {
        for (let col = 0; col < newBoard[row].length; col++) {
            if (oldBoard[row][col] !== newBoard[row][col] && newBoard[row][col] !== 0) {
                return { row, col };
            }
        }
    }

    return null;
}

function makeMove(col) {
    fetch("api/make_move.php?game_id=" + gameId + "&col=" + col + "&player=" + player)
        .then(response => response.json())
        .then(() => {
            loadGame();
        });
}

function launchConfetti() {
    const confetti = document.getElementById("confetti");
    confetti.innerHTML = "";

    for (let i = 0; i < 120; i++) {
        const piece = document.createElement("div");
        piece.classList.add("confetti-piece");

        const colors = ["#6dff8b", "#8b5cf6", "#ffffff", "#d4b26a", "#59dfff"];
        piece.style.left = Math.random() * 100 + "%";
        piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        piece.style.animationDelay = (Math.random() * 0.8) + "s";

        confetti.appendChild(piece);
    }

    setTimeout(() => {
        confetti.innerHTML = "";
    }, 4000);
}

function showWinnerPopup(title, text) {
    document.getElementById("winnerTitle").innerText = title;
    document.getElementById("winnerText").innerText = text;
    document.getElementById("winnerPopup").classList.add("show");
}

function hideWinnerPopup() {
    document.getElementById("winnerPopup").classList.remove("show");
}

function resetGame() {
    fetch("api/reset_game.php?game_id=" + gameId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                winnerShown = false;
                previousBoard = null;
                hideWinnerPopup();
                loadGame();
            }
        });
}

function goToMenu() {
    window.location.href = "index.php";
}

document.getElementById("playAgainBtn").addEventListener("click", resetGame);
document.getElementById("closePopupBtn").addEventListener("click", goToMenu);

function loadGame() {
    fetch("api/get_game.php?game_id=" + gameId)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                document.getElementById("status").innerText = data.message;
                return;
            }

            let statusText = "";

            if (data.winner !== null) {
                if (data.winner == player) {
                    statusText = "Du vann!";
                    showWinnerPopup("Victory", "Du vann!");
                } else {
                    statusText = "Du förlorade!";
                    showWinnerPopup("Defeat", "Spelare " + data.winner + " vann!");
                }

                if (!winnerShown) {
                    launchConfetti();
                    winnerShown = true;
                }
            } else {
                hideWinnerPopup();

                if (data.is_ai == 1) {
                    if (data.current_player == player) {
                        statusText = "Din tur mot AI";
                    } else {
                        statusText = "AI tänker...";
                    }
                } else {
                    if (data.current_player == player) {
                        statusText = "Det är din tur";
                    } else {
                        statusText = "Vänta på den andra spelaren";
                    }

                    if (data.player2 && data.player2 !== "") {
                        statusText += " | 2 spelare är med";
                    } else {
                        statusText += " | Väntar på spelare 2";
                    }
                }
            }

            document.getElementById("status").innerText = statusText;

            const board = document.getElementById("board");
            board.innerHTML = "";

            const newPiece = findNewPiece(previousBoard, data.board);

            for (let row = 0; row < data.board.length; row++) {
                for (let col = 0; col < data.board[row].length; col++) {
                    const cell = document.createElement("div");
                    cell.classList.add("cell");

                    if (data.board[row][col] == 1) {
                        cell.classList.add("player1");
                    } else if (data.board[row][col] == 2) {
                        cell.classList.add("player2");
                    }

                    if (newPiece && newPiece.row === row && newPiece.col === col) {
                        cell.classList.add("falling");
                    }

                    cell.addEventListener("click", function () {
                        if (data.winner === null && data.current_player == player) {
                            if (data.is_ai == 1 || (data.player2 && data.player2 !== "")) {
                                makeMove(col);
                            }
                        }
                    });

                    board.appendChild(cell);
                }
            }

            previousBoard = data.board.map(row => [...row]);
        })
        .catch(() => {
            document.getElementById("status").innerText = "Kunde inte ladda spelet.";
        });
}

hideWinnerPopup();
loadGame();
setInterval(loadGame, 1000);