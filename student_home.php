<?php
session_start();
include("dbase.php");

// Check if the student is logged in
if (!isset($_SESSION['student_email'])) {
    header("Location:login.php");
    exit;
}

$student_email = $_SESSION['student_email'];

// Fetch student data from the database
$query_student = "SELECT * FROM student WHERE Email = ?";
$stmt_student = $conn->prepare($query_student);
$stmt_student->bind_param("s", $student_email);
$stmt_student->execute();
$result_student = $stmt_student->get_result();

if ($result_student->num_rows > 0) {
    $student = $result_student->fetch_assoc();
    $enrolled_course_name = $student['course'];

    // Fetch enrolled course information
    $query_course = "SELECT image FROM courses WHERE name = ?";
    $stmt_course = $conn->prepare($query_course);
    $stmt_course->bind_param("s", $enrolled_course_name);
    $stmt_course->execute();
    $result_course = $stmt_course->get_result();

    if ($result_course->num_rows > 0) {
        $course = $result_course->fetch_assoc();
        $course_image = $course['image'];
    } else {
        $course_image = "default_course_image.jpg"; // Provide a default image path
    }
} else {
    echo "No student found with the given email.";
    exit;
}

$stmt_student->close();
$stmt_course->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>STUDENT-Home</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <link rel="stylesheet" href="student_dashboard/css/style.css">
   <style>
      /* CSS for adjusting image size */
      .box img {
         width: 500px; /* Adjust the width as needed */
         height: 300px; /* Maintain aspect ratio */
      }
      .icons {
         display: flex;
         align-items: center;
      }

      .icons div {
         margin: 0 10px; /* Adjust the spacing between icons */
         cursor: pointer;
      }

      .fa-sign-out {
         color: red; /* Customize the logout icon color */
      }
   </style>

</head>
<body>

<header class="header">

   <section class="flex">

      <a href="student_home.php"><img src="img/logo.png" class="logo" style="height: 7vh; width: 45vh;" alt="SkillSphere"></a>
      <form action="search.html" method="post" class="search-form">
         <input type="text" name="search_box" required placeholder="Search Here" maxlength="100">
         <button type="submit" class="fas fa-search"></button>
      </form>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="logout-btn">
            <a href="logout.php" class="fa fa-sign-out"></a>
         </div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

   </section>

</header>   

<div class="side-bar">

   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
    <img src="img/student_pic.png" class="image" alt="">
    <?php
    $full_name = htmlspecialchars($student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name']);
    ?>
    <h3 class="name"><?php echo $full_name; ?></h3>
    <p class="role">Student</p>
    <a href="student_dashboard/student_update.php" class="btn">Update Profile</a>
</div>

   <nav class="navbar">
      <a href="student_home.php"><i class="fas fa-home"></i><span>Home</span></a>
      <div class="dropdown">
         <button class="dropbtn"><i class="fas fa-chalkboard-teacher"></i><span>My Courses</span>
         </button>  
         <div class="dropdown-content">
            <a href="student_dashboard/download_notes.php"><i class="fas fa-file-download"></i><span>Download Notes</span></a>
            <a href="student_dashboard/upload_tasks.php"><i class="fas fa-tasks"></i><span>Upload Tasks</span></a>
            <a href="student_dashboard/daily_classes.php"><i class="fas fa-video"></i><span>Daily Classes</span></a>
          </div>
      </div>
      <a href="student_dashboard/student_about.php"><i class="fas fa-question"></i><span>About Us</span></a>
      <a href="student_dashboard/student_contact.php"><i class="fas fa-headset"></i><span>Contact us</span></a>
   </nav>

</div>

<section class="user-profile">

   <h1 class="heading">Your profile</h1>

   <div class="info">

   <div class="user">
         <img src="img/student_pic.png" alt="">
         <?php
         $full_name = htmlspecialchars($student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name']);
         ?>
         <h3 class="name"><?php echo $full_name; ?></h3>
         <p>Student</p>
         <a href="student_dashboard/student_update.php" class="inline-btn">Update Profile</a>
      </div>
   
      <div class="box-container">
   
      <div class="box">
         <a href="student_dashboard/home_playlist.php">
            <img src="course_images/<?php echo $course_image; ?>" alt="<?php echo $enrolled_course_name; ?>">
         </a>
         <h1><?php echo $enrolled_course_name; ?></h1>
      </div>
   
      </div>
   </div>

</section>














<footer class="footer">

   &copy; SkillSphere 2024 <span></span> | All Rights Are Reserved !

</footer>


<script src="student_dashboard/js/script.js"></script>

   
</body>
</html>