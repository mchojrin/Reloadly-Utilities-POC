<html>
<head>
    <title>Reloadly's Utilities Payment demo Application</title>
</head>
<body>
<?php
$countries = require_once __DIR__ . '/../config/countries.php';
?>
<form method="post" action="select_vendor.php">
    <label for="country">Select your country</label>
    <select name="country" id="country">
        <?php
        foreach ($countries as $iso => $country): ?>
        <option value="<?php echo $iso;?>"><?php echo $country;?></option>
        <?php
        endforeach;
        ?>
    </select>
    <input type="submit" value="Select vendor">
</form>
</body>
</html>