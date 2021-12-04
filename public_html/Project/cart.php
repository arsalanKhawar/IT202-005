<?php
require(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
?>
<h1>Your Cart</h1>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>