<?php

// header("Content-Security-Policy: default-src 'self'; script-src 'self';"); // FF 23+ Chrome 25+ Safari 7+ Opera 19+
// header("X-Content-Security-Policy: default-src 'self'; script-src 'self';"); // IE 10+

// $isHttps = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off';
// if ($isHttps)
// {
//   header('Strict-Transport-Security: max-age=31536000'); // FF 4 Chrome 4.0.211 Opera 12
// }

function validate($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

$err = array();


$email = validate($_POST["email"]);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $err[] = "Invalid email format";
}else{
    $e = $email;
}

$name = validate($_POST["name"]);
if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
  $err[] = "Name: Only letters and white space allowed";
}else{
    $n = $name;
}

$industry = validate($_POST["industry"]);
if (!preg_match("/^[a-zA-Z ]*$/",$industry)) {
  $err[] = "Industry: Only letters and white space allowed";
}else{
    $i = $industry;
}

$d = validate($_POST['date']);

$location = validate($_POST['location']);
if (!preg_match("/^[a-zA-Z ]*$/",$location)) {
  $err[] = "Invalid location";
}else{
    $l = $location;
}


if(empty($err)){

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=womentech", "root", "");
        
        $check_existing = $pdo->prepare("SELECT * FROM entries WHERE email=:email");

        $check_existing->bindParam(':email', $e);

        $result = $check_existing->execute();

        $user = $check_existing->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo 1;
        } else {
            $count = $pdo->prepare("INSERT INTO entries(email, name, date1, location, industry) VALUES(:email, :name, :date1, :location, :industry)");

            $count->bindParam(':name', $n);
            $count->bindParam(':email', $e);
            $count->bindParam(':date1', $d);
            $count->bindParam(':location', $l);
            $count->bindParam(':industry', $i);

            $value = $count->execute();

            if($value){
                $headers = 'From: Women in Technology <no-reply@accessbankplc.com>' . "\r\n" .  
                        'Content-type: text/html' . "\r\n" .    
                        'X-Mailer: PHP/' . phpversion();
            
                $message = "<p>Thank you for applying for the Empowering Women in Technology program with Access Bank</p><br><br>
                    <p>Access Bank account holders and W community members are entitled to a discounted fee of N37,500 while non-members will pay N100,000.</p><br><br>
                    <p>Payments should be made to:<br>
                    Account Name: Access Bank W Events<br>
                    Account Number: 0694332364</p>
                    <div><p>Regards.</p></div>
                    <br><br>";

                $message2 = "<p>Hi, </p>
                        <p> A form on the 'Empowering Women in Technology' page has been filled, the details filled are;</p><br><br>

                        <p>Name: $n</p>
                        <p>Email Address: $e</p>
                        <p>Location: $l</p>
                        <p>Date: $d</p>
                        <p>Industry: $i</p>

                        <div><p>Regards.</p></div>
                        <br><br>";

                mail('womenbanking@accessbankplc.com ', 'New Entry - Empowering Women in Technology' ,$message, $headers);

                mail($e, 'Application for Empowering Women in Technology', $message2, $headers); 
        
                echo 2;
            }else{
                echo "Please contact an admin";
            }
        }
        $pdo = null;
    }catch(PDOException $e){
        echo $e->getMessage();
        echo "Pls bear with us, maintenance in progress";
    }

}

else {
    $err_text = "";
    foreach ($err as $error) {
        
        $err_text .= "<p class='label label-danger'>";
        $err_text .= $error;
        $err_text .= "</p>";

    }

    echo $err_text;

}


