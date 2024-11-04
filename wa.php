<?php
$phone = "6285299699662"; 

$message = "Bang daftarin gw bang";

$url = "https://wa.me/" . $phone . "?text=" . urlencode($message);

header("Location: $url");
exit();
?>