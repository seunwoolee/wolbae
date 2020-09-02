<?
$securePassword = trim(base64_encode(hash('sha256', "7617", true))); 
echo $securePassword;
?>