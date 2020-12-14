<html>
<body>
    <form action="learnphp.php" method="post">
        Name: <input type="text" value="username" name="username">
        Street Address: <input type="text" value="streetaddress" name="streetaddress">
        City Address: <input type="text" value="cityaddress" name="cityaddress">
        <button type="submit" name="submit" value="submit">Submit</button>

    </form>
<?php
echo "Hello World";

echo date('h:i:s a, l, F, jS Y');

$num = 0;
while($num < 20) {
    echo ++$num . ", ";
}


 for($number = 1; $number <= 20; $number++) {
     echo $number;

     if($number != 20){
         echo ", ";
     }else{
         break;
     }
 }

?>
</body>
</html>