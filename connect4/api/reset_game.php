<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . "/../config/db.php";

if (!isset($_GET["game_id"])) {
    echo json_encode(["success" => false, "message" => "Game ID saknas"]);
    exit;
}

$game_id = (int)$_GET["game_id"];

$board = json_encode([
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0]
]);

$sql = "UPDATE games 
        SET board = '$board', current_player = 1, winner = NULL, status = 'playing', ai_move_at = NULL
        WHERE id = $game_id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Något gick fel"]);
}
?>