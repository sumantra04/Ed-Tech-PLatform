<?php
session_start();
include("dbase.php");

// Check if the teacher is logged in
if (!isset($_SESSION['teacher_email'])) {
    header("Location:login.php");
    exit;
}

$teacher_email = $_SESSION['teacher_email'];

// Fetch teacher data from the database
$query = "SELECT * FROM teacher WHERE Email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $teacher_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $teacher = $result->fetch_assoc();
} else {
    echo "No teacher found with the given email.";
    exit;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>TEACHER-Home</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
   <link rel="stylesheet" href="teacher_dashboard/css/style.css">
</head>
<body>

<header class="header">
   <section class="flex">
      <a href="teacher_home.php"><img src="img/Logo.png" class="logo" style="height: 7vh; width: 45vh;" alt="SkillSphere"></a>
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
      <img src="img/teacher_pic.png" class="image" alt="">
      <h3 class="name"><?php echo htmlspecialchars($teacher['Full_name']); ?></h3>
      <p class="role">Teacher</p>
      <a href="teacher_dashboard/teacher_update.php" class="btn">Update Profile</a>
   </div>
   <nav class="navbar">
      <a href="teacher_home.php"><i class="fas fa-home"></i><span>Home</span></a>
      <div class="dropdown">
         <button class="dropbtn"><i class="fas fa-chalkboard-teacher"></i><span>My Courses</span></button>  
         <div class="dropdown-content">
            <a href="teacher_dashboard/upload_notes.php"><i class="fas fa-sticky-note"></i><span>Upload Notes</span></a>
            <a href="teacher_dashboard/view_tasks.php"><i class="fas fa-tasks"></i><span>View Task</span></a>
            <a href="teacher_dashboard/daily_classes.php"><i class="fas fa-video"></i><span>Daily Classes</span></a>
         </div>
      </div>
      <a href="teacher_dashboard/teacher_students.php"><i class="fas fa-desktop"></i><span>Students</span></a>
      <a href="teacher_dashboard/teacher_about.php"><i class="fas fa-question"></i><span>About Us</span></a>
      <a href="teacher_dashboard/teacher_contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
   </nav>
</div>

<section class="teacher-profile">
   <h1 class="heading">Profile Details</h1>
   <div class="details">
      <div class="tutor">
         <img src="img/teacher_pic.png" alt="">
         <h3><?php echo htmlspecialchars($teacher['Full_name']); ?></h3>
         <span>Teacher</span>
      </div>
      <div class="flex">
         <p>Email: <span><?php echo htmlspecialchars($teacher['Email']); ?></span></p>
         <p>Phone: <span><?php echo htmlspecialchars($teacher['Phone_number']); ?></span></p>
         <p>Subject: <span><?php echo htmlspecialchars($teacher['Subject_specialization']); ?></span></p>
         <p>Qualification: <span><?php echo htmlspecialchars($teacher['Highest_qualification']); ?></span></p>
         <p>Institute: <span><?php echo htmlspecialchars($teacher['Institute_name']); ?></span></p>
         <p>Experience: <span><?php echo htmlspecialchars($teacher['Years_of_experience']); ?> years</span></p>
      </div>
      <div class="user">
         <a href="teacher_dashboard/teacher_update.php" class="inline-btn">Update Profile</a>
      </div>
   </div>
</section>


<footer class="footer">
   &copy; SkillSphere 2024 <span></span> | All Rights Are Reserved !
</footer>

<script src="js/script.js"></script>
</body>
</html>
