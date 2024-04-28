<?php
    session_start();
    include("connection.php"); 

    // Connection to the database
    $db = new mysqli($server_name, $username, $password, $database_name);
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    $response = ['success' => false, 'message' => ''];
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['student_email'];
        $password = $_POST['password'];

        $stmt = $db->prepare("
            SELECT ui.student_id, up.password, upm.account_type
            FROM users_info ui
            JOIN users_passwords up ON ui.student_id = up.student_id
            JOIN users_permissions upm ON ui.student_id = upm.student_id
            WHERE ui.student_email = ?
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // this shall only get executed if the student_email that the user
            // entered is valid (i.e., found in the database).
            $stmt->bind_result($student_id, $hashedPassword, $account_type);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['student_id'] = $student_id;
                $_SESSION['account_type'] = $account_type;

                $response['success'] = true;
                $response['message'] = 'Login successful.'; 
            } else {
                $response['message'] = "Invalid password.";
            }
        } else {
            $response['message'] = "Email does not exist.";
        }
        $stmt->close();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo json_encode($response);
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <title>Login on SYSCX</title>
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
                <h2>Login</h2>
                <form id="loginForm" action="login.php">
                    <table>
                        <tr>
                            <td><label for="email">Email address:</label></td>
                            <td><input type="email" id="email" name="student_email" required></td>
                        </tr>
                        <tr>
                            <td><label for="password">Password:</label></td>
                            <td><input type="password" id="password" name="password" required></td>
                        </tr>
                        <tr>
                            <td colspan="2"><span id="error_message_password"></span></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="button-container">
                                <input type="submit" value="Login">
                                <input type="reset" value="Reset">
                            </td>
                        </tr>
                    </table>
                    <p>Don't have an account? <a href="register.php">Register now</a>.</p>
                </form>
            </section>
        </main>
        <aside id="user-info">
        </aside>
    </div>
    <script>
      let userLoggedIn = <?php echo json_encode(isset($_SESSION['student_id'])); ?>;
      let login = true;
      let formData = this;
    </script>
    <script src="loginFormHandler.js"></script>
    <script src="navigationMenuLogic.js"></script>
</body>
</html>
