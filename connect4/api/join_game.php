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

if ($game["player2"] !== null && $game["player2"] !== "") {
    echo json_encode(["success" => false, "message" => "Spelet är fullt"]);
    exit;
}

$updateSql = "UPDATE games SET player2 = 'Player 2', status = 'playing' WHERE id = $game_id";

if ($conn->query($updateSql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Du gick med i spelet"]);
} else {
    echo json_encode(["success" => false, "message" => "Något gick fel"]);
}
?>