<?php
session_start(); // Start a PHP session

// Check if registration data is stored in session
if (!isset($_SESSION['registration_data'])) {
    // Redirect to registration.php if registration data is not found
    header("Location: student_dashboard/registration.php");
    exit; // Ensure no further code is executed
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skillsphere";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch registration data from session
$registration_data = $_SESSION['registration_data'];

// Assign registration data to variables
$first_name = $registration_data['first_name'];
$middle_name = $registration_data['middle_name'];
$last_name = $registration_data['last_name'];
$email = $registration_data['email'];
$course = $registration_data['course'];
$full_address = $registration_data['full_address'];
$student_mob = $registration_data['student_mob'];

// Fetch amount from courses table based on the course name
$sql = "SELECT price FROM courses WHERE name = '$course'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // If course found, fetch the price
    $row = $result->fetch_assoc();
    $payment_amount = $row['price'];
} else {
    // If course not found, set default payment amount to 0 or handle error accordingly
    $payment_amount = 0;
}


// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Payment Here</title>
    <link rel="stylesheet" href="css/payment_style.css">
    <style>
        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 8vh;
        }

        .logo {
            height: 10vh;
            width: 45vh;
        }
    </style>
</head>

<body>

    <!-- Navbar Start -->
    <header class="header">
        <section class="flex">
            <a href="index.php"><img src="img/Logo.png" class="logo" alt="SkillSphere"></a>
        </section>
    </header>
    <!-- Navbar End -->

    <div class="container">

        <form action="stripe_payment.php" method="POST" name="cardpayment" id="payment-form">

            <div class="row">

                <div class="col">
                    <h3 class="title">
                        Billing Details
                    </h3>

                    <div class="inputBox">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo $first_name . ' ' . $middle_name . ' ' . $last_name; ?>" readonly>
                    </div>

                    <div class="inputBox">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo $email; ?>" readonly>
                    </div>

                    <div class="inputBox">
                        <label for="course">Course:</label>
                        <input type="text" id="course" name="course" value="<?php echo $course; ?>" readonly>
                    </div>

                    <div class="inputBox">
                        <label for="price">Amount:</label>
                        <input type="text" id="price" name="price" value="<?php echo $payment_amount; ?>" readonly>
                    </div>

                    <div class="inputBox">
                        <label for="mobile">Mobile Number:</label>
                        <input type="text" id="mobile" name="mobile" value="<?php echo $student_mob; ?>" readonly>
                    </div>

                </div>
                <div class="col">
                    <h3 class="title">Payment</h3>

                    <div class="inputBox">
                        <label for="cardAccepted">Cards Accepted:</label>
                        <img src="img/Card_logo.png" alt="credit/debit card image">
                    </div>

                    <div class="inputBox">
                        <label for="cardName">Name On Card:</label>
                        <input type="text" id="cardName" name="card_name" placeholder="Enter name on card" required>
                    </div>

                    <div class="inputBox">
                        <label for="cardNum">Card Number:</label>
                        <input type="text" id="cardNum" name="card_number" placeholder="1111-2222-3333-4444" maxlength="19" required>
                    </div>

                    <div class="inputBox">
                        <label for="expMonth">Exp Month:</label>
                        <select name="exp_month" id="expMonth" required>
                            <option value="">Choose month</option>
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>


                    <div class="flex">
                        <div class="inputBox">
                            <label for="expYear">Exp Year:</label>
                            <select name="exp_year" id="expYear" required>
                                <option value="">Choose Year</option>
                                <option value="2023">2023</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                            </select>
                        </div>

                        <div class="inputBox">
                            <label for="cardCVC">CVV</label>
                            <input type="text" name="card_cvc" id="cardCVC" placeholder="CVC" autocomplete="cc-csc" required />
                        </div>
                    </div>

                </div>

            </div>
            <button type="button" id="payBtn" class="submit_btn">Proceed to Checkout</button>
        </form>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://js.stripe.com/v2/"></script>

    <script>

            // Set your publishable key
            Stripe.setPublishableKey('pk_test_51PN8lYAJdxKfYPBeUaaT58z01JtDqjmDYo1aHmsnxMkUGVOtuf5b77HBZU2jt6doFqlMsKVN8JTv6tSAgFjBcRKS00XddkTkMM');

            $("#payBtn").click(function() {
            // Disable the button to prevent multiple clicks
            $(this).prop("disabled", true);

            // Create a single-use token to charge the user
            Stripe.createToken({
                number: $('#cardNum').val(),
                cvc: $('#cardCVC').val(), // Updated selector for CVC input field
                exp_month: $('#expMonth').val(),
                exp_year: $('#expYear').val()
            }, stripeResponseHandler);

            // Prevent the form from submitting
            return false;
        });

        // Callback to handle the response from Stripe
        function stripeResponseHandler(status, response) {
            // Re-enable the button
            $('#payBtn').prop("disabled", false);

            if (response.error) {
                // Display the errors on the form
                $(".payment-status").html('<p>' + response.error.message + '</p>');
            } else {
                var form$ = $("#payment-form");
                // Get token id
                var token = response.id;
                // Insert the token into the form
                form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
                // Submit the form
                form$.get(0).submit();
            }
        }
    </script>

</body>

</html>