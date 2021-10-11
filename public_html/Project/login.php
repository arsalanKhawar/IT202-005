<?php
require(__DIR__ . "/../../partials/nav.php")
?>
<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" required />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <div>
        <label for="confirm">Confirm</label>
        <input type="password" name="confirm" required minlength="8" />
    </div>
    <input type="submit" value="Register" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>
<?php
 //TODO 2: add PHP Code
 if(isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"])){
     $email = se($_POST, "email", "", false);
     $password =se($_POST, "password", "", false);
     $confirm = se($_POST, "confirm", "", false);
 
 //TODO 3: validate/use
 $errors = [];
 if(empty($email)){
     array_push($errors,"email must be set");
 }

 //sanitize
 //$email = filter_var($email, FILTER_SANITIZE_EMAIL);
 $email = sanitize_email($email);

//validate
//if(!filter_var($email, FILTER_SANITIZE_EMAIL)){
if(!is_valid_email($email)){
    array_push($errors, "Invalid email address");
}

 if(empty($password)){
    array_push($errors,"Password must be set");
}
if(empty($confirm)){
    array_push($errors,"Confirm password must be set");
}
if(strlen($password) < 8){
    array_push($errors,"password must be 8 or more characters");
}
if(strlen($password)> 0 && $password !== $confirm){
    array_push($errors,"passwords don't match");
}
if(count($errors)> 0){
    echo "<pre>" . var_export($errors,true) . "</pre>";
}
else{
   $db = getDB();
   $stmt = $db->prepare("SELECT email, password FROM Users WHERE email = :email");
   try{
       $r = $stmt->execute([":email" => $email]);
       if($r){
           $user = $stmt->fetch(PDO::FETCH_ASSOC);
           if($user){
               $hash = $user["password"];
               unset($user["password"]);
               if(password_verify($password,$hash)){
                   echo "Welcome, $email";
                   $_SESSION["user"] = $user;
                   die(header("Location: home.php"));
               }
               else{
                   echo "Invalid password";
               }
           }
           else{
               echo "Invalid email";
           }

       }
   }catch(Exception $e){
    echo "<pre>" . var_export($e,true) . "</pre";
}
}
 }
?>

