<?php
   session_start(); 
   include("connection.php");

   // redirecting user if user does not come from the login.php page
   if (!isset($_SESSION['student_id'])) {
      header('Location: login.php');
      exit();
   }

   $student_id = $_SESSION['student_id'];

   // Connection to the database
   $db = new mysqli($server_name, $username, $password, $database_name);
   if ($db->connect_error) {
      die("Connection failed: " . $db->connect_error);
   }

   $first_name = $last_name = $dob = $email = $program = "";
   $avatar = $street_number = $street_name = $city = $province = $postal_code = NULL;

   // fetching the user's existing information
   if ($_SERVER["REQUEST_METHOD"] != "POST") {
      $stmt = $db->prepare("
         SELECT 
            ui.student_email, 
            ui.first_name, 
            ui.last_name, 
            ui.dob, 
            up.program,
            ua.avatar,
            ua2.street_number,
            ua2.street_name,
            ua2.city,
            ua2.province,
            ua2.postal_code
         FROM users_info ui
         LEFT JOIN users_program up ON ui.student_id = up.student_id
         LEFT JOIN users_avatar ua ON ui.student_id = ua.student_id
         LEFT JOIN users_address ua2 ON ui.student_id = ua2.student_id
         WHERE ui.student_id = ?
      ");
      $stmt->bind_param("i", $student_id);
      $stmt->execute();
      $stmt->bind_result($email, $first_name, $last_name, $dob,
                         $program, $avatar, $street_number, $street_name,
                         $city, $province, $postal_code);
      $stmt->fetch();
      $stmt->close();
   }

   // checking if form was submitted
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $first_name = $_POST['first_name'];
      $last_name = $_POST['last_name'];
      $dob = $_POST['DOB'];
      $email = $_POST['student_email'];
      $program = $_POST['program'];
      $avatar = (isset($_POST['avatar']) && $_POST['avatar'] !== '') ? intval($_POST['avatar']) : NULL;
      $street_number = $_POST['street_number'];
      $street_name = $_POST['street_name'];
      $city = $_POST['city'];
      $province = $_POST['province'];
      $postal_code = $_POST['postal_code'];

      // updating users_info table
      if ($stmt = $db->prepare("UPDATE users_info SET student_email = ?, first_name = ?, last_name = ?, dob = ? WHERE student_id = ?")) {
         $stmt->bind_param("ssssi", $email, $first_name, $last_name, $dob, $student_id);
         $stmt->execute();
         $stmt->close();
      }

      // updating users_program table
      if ($stmt = $db->prepare("UPDATE users_program SET program = ? WHERE student_id = ?")) {
         $stmt->bind_param("si", $program, $student_id);
         $stmt->execute();
         $stmt->close();
      }

      // updating users_avatar table, assuming 'avatar' is optional
      if ($stmt = $db->prepare("UPDATE users_avatar SET avatar = ? WHERE student_id = ?")) {
         $stmt->bind_param("ii", $avatar, $student_id);
         $stmt->execute();
         $stmt->close();
      }

      // updating users_address table
      if ($stmt = $db->prepare("UPDATE users_address SET street_number = ?, street_name = ?, city = ?, province = ?, postal_code = ? WHERE student_id = ?")) {
         $stmt->bind_param("issssi", $street_number, $street_name, $city, $province, $postal_code, $student_id);
         $stmt->execute();
         $stmt->close();
      }

      // redirecting to profile.php to show the updated values
      header("Location: profile.php");
      exit();
   }

   // closing database connection
   $db->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Update Profile Information</title>
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
            <h2>Update Profile information</h2>
            <form action="profile.php" method="post">
               <div class="heading-style"><span>Personal information</span></div>
               <!-- Personal information table -->
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

               <div class="heading-style"><span>Address</span></div>
               <!-- Address table -->
               <table>
                  <tr>
                     <td><label for="streetNumber">Street Number:</label></td>
                     <td><input type="text" id="streetNumber" name="street_number" required></td>
                     <td><label for="streetName">Street Name:</label></td>
                     <td><input type="text" id="streetName" name="street_name" required></td>
                     <td><label for="city">City:</label></td>
                     <td><input type="text" id="city" name="city" required></td>
                  </tr>
                  <tr>
                     <td><label for="province">Province:</label></td>
                     <td><input type="text" id="province" name="province" required></td>
                     <td><label for="postalCode">Postal Code:</label></td>
                     <td><input type="text" id="postalCode" name="postal_code" required></td>
                     <td></td>
                     <td></td>
                  </tr>
               </table>

               <!-- Profile Information table -->
               <div class="heading-style"><span>Profile Information</span></div>
               <table>
                  <tr>
                     <td><label for="email">Email address:</label></td>
                     <td><input type="email" id="email" name="student_email" required></td>
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
                     <td colspan="2">
                        <label>Choose your Avatar</label>
                        <div class="avatars-container">
                           <input type="radio" id="avatar1" name="avatar" value="0" required>
                           <label class="avatar-label" for="avatar1"><img src="../yash_kapoor_a03/images/img_avatar1.png" alt="Avatar 1"></label>

                           <input type="radio" id="avatar2" name="avatar" value="1">
                           <label class="avatar-label" for="avatar2"><img src="../yash_kapoor_a03/images/img_avatar2.png" alt="Avatar 2"></label>

                           <input type="radio" id="avatar3" name="avatar" value="2">
                           <label class="avatar-label" for="avatar3"><img src="../yash_kapoor_a03/images/img_avatar3.png" alt="Avatar 3"></label>

                           <input type="radio" id="avatar4" name="avatar" value="3">
                           <label class="avatar-label" for="avatar4"><img src="../yash_kapoor_a03/images/img_avatar4.png" alt="Avatar 4"></label>

                           <input type="radio" id="avatar5" name="avatar" value="4">
                           <label class="avatar-label" for="avatar5"><img src="../yash_kapoor_a03/images/img_avatar5.png" alt="Avatar 5"></label>
                        </div>
                     </td>
                  </tr>
                  <tr>
                     <td colspan ="2" class="button-container">
                        <input type="submit" value="Submit">
                        <input type="reset" value="Reset">
                     </td>
                  </tr>
               </table>
            </form>   
         </section>
      </main>
      <aside id="user-info">
         <h3 id="userFullName"><!-- Going to use JavaScript to set the name --></h3>
         <label class="avatar-label">
            <img id="userAvatar" src="" alt="Avatar">
         </label>
         <p>Email: <a id="userEmail" href="mailto:"><!-- Going to use JavaScript to set the email --></a></p>
         <p class="no-margin">Program:</p>
         <p id="userProgram"><!-- Going to use JavaScript to set the program --></p>
      </aside>

   </div>
   <script>
      let firstName = "<?php echo $first_name ?>";
      let lastName = "<?php echo $last_name ?>";
      let dob = "<?php echo $dob ?>";
      let email = "<?php echo $email ?>";
      let program = "<?php echo $program ?>";

      let streetNumber = "<?php echo $street_number ?>";
      let streetName = "<?php echo $street_name ?>";
      let city = "<?php echo $city ?>";
      let province = "<?php echo $province ?>";
      let postalCode = "<?php echo $postal_code ?>";

      let avatarIndex = "<?php echo $avatar; ?>";

      // user info values
      let userFullName = "<?php echo $first_name . ' ' . $last_name; ?>";
      let userAvatar = "../yash_kapoor_a03/images/img_avatar" + (parseInt("<?php echo $avatar; ?>") + 1) + ".png";
      let userEmail = "<?php echo $email; ?>";
      let userProgram = "<?php echo $program; ?>";

      let userLoggedIn = <?php echo json_encode(isset($_SESSION['student_id'])); ?>;
      let userIsAdmin = <?php echo json_encode(isset($_SESSION['account_type']) && $_SESSION['account_type'] === 0); ?>;
      let profile = true;
   </script>
   <script src="displayProfileInfo.js"></script>
   <script src="userInfo.js"></script>
   <script src="navigationMenuLogic.js"></script>
</body>

</html>
