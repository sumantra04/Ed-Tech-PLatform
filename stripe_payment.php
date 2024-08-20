<?php 
session_start(); // Start a PHP session

include "dbase.php";

$payment_id = $statusMsg = ''; 
$ordStatus = 'error';
$id = '';

// Check whether stripe token is not empty
if(!empty($_POST['stripeToken'])){
    // Get Token, Card and User Info from Form
    $token = $_POST['stripeToken'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $card_no = $_POST['card_number'];
    $card_cvc = $_POST['card_cvc'];
    $card_exp_month = $_POST['exp_month'];
    $card_exp_year = $_POST['exp_year'];
    $price = $_POST['price'];

    // Include STRIPE PHP Library
    require_once('stripe-php/init.php');

    // set API Key
    $stripe = array(
        "SecretKey"=>"sk_test_51PN8lYAJdxKfYPBevEgUQEONfWl5vLOwt9xFsLWP4SyG5z0RIbvheLD0AuPLfKJadZX8QyFsBq5eLJ943Wnlphhm002pLZt27m",
        "PublishableKey"=>"pk_test_51PN8lYAJdxKfYPBeUaaT58z01JtDqjmDYo1aHmsnxMkUGVOtuf5b77HBZU2jt6doFqlMsKVN8JTv6tSAgFjBcRKS00XddkTkMM"
    );

    // Set your secret key
    \Stripe\Stripe::setApiKey($stripe['SecretKey']);

    // Add customer to stripe 
    $customer = \Stripe\Customer::create(array( 
        'email' => $email, 
        'source'  => $token,
        'name' => $name,
        'description'=>$course
    ));

    // Generate Unique order ID 
    $orderID = strtoupper(str_replace('.','',uniqid('', true)));
     
    // Convert price to cents 
    $itemPrice = ($price*100);
    $currency = "usd";

    // Charge a credit or a debit card 
    $charge = \Stripe\Charge::create(array( 
        'customer' => $customer->id, 
        'amount'   => $itemPrice, 
        'currency' => $currency, 
        'description' => $course, 
        'metadata' => array( 
            'order_id' => $orderID 
        ) 
    ));

    // Retrieve charge details 
    $chargeJson = $charge->jsonSerialize();

    // Check whether the charge is successful 
    if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1){ 
        // Order details 
        $transactionID = $chargeJson['id']; 
        $paidAmount = $chargeJson['amount']; 
        $paidCurrency = $chargeJson['currency']; 
        $payment_status = $chargeJson['status'];
        $payment_date = date("Y-m-d H:i:s");
        $dt_tm = date('Y-m-d H:i:s');

        // Insert transaction data into the database
        $sql = "INSERT INTO payments (name, email, coursename, fees, card_number, card_expirymonth, card_expiryyear, status, paymentid, added_date) VALUES ('$name', '$email', '$course', '$price', '$card_no', '$card_exp_month', '$card_exp_year', '$payment_status', '$transactionID', '$dt_tm')";
        mysqli_query($conn, $sql) or die("Mysql Error Stripe-Charge(SQL)" . mysqli_error($conn));

        // If the order is successful 
        if($payment_status == 'succeeded'){ 
            $ordStatus = 'Success'; 
            $statusMsg = 'Your Payment has been Successful!'; 
        } else{ 
            $statusMsg = "Your Payment has Failed!"; 
        } 
    } else{ 
        $statusMsg = "Transaction has been failed!"; 
    } 
} else{ 
    $statusMsg = "Error on form submission."; 
}
 // Close connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Status</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/stripe.css">
</head>
<body>
    <div class="container">
        <h2 class="<?php echo $ordStatus; ?>"><?php echo $statusMsg; ?></h2>
        <div class="payment-info">
            <p><b>Transaction ID:</b> <?php echo $transactionID; ?></p>
            <p><b>Paid Amount:</b> <?php echo $paidAmount . ' ' . $paidCurrency; ?> ($<?php echo $price;?>.00)</p>
            <p><b>Payment Status:</b> <?php echo $payment_status; ?></p>
            <h4 class="heading"> Course Details </h4>
					<br>
					<p><b>Course Name:</b> <?php echo $course; ?></p>
					<p><b>Course Price:</b> <?php echo $price.' '.$currency; ?> ($<?php echo $price;?>.00)</p>
				</div>
        </div>
        <a href="login.php" class="btn-continue">Login</a>
    </div>
</body>
</html>