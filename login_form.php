<?php
@include 'config.php';

session_start();

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = md5($_POST['password']);
   
   $select = " SELECT * FROM user_form WHERE email = '$email' && password = '$pass' ";

   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){

      $row = mysqli_fetch_array($result);

      if($row['user_type'] == 'admin'){

         $_SESSION['admin_name'] = $row['name'];
         header('location:admindashboard.php');

      }elseif($row['user_type'] == 'user'){

         $_SESSION['user_name'] = $row['name'];
         header('location:userdashboard.php');

      }
     
   }else{
      $error[] = 'incorrect email or password!';
   }

};
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Login page</title>
         <!--font awesome cdn link-->
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!--custom css file link-->
         <link rel="stylesheet" href="dashboard.css">
    </head>
    <body>
        <div class="body2" id="body2">
        <div class="container2" id="container2">
            <div class="form-container2 sign-in">
                <form action="" method="post" autocomplete="on">
                    <h1>sign-in</h1>
                    <?php
                     if(isset($error)){
                        foreach($error as $error){
                            echo '<span class="error-msg">'.$error.'</span>';
                        };
                     };
                    ?>
                    <div class="social-icons">
                        <a href="#" class="fab fa-facebook-f"></a>
                <a href="#" class="fab fa-l"></a>
                <a href="#" class="fab fa-instagram"></a>
                <a href="#" class="fab fa-linkedin"></a>
                <a href="#" class="fab fa-github"></a>
                    </div>
                    <span>or use your email password</span>
                   
                    <input type="email" name="email" placeholder="email" required>
                    <input type="password" name ="password" placeholder="password">
                    <a href="#"> forgot your password?</a>
                    <input type="submit" name="submit" value="Sign in" class="btn">
                </form>
            </div>
            <div class="toggle-container2">
                <div class="toggle2">
                    <!--<div class="toggle-pannel toggle-left">
                        <h1>Welcome back!</h1>
                        <p>Enter your details to access site features</p>
                        <button class="hidden" id="login">Sign In</button>
                    </div>-->
                    <div class="toggle-pannel2 toggle-right">
                        <h1>Welcome back!</h1>
                        <p>Enter your details to access site features.</p>
                        <!--<button class="hidden" id="register">Register</button>-->
                    </div>
                </div>
            </div>
        </div>
        </div>
        
        
    </body>
    </html>