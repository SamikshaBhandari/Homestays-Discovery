<?php
session_start();
include 'Backend/databaseconnection.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['name'] : '';
$userEmail = $isLoggedIn ? $_SESSION['email'] : '';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_message'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $subject = trim($_POST['subject']);
        $message = trim($_POST['message']);

        if (!empty($full_name) && !empty($email) && !empty($phone) && !empty($subject) && !empty($message)) {
            $full_name = $conn->real_escape_string($full_name);
            $email = $conn->real_escape_string($email);
            $phone = $conn->real_escape_string($phone);
            $subject = $conn->real_escape_string($subject);
            $message = $conn->real_escape_string($message);

            $sql = "INSERT INTO contact_messages (full_name, email, phone, subject, message)
                    VALUES ('$full_name', '$email', '$phone', '$subject', '$message')";

            if (mysqli_query($conn, $sql)) {
                $success_msg = 'Thank you! Your message has been sent successfully. We will get back to you soon.';
            } else {
                $error_msg = 'Error: ' . mysqli_error($conn);
            }
        } else {
            $error_msg = 'Please fill all required fields.';
        }
    }
    elseif (isset($_POST['submit_review'])) {
        $guest_name = trim($_POST['guest_name']);
        $guest_country = trim($_POST['guest_country']);
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        $guest_avatar = 'Images/default-avatar.png';

        if (!empty($guest_name) && !empty($comment) && $rating >= 1 && $rating <= 5) {
            $guest_name = $conn->real_escape_string($guest_name);
            $guest_country = $conn->real_escape_string($guest_country);
            $comment = $conn->real_escape_string($comment);

            $sql = "INSERT INTO testimonials (rating, comment, guest_name, guest_country, guest_avatar)
                    VALUES ($rating, '$comment', '$guest_name', '$guest_country', '$guest_avatar')";

            if (mysqli_query($conn, $sql)) {
                $success_msg = 'Thank you! Your review has been submitted successfully.';
            } else {
                $error_msg = 'Error: ' . mysqli_error($conn);
            }
        } else {
            $error_msg = 'Please fill all required fields and select a rating.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact & Review</title>
    <link rel="stylesheet" href="./css/contact.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <style>
      .alert {
  padding: 15px 20px;
  margin: 20px auto;
  border-radius: 8px;
  max-width: 800px;
  text-align: center;
  font-weight: 600;
  font-size: 16px;
}

.alert-success {
  background: rgb(212, 237, 218);
  color: rgb(21, 87, 36);
  border: 2px solid rgb(195, 230, 203);
}

.alert-error {
  background: rgb(248, 215, 218);
  color: rgb(114, 28, 36);
  border: 2px solid rgb(245, 198, 203);
}

.star-rating {
  display: flex;
  gap: 10px;
  font-size: 32px;
  justify-content: center;
  margin: 20px 0;
  flex-direction: row-reverse;
}

.star-rating input {
  display: none;
}

.star-rating label {
  cursor: pointer;
  color: rgb(186, 182, 182);
  transition: color 0.2s ease;
}

.star-rating label:hover,
.star-rating label:hover label {
  color: rgb(249, 220, 7);
}

.review-section {
  padding: 60px 20px;
  background: rgb(154, 206, 241);
  margin-top: 50px;
}

.review-container {
  max-width: 700px;
  margin: 0 auto;
  background: rgb(255, 255, 255);
  padding: 40px;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.review-container h1 {
  color: rgb(44, 62, 80);
  text-align: center;
  margin-bottom: 10px;
}

.review-container p {
  text-align: center;
  color: rgb(128, 128, 128);
  margin-bottom: 30px;
}

.review-btn {
  background: rgb(135, 206, 235);
  color: rgb(255, 255, 255) !important;
  font-weight: 700;
  padding: 14px 30px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.3s ease;
}

.review-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}
    </style>
  </head>
  <body>
    <header>
      <div class="image">
        <img src="images/logo.png" />
      </div>
      <div class="navigation">
  <a href="index1.php">Home</a>
  <a href="Homestay.php">Homestays</a>
  <?php if ($isLoggedIn): ?>
    <a href="Backend/my_bookings.php">My Bookings</a>
  <?php endif; ?>
  <a href="Contact.php">Contact</a>
</div>

      <div class="Login_container">
        <?php if ($isLoggedIn): 
          $email = trim($_SESSION['email'] ?? '');
          $gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower($email)) . "?d=mp&s=40";
        ?>
          <div style="display: flex; align-items: center; gap: 12px;">    
            <a href="Backend/profile.php" title="View Profile">
              <div style="width: 38px; height: 38px; border-radius: 50%; overflow: hidden; border: 2px solid gray; display: flex; align-items: center; justify-content: center;">
                <img src="<?php echo $gravatar_url; ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            </a>
            <span style="color: gray; font-weight: bold; font-family: 'Roboto', sans-serif;">
              <?php echo htmlspecialchars($userName); ?>
            </span>
          </div>
        <?php else: ?>
          <div class="login"><a href="Login.html">Login</a></div>
          <div class="Sign"><a href="Signup.html">Sign Up</a></div>
        <?php endif; ?>
      </div>
    </header>

    <div class="hero">
      <h1>Contact Us & Share Your Experience</h1>
      <p>Get in touch with our team or share your homestay review</p>
    </div>

    <?php if ($success_msg): ?>
      <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    
    <?php if ($error_msg): ?>
      <div class="alert alert-error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <section class="Box1">
      <div class="box2">
        <h1>Get In Touch</h1>
        <p>
          We're here to help you plan the perfect homestay experience in Nepal.<br />
          Reach out to us for personalized recommendations and booking assistance.
        </p>
        <div class="box3">
          <div class="box4">
            <p><i class="fa-solid fa-location-dot"></i></p>
          </div>
          <div class="box5">
            <h3>Office Address</h3>
            <p>Itahari-Sunsari, Nepal</p>
          </div>
        </div>
        <div class="box3">
          <div class="box4">
            <p><i class="fa-solid fa-phone"></i></p>
          </div>
          <div class="box5">
            <h3>Phone Number</h3>
            <p>+977-9842365501</p>
          </div>
        </div>
        <div class="box3">
          <div class="box4">
            <p><i class="fa-solid fa-envelope"></i></p>
          </div>
          <div class="box5">
            <h3>Email Address</h3>
            <p>Homestay11@gmail.com</p>
          </div>
        </div>
      </div>

      <div class="Container1">
        <h1>Send us a Message</h1>
        <form method="POST" action="">
          <div class="detail">
            <div class="name">
              <label for="Name">Full Name *</label>
              <input type="text" name="full_name" placeholder="Your full name" required />
            </div>
            <div class="email">
              <label for="email">Email Address *</label>
              <input type="email" name="email" placeholder="Enter your email" required />
            </div>
          </div>
          
          <div class="detail">
            <div class="name">
              <label for="Phone">Phone Number *</label>
              <input type="text" name="phone" placeholder="Your phone number" required />
            </div>
            <div class="email">
              <label for="Subject">Subject *</label>
              <select name="subject" required>
                <option value="" disabled selected>Select a Subject</option>
                <option value="booking">Booking Inquiry</option>
                <option value="info">General Information</option>
                <option value="feedback">Feedback</option>
              </select>
            </div>
          </div>

          <div class="message">
            <label for="Message">Message *(Max 500 Characters)</label>
            <textarea name="message" placeholder="Tell us about your plans..." maxlength="500" required></textarea>
          </div>

          <div class="btn">
            <button type="submit" name="submit_message">Send message</button>
          </div>
        </form>
      </div>
    </section>

    <section class="review-section">
      <div class="review-container">
        <h1><i class="fa fa-star" style="color: rgb(249, 220, 7);"></i> Share Your Experience</h1>
        <p>Help others discover authentic Nepal through your review</p>
        
        <form method="POST" action="">
          <div class="detail">
            <div class="name">
              <label>Your Name *</label>
              <input type="text" name="guest_name" placeholder="Enter your name" required />
            </div>
            <div class="email">
              <label>Country (Optional)</label>
              <input type="text" name="guest_country" placeholder="e.g., Japan, USA" />
            </div>
          </div>

          <div style="text-align: center; margin: 25px 0;">
            <label style="display: block; font-weight: 700; margin-bottom: 15px; color: #2c3e50; font-size: 18px;">
              Rate Your Experience *
            </label>
            <div class="star-rating">
              <input type="radio" id="star5" name="rating" value="5" required />
              <label for="star5"><i class="fa-solid fa-star"></i></label>
              <input type="radio" id="star4" name="rating" value="4" />
              <label for="star4"><i class="fa-solid fa-star"></i></label>
              <input type="radio" id="star3" name="rating" value="3" />
              <label for="star3"><i class="fa-solid fa-star"></i></label>
              <input type="radio" id="star2" name="rating" value="2" />
              <label for="star2"><i class="fa-solid fa-star"></i></label>
              <input type="radio" id="star1" name="rating" value="1" />
              <label for="star1"><i class="fa-solid fa-star"></i></label>
            </div>
          </div>

          <div class="message">
            <label>Your Review *</label>
            <textarea name="comment" placeholder="Share your homestay experience with us..." rows="6" required></textarea>
          </div>

          <div class="btn" style="text-align: center;">
            <button type="submit" name="submit_review" class="review-btn">
              <i class="fa fa-paper-plane"></i> Submit Review
            </button>
          </div>
        </form>
      </div>
    </section>

    <footer>
      <div class="main_section">
        <div class="media">
          <img src="Images/logo.png" />
          <p>
            Discover authentic Nepali hospitality through our carefully selected homestays.<br />
            Experience local culture, breathtaking landscapes, and unforgettable adventures.
          </p>
          <div class="icons">
            <button><a class="fa-brands fa-facebook" style="color: blue"></a></button>
            <button><a class="fa-brands fa-instagram" style="color: red"></a></button>
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
            <p><i class="fa-solid fa-location-dot"></i>Sunsari, Nepal</p>
            <p><i class="fa-solid fa-phone"></i>9742869769</p>
            <p><i class="fa-solid fa-envelope"></i>Travellocal2@gmail.com</p>
          </div>
        </div>
      </div>
      <div class="copyright">
        <p><i class="fa-regular fa-copyright"></i> 2025 TravelLocal Nepal. All rights reserved.</p>
      </div>
    </footer>
  </body>
</html>
<?php mysqli_close($conn); ?>