


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   <form action="index.php" method="post">
    <input type="radio" name="credit_card" value="visa">Visa<br>
     <input type="radio" name="credit_card" value="mastercard">Mastercard<br>
      <input type="radio" name="credit_card" value="paypal">Paypal<br>
      <input type="text" name="some" >
       <input type="submit" name="confirm" value="submit">
   </form>
</body>
</html>



<?php

  


   setcookie("fav_food","achu"+time()+(300),"/");
    

    if(isset($_POST["confirm"])){
         $credit_card=$_POST["credit_card"];
         echo $credit_card;
         happy_birthday("fureh");
    }

    function happy_birthday($bro){
        echo "Happy Birthday {$bro}";
    }

    $username="fureh";
    $username=strtolower($username);


    $upper=strtoupper($username);



   
?>