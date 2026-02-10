<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']); 
$redirectSelf = rawurlencode($_SERVER['REQUEST_URI']);
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html?redirect={$redirectSelf}");
    exit;
}

$userName  = $_SESSION['name']  ?? '';
$userEmail = $_SESSION['email'] ?? '';

$homestay_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$homestay_id) {
    $homestay_id = filter_input(INPUT_POST, 'homestay_id', FILTER_VALIDATE_INT);
}
if (!$homestay_id && isset($_SESSION['selected_homestay_id'])) {
    $homestay_id = (int) $_SESSION['selected_homestay_id'];
}
if (!$homestay_id) {
    http_response_code(400);
    echo "Homestay not specified. Please go back and select a homestay.";
    exit;
}
$pre_checkin  = $_GET['checkIn'] ?? '';
$pre_checkout = $_GET['checkout'] ?? '';
$pre_guest    = $_GET['guest'] ?? 1;

require 'Backend/databaseconnection.php';

$stmt = $conn->prepare("SELECT homestay_id, name, location, price, profile_image FROM homestays WHERE homestay_id = ? LIMIT 1");
$stmt->bind_param("i", $homestay_id);
$stmt->execute();
$homestay = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$homestay) {
    http_response_code(404);
    echo "Homestay not found.";
    exit;
}

$homestay_name= htmlspecialchars($homestay['name']);
$homestay_location= htmlspecialchars($homestay['location']);
$price_per_night= (float) $homestay['price'];
$profile_image= $homestay['profile_image'] ? 'images/' . $homestay['profile_image'] : 'images/logo.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Complete Booking  <?php echo $homestay_name; ?></title>
  <link rel="stylesheet" href="./css/confirm_booking.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
</head>
<body>
<header>
  <div class="image"><img src="images/logo.png" alt="Logo" /></div>
  <div class="navigation">
    <a href="index1.php">Home</a>
    <a href="Homestay.php">Homestays</a>
    <?php if ($isLoggedIn): ?>
            <a href="Backend/my_bookings.php">My Bookings</a>
        <?php endif; ?>
    <a href="Contact.php">Contact</a>
  </div>
  <div class="Login_container">
    <?php $gravatar = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($userEmail))) . "?d=mp&s=40"; ?>
    <div style="display:flex;align-items:center;gap:12px;">
      <a href="Backend/profile.php" title="View Profile">
        <div style="width:38px;height:38px;border-radius:50%;overflow:hidden;border:2px solid gray;display:flex;align-items:center;justify-content:center;">
          <img src="<?php echo htmlspecialchars($gravatar); ?>" alt="Profile" style="width:100%;height:100%;object-fit:cover;">
        </div>
      </a>
      <span style="color:gray;font-weight:bold;font-family:'Roboto',sans-serif;"><?php echo htmlspecialchars($userName); ?></span>
    </div>
  </div>
</header>

