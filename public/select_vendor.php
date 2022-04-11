<?php

session_start();
?>
<html>
<head>
    <title>Reloadly's Utilities Payment demo Application</title>
</head>
<body>
<?php
if ('post' !== strtolower($_SERVER['REQUEST_METHOD'])) {
    http_response_code(400);
    die('This page can only be accessed via post');
}

$country = $_POST['country'] ?? '';
$countries = require_once __DIR__ . '/../config/countries.php';

if (!array_key_exists($country, $countries)) {
    http_response_code(400);
    die($country . ' is not available at the moment');
}

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client([
    'timeout' => 2.0,
]);
$auth = require_once __DIR__ . '/../config/auth.php';

$response = $client->post('https://auth.reloadly.com/oauth/token',
    [
        'headers' =>
            [
                'Content-Type' => 'application/json',
            ],
        'body' => json_encode(
            [
                "client_id" => $auth['client_id'],
                "client_secret" => $auth['secret'],
                "grant_type" => 'client_credentials',
                "audience" => 'https://utilities.reloadly.com',
            ]
        ),
    ]);

$token = json_decode($response->getBody())->access_token;
$_SESSION['token'] = $token;

$response = $client->request('GET', 'https://utilities.reloadly.com/billers', [
    'query' => [
        'countryISOCode' => $country,
    ],
    'headers' => [
        'Accept' => 'application/com.reloadly.utilities-v1+json',
        'Authorization' => 'Bearer ' . $token,
    ]
]);

$billers = json_decode($response->getBody())->content;
?>
<form method="post" action="pay.php">
    <label for="biller">Select your biller</label>
    <select name="biller_id" id="biller">
        <?php
        foreach ($billers as $biller): ?>
            <option value="<?php echo $biller->id; ?>"><?php echo $biller->name; ?></option>
        <?php
        endforeach;
        ?>
    </select>
    <label for="account_number">Account Number:</label>
    <input type="text" name="account_number" id="account_number" placeholder="Enter your account number"/>
    <label for="amount">Amount:</label>
    <input type="number" min="1" name="amount" id="amount"/>
    <input type="submit" value="Pay">
</form>
</body>
</html>