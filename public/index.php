<?php
require_once '../header.php';

$countries = require_once __DIR__ . '/../config/countries.php';
?>
    <div>
        <h2 class="form-signin-heading">Select your country</h2>
        <form method="post" action="select_vendor.php">
            <div>
                <select name="country" id="country" class="form-control">
                    <?php
                    foreach ($countries as $iso => $country): ?>
                        <option value="<?php echo $iso;?>"><?php echo $country;?></option>
                    <?php
                    endforeach;
                    ?>
                </select>
            </div>
            <div>
                <input type="submit" value="This is my country" class="btn btn-lg btn-primary btn-block">
            </div>
        </form>
    </div>
<?php

require_once '../footer.php';