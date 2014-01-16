<?php
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$file = __DIR__ . DIRECTORY_SEPARATOR . trim($path, '/') . DIRECTORY_SEPARATOR . 'response.json';
$json = file_get_contents($file);
$data = json_decode($json, TRUE);

$data['_debug']['server'] = $_SERVER;
$data['_debug']['post'] = $_POST;
$data['_debug']['get'] = $_GET;
$data['_debug']['request'] = $_REQUEST;
$data['_debug']['raw_post'] = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : NULL;
$data['_debug']['raw_put'] = $_SERVER['REQUEST_METHOD'] === "PUT" ? file_get_contents('php://input') : array();
echo json_encode($data);
