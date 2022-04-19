<?php

session_start();
require_once '../header.php';
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
                "audience" => 'https://utilities-sandbox.reloadly.com',
            ]
        ),
    ]);

$token = json_decode($response->getBody())->access_token;
$_SESSION['token'] = $token;

$response = $client->request('GET', 'https://utilities-sandbox.reloadly.com/billers', [
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
    <h2>Enter payment details</h2>
    <div>
        <label for="biller">Biller</label>
        <select name="biller_id" id="biller" class="form-control">
            <?php
            foreach ($billers as $biller): ?>
                <option value="<?php echo $biller->id; ?>"><?php echo $biller->name; ?></option>
            <?php
            endforeach;
            ?>
        </select>
    </div>
    <div>
        <label for="account_number">Account Number:</label>
        <input type="text" name="account_number" id="account_number" placeholder="Enter your account number" class="form-control"/>
    </div>
    <div>
        <label for="amount">Amount:</label>
        <input type="number" min="1" name="amount" id="amount" class="form-control"/>
    </div>
    <div>
        <input type="submit" value="Pay" class="btn btn-lg btn-primary btn-block">
    </div>
</form>
<?php

require_once '../footer.php';