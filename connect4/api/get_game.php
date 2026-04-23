<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . "/../config/db.php";

if (!isset($_GET["game_id"])) {
    echo json_encode(["success" => false, "message" => "Game ID saknas"]);
    exit;
}

$game_id = (int)$_GET["game_id"];

$sql = "SELECT * FROM games WHERE id = $game_id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Spelet finns inte"]);
    exit;
}

$game = $result->fetch_assoc();

echo json_encode([
    "success" => true,
    "id" => $game["id"],
    "board" => json_decode($game["board"]),
    "current_player" => (int)$game["current_player"],
    "winner" => $game["winner"] !== null ? (int)$game["winner"] : null,
    "status" => $game["status"],
    "player1" => $game["player1"],
    "player2" => $game["player2"],
    "is_ai" => isset($game["is_ai"]) ? (int)$game["is_ai"] : 0
]);
?>