<section class="Reserve_box">
  <div class="text_booking">
    <a href="homestaydetail.php?id=<?php echo $homestay_id; ?>"><i class="fa-solid fa-arrow-left"></i> Back</a>
    <h3>Complete your booking</h3>
  </div>
  
  <div class="Reserve_box1">
    <div class="reserve_form">
      <form id="Booking" action="Backend/booking.php" method="POST">
        <input type="hidden" name="homestay_id" value="<?php echo $homestay_id; ?>">
        <h3>Booking Details</h3>
        <div class="Book_detl">
          <div class="Check_in">
            <label for="checkin">Check-in Date *</label><br/>
            <input type="date" id="checkin" name="checkIn" value="<?php echo $pre_checkin; ?>" required min="<?php echo date('Y-m-d'); ?>" onchange="calculateTotal()"/>
          </div>  
          <div class="nights">
            <label for="checkout">Check-out Date *</label><br/>
            <input type="date" id="checkout" name="checkout" value="<?php echo $pre_checkout; ?>" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" onchange="calculateTotal()"/>
          </div>
        </div>
        <div class="noofguest">
          <label for="guest">Number of Guests *</label><br/>
          <input type="number" id="guest" name="guest" min="1" max="10" value="<?php echo $pre_guest; ?>" required onchange="calculateTotal()"/>
        </div>
        <h3>Your Information</h3>
        <div class="your_info">
          <div class="full_name">
            <label for="fname">Full Name *</label><br/>
            <input type="text" id="Fname" name="name" value="<?php echo htmlspecialchars($userName); ?>" required/>
          </div>
          <div class="Email">
            <label for="email">Email Address *</label><br/>
            <input type="email" id="Email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" required/>
          </div>
        </div>
        <div class="phone">
          <label for="phone">Phone Number *</label><br/>
          <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" required/>
        </div>
        <div class="policy">
          <p><i class="fa-solid fa-circle-info"></i> Booking Policy: Your booking will be confirmed within 24 hours. 
            Cancellations made 7 days before check-in are eligible for full refund.</p>
        </div>
        <div class="confirm">
          <button class="confirm-btn" type="submit"><i class="fa-solid fa-check"></i> Confirm Booking</button>
        </div>
      </form>
    </div>

    <div class="booking_summary">
      <h3>Booking Summary</h3>
      <div class="booking_img">
        <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="<?php echo $homestay_name; ?>" />
      </div>
      <div class="homes_name">
        <h3><?php echo $homestay_name; ?></h3>
        <p><i class="fa-solid fa-location-dot"></i> <?php echo $homestay_location; ?></p>
      </div>
      <div class="price_detail">
        <div class="price1"><p>Price per Night</p></div>
        <div class="money"><p id="Money">Rs. <?php echo number_format($price_per_night, 2); ?></p></div>
      </div>
      <div class="price_detail">
        <div class="price1"><p>Number of Nights</p></div>
        <div class="money"><p id="Numofnight">0</p></div>
      </div>
      <div class="price_detail">
        <div class="price1"><p>Number of Guests</p></div>
        <div class="money"><p id="numofguest">1</p></div>
      </div>
      <div class="price_detail">
        <div class="price1"><p>Subtotal</p></div>
        <div class="money"><p id="SubTotal">Rs. 0.00</p></div>
      </div>
      <div class="total_amount">
        <div class="amount"><p>Total</p></div>
        <div class="amount"><p id="total_Amount">Rs. 0.00</p></div>
      </div>
      <div class="Secure"><p><i class="fa-solid fa-shield-halved"></i> Secure booking</p></div>
    </div>
  </div>
</section>

<footer>
  <div class="main_section">
    <div class="media">
      <img src="images/logo.png" alt="Logo" />
      <p>Discover authentic Nepali hospitality through our carefully selected homestays.<br>
         Experience local culture, breathtaking landscapes, and unforgettable adventures.</p>
      <div class="icons">
        <button><i class="fa-brands fa-facebook" style="color: blue"></i></button>
        <button><i class="fa-brands fa-instagram" style="color: red"></i></button>
      </div>
    </div>
    <div class="link">
      <h2>Quick Links</h2>
      <div class="tags">
        <a href="Homestay.php">Homestays</a>
        <a href="Contact.php">Contact Us</a>
        <a href="index1.php">Home</a>
      </div>
    </div>
    <div class="contact">
      <h2>Contact</h2>
      <div class="number">
        <p><i class="fa-solid fa-location-dot"></i> Sunsari, Nepal</p>
        <p><i class="fa-solid fa-phone"></i> 9742869769</p>
        <p><i class="fa-solid fa-envelope"></i> Travellocal2@gmail.com</p>
      </div>
    </div>
  </div>
  <div class="copyright">
    <p><i class="fa-regular fa-copyright"></i> 2025 TravelLocal Nepal. All rights reserved.</p>
  </div>
</footer>

<script>
  const pricePerNight = <?php echo $price_per_night; ?>;
  function calculateTotal() {
    const checkin = document.getElementById('checkin').value;
    const checkout = document.getElementById('checkout').value;
    const guests = parseInt(document.getElementById('guest').value) || 1;
    document.getElementById('numofguest').textContent = guests;
    if (checkin && checkout) {
      const nights = Math.ceil((new Date(checkout) - new Date(checkin)) / (1000*60*60*24));
      if (nights > 0) {
        const subtotal = nights * pricePerNight;
        document.getElementById('Numofnight').textContent = nights;
        document.getElementById('SubTotal').textContent   = 'Rs. ' + subtotal.toFixed(2);
        document.getElementById('total_Amount').textContent = 'Rs. ' + subtotal.toFixed(2);
        return;
      }
    }
    resetCalc();
  }
  function resetCalc() {
    document.getElementById('Numofnight').textContent = '0';
    document.getElementById('SubTotal').textContent   = 'Rs. 0.00';
    document.getElementById('total_Amount').textContent = 'Rs. 0.00';
  }
  document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
</body>
</html>
<?php $conn->close(); ?>