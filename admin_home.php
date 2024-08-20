<?php
session_start();
include("dbase.php");

// Fetch admin details from the database
$admin_email = $_SESSION['admin_email'] ?? '';
$admin_details = [];

if ($admin_email) {
    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $admin_details = $result->fetch_assoc();
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ADMIN-Home</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
   <link rel="stylesheet" href="admin_dashboard/css/style.css">
</head>
<body>

<header class="header">
   <section class="flex">
      <a href="admin_home.php"><img src="img/Logo.png" class="logo" style="height: 7vh; width: 45vh;" alt="SkillSphere"></a>
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
      <img src="img/admin_pic.png" class="image" alt="">
      <h3 class="name"><?= htmlspecialchars($admin_details['full_name'] ?? 'Admin Name') ?></h3>
      <p class="role">Admin</p>
      <a href="admin_dashboard/admin_update.php" class="btn">Update Profile</a>
   </div>

   <nav class="navbar">
      <a href="admin_home.php"><i class="fas fa-home"></i><span>Home</span></a>
      <a href="admin_dashboard/admin_students.php"><i class="fas fa-users"></i><span>Students</span></a>
      <a href="admin_dashboard/admin_teachers.php"><i class="fas fa-chalkboard-teacher"></i><span>Teachers</span></a>
      <a href="admin_dashboard/admin_courses.php"><i class="fas fa-folder-open"></i><span>Our Courses</span></a>
      <a href="admin_dashboard/daily_classes.php"><i class="fas fa-video"></i><span>Daily Classes</span></a>
      <a href="admin_dashboard/admin_about.php"><i class="fas fa-question"></i><span>About Us</span></a>
   </nav>
</div>

<section class="teacher-profile">
   <h1 class="heading">Profile Details</h1>
   <div class="details">
      <div class="tutor">
         <img src="img/admin_pic.png" alt="">
         <h3><?= htmlspecialchars($admin_details['full_name'] ?? 'Admin Name') ?></h3>
         <span>Admin</span>
      </div>
   </div>
</section>

<section class="courses">
   <h1 class="heading">New Courses</h1>
   <div class="box-container">
      <div class="box">
         <div class="thumb">
            <img src="course_images/Web Design.jpg" alt="">
         </div>
         <h3 class="title">Web Design & Development</h3>
      </div>
      <div class="box">
         <div class="thumb">
            <img src="course_images/Full Stack.jpg" alt="">
         </div>
         <h3 class="title">Full Stack Development</h3>
      </div>
      <br><p></p></br>
   </div>
</section>

<footer class="footer">
   &copy; SkillSphere 2024 <span></span> | All Rights Are Reserved !
</footer>

<script src="admin_dashboard/js/script.js"></script>
</body>
</html>
