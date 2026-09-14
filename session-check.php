<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Content-Type: application/json");

if (isset($_SESSION["admin"])) {
    echo json_encode(["ok" => true]);
} else {
    http_response_code(401);
    echo json_encode(["ok" => false, "redirect" => "/"]);
}
exit;
