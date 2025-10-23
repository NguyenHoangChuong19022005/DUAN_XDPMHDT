<?php
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../models/Premium.php';

$premium = new Premium($db);
if ($_POST['stripe_id']) {
    $premium->subscribe($_SESSION['user_id'], 'premium', $_POST['stripe_id']);
}
?>