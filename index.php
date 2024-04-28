<?php
   session_start(); 
   include("connection.php");
   $test = '';

   // Connection to the database
   $db = new mysqli($server_name, $username, '', $database_name);
   if ($db->connect_error) {
      die("Connection failed: " . $db->connect_error);
   }
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>SYSCX Home Page</title>
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
         <section id="new-post">
            <h2>New Post</h2>
            <form action="index.php" method="post">
               <!-- New Posts Table (includes the textarea in one row and button in the second row) -->
               <table>
                  <tr>
                     <td><textarea name="new_post" placeholder="What is happening?! (max 280 char)"></textarea></td>
                  </tr>
                  <tr>
                     <td class="button-container">
                        <input type="submit" value="Post">
                        <input type="reset" value="Reset">
                     </td>
                  </tr>
               </table>
            </form>
            <div id="posts">
               <h2>Posts</h2>
               <?php
                 
                  

                  // checking if a user is logged in
                  if (!isset($_SESSION['student_id'])) {
                     header('Location: login.php');
                     exit();
                  }

                  $student_id = $_SESSION['student_id'];

                  
                  
                  // fetching the user's existing information
                  if ($_SERVER["REQUEST_METHOD"] != "POST") {
                     $stmt = $db->prepare("SELECT ui.first_name, ui.last_name, ui.student_email, up.program, ua.avatar
                     FROM users_info ui
                     LEFT JOIN users_program up ON ui.student_id = up.student_id
                     LEFT JOIN users_avatar ua ON ui.student_id = ua.student_id
                     WHERE ui.student_id = ?");
                     $stmt->bind_param("i", $student_id);
                     $stmt->execute();
                     $stmt->bind_result($first_name, $last_name, $email, $program, $avatar);
                     $stmt->fetch();
                     $stmt->close();
                     
                     $_SESSION['first_name'] = $first_name;
                     $_SESSION['last_name'] = $last_name;
                     $_SESSION['email'] = $email;
                     $_SESSION['program'] = $program;
                     $_SESSION['avatar'] = $avatar;
                  }

                  // Check form submission to insert a new post
                  if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['new_post'])) {
                     $new_post = $_POST['new_post'];
                     
                     // inserting the new post into the database
                     $stmt_insert = $db->prepare("INSERT INTO users_posts (student_id, new_post, post_date) VALUES (?, ?, NOW())");
                     $stmt_insert->bind_param("is", $student_id, $new_post);
                     
                     if (!$stmt_insert->execute()) {
                        error_log("Error: " . $stmt_insert->error);
                     }
                     
                     $stmt_insert->close();
                  }

                  // preparing SQL query to fetch the last 10 posts from all users
                  $stmt = $db->prepare("SELECT new_post, post_date FROM users_posts ORDER BY post_date DESC LIMIT 10");

                  // executing the query
                  $stmt->execute();

                  // binding result variables
                  $stmt->bind_result($post, $post_date);

                  // fetching values and displaying each post
                  $postsList = [];
                  while ($stmt->fetch()) {
                     $postsList[] = [
                        'post' => $post,
                        'post_date' => $post_date
                     ];
                  }

                  // closing database connection and statement
                  $stmt->close();
                  $db->close();
               ?>
            </div>
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
      let posts = <?php echo json_encode($postsList); ?>;

      let userLoggedIn = <?php echo json_encode(isset($_SESSION['student_id'])); ?>;
      let userIsAdmin = <?php echo json_encode(isset($_SESSION['account_type']) && $_SESSION['account_type'] === 0); ?>;
      let index = true;
   </script>
   <script src="displayPosts.js"></script>
   <script src="userInfo.js"></script>
   <script src="navigationMenuLogic.js"></script>
</body>
</html>