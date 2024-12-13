<?php
<<<<<<< HEAD
$phone = "6285299699662"; 
=======
$phone = "6281539819240"; 
>>>>>>> ba514db (Update 13 December)

$message = "Bang daftarin gw bang";

$url = "https://wa.me/" . $phone . "?text=" . urlencode($message);

header("Location: $url");
exit();
?>