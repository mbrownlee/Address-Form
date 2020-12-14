<html>
<body>
<?php


$userName = $_POST['username'];
$streetAddress = $_POST['streetaddress'];
$cityAddress = $_POST['cityaddress'];

echo $userName . "<br>";
echo $streetAddress . "<br>";
echo $cityAddress . "<br>";

$str = <<<EOD
The customer name is $userName and they live at $streetAddress in $cityAddress. </br>
EOD;
echo $str;
?>
</body>
</html>