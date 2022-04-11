<?php

session_start();
$token ??= $_SESSION['token'];

if (empty($token)) {
    header('Location: index.php');

    die;
}

if (empty($_POST['account_number']) || empty($_POST['biller_id']) || empty($_POST['amount'])) {
    http_response_code(400);

    die('Missing parameters');
}

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client([
    'timeout' => 2.0,
]);

try {
    $response = $client->request('POST', 'https://utilities.reloadly.com/pay', [
        'headers' => [
            'Accept' => 'application/com.reloadly.utilities-v1+json',
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode([
            'subscriberAccountNumber' => $_POST['account_number'],
            'amount' => $_POST['amount'],
            'billerId' => $_POST['biller_id'],
            'useLocalAmount' => false,
        ])
    ]);

    echo json_decode($response->getBody())->message;
} catch (\GuzzleHttp\Exception\ClientException $exception) {
    die ($exception->getMessage());
}