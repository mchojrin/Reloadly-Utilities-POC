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

die(var_dump($response->getBody()));
$response = $client->request('GET', 'billers', [
    'query' => [
        'countryISOCode' => $country,
    ]
]);
?>
<form method="post" action="select_vendor.php">
    <label for="country">Select your country</label>
    <select name="country" id="country">
        <?php
        foreach ($countries as $iso => $country): ?>
            <option value="<?php echo $iso; ?>"><?php echo $country; ?></option>
        <?php
        endforeach;
        ?>
    </select>
    <input type="submit" value="Select vendor">
</form>
</body>
</html>