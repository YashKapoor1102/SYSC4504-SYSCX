<?php
   session_start(); 
   include("connection.php");

   // Connection to the database
   $db = new mysqli($server_name, $username, $password, $database_name);
   if ($db->connect_error) {
      die("Connection failed: " . $db->connect_error);
   }

   /**
    * Checks if the email entered by the user exists in the database.
    */
   function checkEmailExists($db, $email) {
      $stmt_check_email = $db->prepare("SELECT student_id FROM users_info WHERE student_email = ?");
      $stmt_check_email->bind_param("s", $email);
      $stmt_check_email->execute();
      $stmt_check_email->store_result();
      
      $exists = $stmt_check_email->num_rows > 0;
      $stmt_check_email->close();
      return $exists;
  }
  
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'check_email') {
      $email = $_POST['email'] ?? '';
      $response = ['exists' => checkEmailExists($db, $email)];
      
      echo json_encode($response);
      exit();
  }

   // Check form submission - only executes after email has been validated
   if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_POST['form_submission']) && $_POST['form_submission'] == 'register') {
      $email = $_POST['student_email'];
      $first_name = $_POST['first_name'];
      $last_name = $_POST['last_name'];
      $dob = $_POST['DOB'];
      $password = $_POST['password'];
      $program = $_POST['program'];

      if(!checkEmailExists($db, $email)) {
         // ensuring this only runs if the email does not exist again
         // security purposes - protecting against SQL injection attacks
         $stmt = $db->prepare("INSERT INTO users_info (student_email, first_name, last_name, dob) VALUES (?, ?, ?, ?)");
         $stmt->bind_param("ssss", $email, $first_name, $last_name, $dob);
      
         // hashing the password 
         $password = password_hash($password, PASSWORD_BCRYPT);
         
         // Executing the query
         if ($stmt->execute()) {
            $last_id = $db->insert_id;
            $_SESSION['student_id'] = $last_id; // Store student_id in session
            
            // insert a record into users_passwords - hashed password and student_id
            $stmt_password = $db->prepare("INSERT INTO users_passwords (student_id, password) VALUES (?, ?)");
            $stmt_password->bind_param("is", $last_id, $password);

            if (!$stmt_password->execute()) {
               error_log("Error inserting password for user $last_id: " . $stmt_password->error);
            }

            $stmt_password->close();

            // insert into users_program table
            $stmt_program = $db->prepare("INSERT INTO users_program (student_id, program) VALUES (?, ?)");
            $stmt_program->bind_param("is", $last_id, $program);
            
            if ($stmt_program->execute()) {

                  // insert a record into users_permissions
                  $stmt_permissions = $db->prepare("INSERT INTO users_permissions (student_id, account_type) VALUES (?, ?)");
                  $account_type = 1;   // default value for regular user
                  $stmt_permissions->bind_param("ii", $last_id, $account_type);
                  $stmt_permissions->execute();

                  if($stmt_permissions->error) {
                     error_log("Error setting user permissions: " . $stmt_permissions->error);
                  }

                  // insert default records for avatar 
                  $stmt = $db->prepare("INSERT INTO users_avatar (student_id, avatar) VALUES (?, ?)");
                  $stmt->bind_param("ii", $last_id, $defaultAvatar);
                  $defaultAvatar = 0; 
                  $stmt->execute();
                  
                  // insert default records for address
                  $stmt = $db->prepare("INSERT INTO users_address (student_id, street_number, street_name, city, province, postal_code) VALUES (?, ?, ?, ?, ?, ?)");
                  $stmt->bind_param("iissss", $last_id, $streetNumber, $streetName, $city, $province, $postalCode);
                  $streetNumber = NULL;
                  $streetName = NULL;
                  $city = NULL;
                  $province = NULL;
                  $postalCode = NULL;
                  $stmt->execute();
                  
                  // close database connection and statement
                  $stmt->close();
                  $db->close();
                  // redirecting to profile page
                  header("Location: profile.php");
                  exit();
            } else {
                  error_log("Error: " . $stmt_program->error);
            }
         } else {
            error_log("Error: " . $stmt->error);
         }
      }
   }
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Register on SYSCX</title>
   <link rel="stylesheet" href="assets/css/reset.css">
   <link rel="stylesheet" href="assets/css/style.css">
   <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
   <header>
      <h1>SYSCX</h1>
      <p>Social media for SYSC students in Carleton University</p>
   </header>

   <div class="container">
      <nav>
         <ul id="navigation-menu">
         </ul>
      </nav>
      <main>
         <section>
            <h2>Register a new profile</h2>
            <form action="" method="post">
               <input type="hidden" name="form_submission" value="register">
               <!-- Personal information table -->
               <div class="heading-style"><span>Personal information</span></div>
               <table>
                  <tr>
                     <td><label for="firstName">First Name:</label></td>
                     <td><input type="text" id="firstName" name="first_name" placeholder="ex: John Snow" required></td>
                     <td><label for="lastName">Last Name:</label></td>
                     <td><input type="text" id="lastName" name="last_name" required></td>
                     <td><label for="dob">DOB:</label></td>
                     <td><input type="date" id="dob" name="DOB" required></td>
                  </tr>
               </table>

               <!-- Profile Information table -->
               <div class="heading-style"><span>Profile Information</span></div>
               <table>
                  <tr>
                     <td><label for="email">Email address:</label></td>
                     <td>
                        <input type="email" id="email" name="student_email" required>
                        <span id="email-error"></span>
                     </td>
                  </tr>
                  <tr>
                     <td><label for="program">Program:</label></td>
                     <td>
                        <select id="program" name="program" required>
                           <option value="">Choose Program</option>
                           <option value="Computer Systems Engineering">Computer Systems Engineering</option>
                           <option value="Software Engineering">Software Engineering</option>
                           <option value="Communications Engineering">Communications Engineering</option>
                           <option value="Biomedical and Electrical">Biomedical and Electrical</option>
                           <option value="Electrical Engineering">Electrical Engineering</option>
                           <option value="Special">Special</option>
                        </select>
                     </td>
                  </tr>
                  <tr>
                     <td><label for="password">Password:</label></td>
                     <td><input type="password" id="password" name="password" required></td>
                  </tr>
                  <tr>
                     <td><label for="confirm_password">Confirm Password:</label></td>
                     <td>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <span id="error_message_password"></span></td>
                     </td>
                  </tr>
                  <tr>
                     <td colspan="2" class="button-container">
                        <input type="submit" value="Register">
                        <input type="reset" value="Reset">
                     </td>
                  </tr>
               </table>
               <p>Already have an account? <a href="login.php">Log in now.</a></p>
            </form>
         </section>
      </main>
      <aside id="user-info">
      </aside>
   </div>
   <script>
      let userLoggedIn = <?php echo json_encode(isset($_SESSION['student_id'])); ?>;
      let userIsAdmin = <?php echo json_encode(isset($_SESSION['account_type']) && $_SESSION['account_type'] === 0); ?>;
      let register = true;
   </script>
   <script src="checkEmailExists.js"></script>
   <script src="checkPasswordMatch.js"></script>
   <script src="navigationMenuLogic.js"></script>
</body>

</html>
