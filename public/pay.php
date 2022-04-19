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

require_once '../header.php';

try {
    $body = [
        'subscriberAccountNumber' => $_POST['account_number'],
        'amount' => $_POST['amount'],
        'billerId' => $_POST['biller_id'],
        'useLocalAmount' => false,
    ];

    $headers = [
        'Accept' => 'application/com.reloadly.utilities-v1+json',
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json',
    ];
    $response = $client->request('POST', 'https://utilities-sandbox.reloadly.com/pay', [
        'headers' => $headers,
        'body' => json_encode($body)
    ]);

?>
        <h2>Server Response: </h2>
    <div>
    <?php echo json_decode($response->getBody())->message; ?>
    </div><?php
} catch (\GuzzleHttp\Exception\ClientException $exception) {
    die ('POST failed: <strong>'.$exception->getMessage().'</strong>. Headers: <pre>'.var_dump($headers).'</pre>. Body: <pre>'.var_dump($body).'</pre>.');
}

require_once '../footer.php';
?>