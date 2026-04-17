<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . "/../config/db.php";

$board = json_encode([
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0],
    [0,0,0,0,0,0,0]
]);

$sql = "INSERT INTO games (board, current_player) VALUES ('$board', 1)";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["game_id" => $conn->insert_id]);
} else {
    echo "Error: " . $conn->error;
}
?>