<?php
session_start();
include_once('config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (isset($_POST['resend'])) {
    $email = $_POST['email'];
    $otp = mt_rand(100000, 999999);

    $ret = "SELECT id,isEmailVerify FROM tblusers where (emailId=:uemail)";
    $queryt = $dbh->prepare($ret);
    $queryt->bindParam(':uemail', $email, PDO::PARAM_STR);
    $queryt->execute();
    $results = $queryt->fetchAll(PDO::FETCH_OBJ);
    if ($queryt->rowCount() > 0) {
        foreach ($results as $result) {
            $verifystatus = $result->isEmailVerify;
        }

        if ($verifystatus == '1') {
            echo "<script>alert('Email already verified. No need to verify again.');</script>";
        } else {
            $_SESSION['emailid'] = $email;
            $_SESSION['otp'] = $otp;

            $sql = "UPDATE tblusers SET emailOtp=:otp WHERE emailId=:emailid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':emailid', $email, PDO::PARAM_STR);
            $query->bindParam(':otp', $otp, PDO::PARAM_STR);
            $query->execute();

            $mail = new PHPMailer;
            
            $mail->isSMTP();

           
            $mail->Host = 'smtp.gmail.com'; 
            $mail->Port = 587; 
            $mail->SMTPAuth = true;

           
            $mail->Username = 'akhilsai0099@gmail.com'; 
            $mail->Password = 'znvtsycvzwdhxk';

            $mail->setFrom('akhilsai0099@gmail.com', 'Your Name');

            $mail->addAddress($email, 'Recipient Name');

            $mail->Subject = 'OTP Verification';
            $mail->isHTML(true);
            $mail->Body = "<html></body><div><div>Dear Recipient Name,</div></br></br>";
            $mail->Body .= "<div style='padding-top:8px;'>Thank you for registering with us. OTP for Account Verification is $otp</div><div></div></body></html>";


            if ($mail->send()) {
                echo "<script>window.location.href='verify-otp.php'</script>";
            } else {
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            }
        }
    } else {
        echo "<script>alert('Email id not registered yet');</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Courgette|Pacifico:400,700">
<title>User Registration with email otp verification in PHP</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<style>
body {
	color: #999;
	background: #e2e2e2;
	font-family: 'Roboto', sans-serif;
}
.form-control {
	min-height: 41px;
	box-shadow: none;
	border-color: #e1e1e1;
}
.form-control:focus {
	border-color: #bc42f5;
}	
.form-control, .btn {        
	border-radius: 3px;
}
.form-header {
	margin: -30px -30px 20px;
	padding: 30px 30px 10px;
	text-align: center;
	background: #bc42f5;
	border-radius: 20px;
	border-bottom: 1px solid #eee;
	color: #fff;
}
.form-header h2 {
	font-size: 34px;
	font-weight: bold;
	margin: 0 0 10px;
	font-family: 'Pacifico', sans-serif;
}
.form-header p {
	margin: 20px 0 15px;
	font-size: 17px;
	line-height: normal;
	font-family: 'Courgette', sans-serif;
}
.signup-form {
	width: 390px;
	margin: 0 auto;	
	padding: 30px 0;	
}
.signup-form form {
	color: #999;
	border-radius: 20px;
	margin-bottom: 15px;
	background: #f0f0f0;
	box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
	padding: 30px;
}
.signup-form .form-group {
	margin-bottom: 20px;
}		
.signup-form label {
	font-weight: normal;
	font-size: 14px;
}
.signup-form input[type="checkbox"] {
	position: relative;
	top: 1px;
}
.signup-form .btn {        
	font-size: 16px;
	font-weight: bold;
	background: #bc42f5;
	border: none;
	min-width: 200px;
}
.signup-form .btn:hover, .signup-form .btn:focus {
	background: #00b073 !important;
	outline: none;
}
.signup-form a {
	color: #bc42f5;		
}
.signup-form a:hover {
	text-decoration: underline;
}
</style>
</head>
<body>
<div class="signup-form">
    <form  method="post">
		<div class="form-header">
			<h2>Resend OTP</h2>
		</div>

        <div class="form-group">
			<label>Email Address</label>
        	<input type="email" class="form-control" name="email" required="required">
        </div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary btn-block btn-lg" name="resend">Resend</button>
		</div>	
    </form>
	
</div>
</body>
</html>