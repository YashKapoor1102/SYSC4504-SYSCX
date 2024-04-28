<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>User List</title>
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
            <h2>User List</h2>
            <?php
                session_start();
                include("connection.php"); 

                // Connection to the database
                $db = new mysqli($server_name, $username, $password, $database_name);
                if ($db->connect_error) {
                    die("Connection failed: " . $db->connect_error);
                }

                if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 0) {

                    $stmt_user_info = $db->prepare("
                        SELECT 
                            ui.student_id, 
                            ui.first_name, 
                            ui.last_name, 
                            ui.student_email, 
                            up.program, 
                            permission.account_type
                        FROM 
                            users_info ui
                        LEFT JOIN 
                            users_program up ON ui.student_id = up.student_id
                        LEFT JOIN 
                            users_permissions permission ON ui.student_id = permission.student_id;
                    ");
                    $stmt_user_info->execute();

                    $result = $stmt_user_info->get_result();

                    if ($result->num_rows > 0) {
                        echo "<table border='1'>
                            <tr>
                                <th>Student ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Program</th>
                                <th>Account Type</th>
                            </tr>";

                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>".$row["student_id"]."</td>
                                    <td>".$row["first_name"]."</td>
                                    <td>".$row["last_name"]."</td>
                                    <td>".$row["student_email"]."</td>
                                    <td>".$row["program"]."</td>
                                    <td>".($row["account_type"] == 0 ? 'Admin' : 'Regular User')."</td>
                                </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "No users found.";
                    }
                } else {
                    echo "Permission denied. You must be an admin to view this page. <br>";
                    echo "<a href='index.php'>Go back to the home page.</a>"; 
                }
            ?>
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
        let userFullName = "<?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?>";
        let userAvatar = "../yash_kapoor_a03/images/img_avatar" + (parseInt("<?php echo $_SESSION['avatar']; ?>") + 1) + ".png";
        let userEmail = "<?php echo $_SESSION['email']; ?>";
        let userProgram = "<?php echo $_SESSION['program']; ?>";

        let userLoggedIn = <?php echo json_encode(isset($_SESSION['student_id'])); ?>;
        let userIsAdmin = <?php echo json_encode(isset($_SESSION['account_type']) && $_SESSION['account_type'] === 0); ?>;
        let userList = true;
   </script>
   <script src="userInfo.js"></script>
   <script src="navigationMenuLogic.js"></script>
</body>

</html>