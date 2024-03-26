<?php

@include 'config.php';

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $organisation =  mysqli_real_escape_string($conn, $_POST['organisation']);
   $pass = md5($_POST['password']);
   $cpass = md5($_POST['cpassword']);
   $user_type = $_POST['user_type'];
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;


   $select = " SELECT * FROM user_form WHERE email = '$email' && password = '$pass' ";

   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){

      $error[] = 'user already exist!';

   }else{

      if($pass != $cpass){
         $error[] = 'password not matched!';
      }elseif($image_size > 2000000){
        $error[] = 'image size is too large!';
     }else{
         $insert = mysqli_query($conn,"INSERT INTO user_form(name, email, organisation ,password, user_type,image) VALUES('$name','$email','$organisation','$pass','$user_type','$image')");
         
       
         if($insert){
            move_uploaded_file($image_tmp_name, $image_folder);
            $error[] = 'registered successfully!';
         header('location:adminuseradmin.php');
         }else{
            $error[] = 'registeration failed!';
         }
      }
   }

};


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
        <title>Automated chain of custody website</title>
         <!--font awesome cdn link-->
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!--custom css file link-->
         <link rel="stylesheet" href="dashboard.css">
    </head>
    <body>
        <div class="body" id="body">
        <div class="container" id="container">
            <div class="form-container sign-up">
                <form action="" method="post">
                    <h1>Create Account</h1>
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
                    <span>or use your email for registration</span>
                    <input type="text" name="name" placeholder="full names" required>
                    
                    <input type="email" name="email" placeholder="email" required>
                    <input type="text" name="organisation" placeholder="Organisation" required>

                    <input type="password" name="password" placeholder="password">
                    <input type="password" name="cpassword" placeholder=" confirm password">
                    <select name="user_type">
                        <option value ="user">user</option>
                        <option value ="admin">admin</option>
                    </select>
                    <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
                    <input type="submit" name="submit" value="register now" class="btn">
                </form>
            </div>
            <div class="toggle-container">
                <div class="toggle">
                    <div class="toggle-pannel toggle-left">
                        <h1>Hello, <span>Admin! <span></h1>
                        <p>Register their details to access site features.</p>
                        <!--<button class="hidden" id="register">Register</button>-->
                    </div>
                </div>
            </div>
        </div>
        </div>
    </body>
</html>