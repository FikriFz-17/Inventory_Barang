<?php
$phone = "6281539819240"; 

$message = "Bang daftarin gw bang";

$url = "https://wa.me/" . $phone . "?text=" . urlencode($message);

header("Location: $url");
exit();
?>