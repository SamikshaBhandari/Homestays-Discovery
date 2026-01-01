<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['name'] : '';
$userEmail = $isLoggedIn ? $_SESSION['email'] : '';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ContactPage</title>
    <link rel="stylesheet"href="contact.css"/>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    />
  </head>
  <body>
    <header>
      <div class="image">
        <img src="images/logo.png" />
      </div>
      <div class="navigation">
        <a href="index1.php">Home</a>
        <a href="Homestay.php">Homestays</a>
         <a href="">Notification</a>
        <a href="Contact.php">Contact</a>
      </div>

      <div class="Login_container">
    <?php if ($isLoggedIn): 
        $email = trim($_SESSION['email'] ?? '');
        $gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower($email)) . "?d=mp&s=40";
    ?>
        <div style="display: flex; align-items: center; gap: 12px;">    
            <a href="profile.html" title="View Profile">
                <div style="width: 38px;
                 height: 38px;
                  border-radius: 50%;
                   overflow: hidden;
                    border: 2px solid gray;
                     display: flex;
                      align-items: center;
                       justify-content: center;">
                    <img src="<?php echo $gravatar_url; ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </a>
            <span style="color: gray;
             font-weight: bold; font-family: 'Roboto', sans-serif;">
                <?php echo htmlspecialchars($userName); ?>
            </span>
        </div>
    <?php else: ?>
        <div class="login">
          <a href="Login.html">Login</a>
        </div>
        <div class="Sign">
          <a href="Signup.html">Sign Up</a>
        </div>
    <?php endif; ?>
</div>
    </header>
    <div class="hero">
      <h1>Contact Us</h1>
      <p>
        Get in touch with our team for any questions about your Nepal adventure
      </p>
    </div>
    <section class="Box1">
      <div class="box2">
        <h1>Get In Touch</h1>
        <p>
          We're here to help you plan the perfect homestay experience in
          Nepal.<br />
          Reach out to us for personalized recommendations and booking
          assistance.
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
            <p><i class="fa-solid fa-location-dot"></i></p>
          </div>
          <div class="box5">
            <h3>Phone Number</h3>
            <p>+977-9842365501</p>
          </div>
        </div>
        <div class="box3">
          <div class="box4">
            <p><i class="fa-solid fa-location-dot"></i></p>
          </div>
          <div class="box5">
            <h3>Email Address</h3>
            <p>Homestay11@gmail.com</p>
          </div>
        </div>
      </div>

      <div class="Container1">
        <h1>Send us a Message</h1>
        <form>
          <div class="detail">
            <div class="name">
              <label for="Name">Full Name *</label><br />
              <input type="text" placeholder="Your full name" />
            </div>
            <div class="email">
              <label for="email">Email Address *</label><br />
              <input type="text" placeholder="Enter your email" />
            </div>
          </div>
          <div class="detail">
            <div class="name">
              <label for="Name">Phone Number *</label><br />
              <input type="text" placeholder="Your full name" />
            </div>
            <div class="email">
              <label for="Subject">Subject *</label><br />
              <select id="subject" name="subject">
                <option value="select">Select a Subject</option>
                <option value="select">Booking Inquiry</option>
                <option value="select">General Information</option>
                <option value="select">Feedback</option>
              </select>
            </div>
          </div>
          <div class="message">
            <label for="Message">Message *(Max 100 Characters)</label><br />
            <textarea
        placeholder="Tell us about your plans or any question you have..."
        maxlength="100"
      ></textarea>

          </div>
          <div class="btn">
            <button>Send message</button>
          </div>
        </form>
      </div>
    </section>
     <footer>
        <div class="main_section">
          <div class="media">
            <img src="Images/logo.png" />
            <p>
             Discover authentic Nepali hospitality through our carefully selected homestays.<br> Experience local culture, breathtaking landscapes, and unforgettable adventures.
            </p>
            <div class="icons">
              <button
                ><a class="fa-brands fa-facebook" style="color: blue"></a
              ></button>
              <button
                ><a class="fa-brands fa-instagram" style="color: red"></a
              ></button>
            </div>
          </div>
          <div class="link">
            <h2>Quick Links</h2>
            <div class="tags">
              <a href="#">Homestays</a>
              <a href="#">Contact Us</a>
              <a href="#">About Us</a>
            </div>
          </div>
          <div class="contact">
          <h2>Contact</h2>
          <div class="number">
            <p><i class="fa-solid fa-location-dot"></i>Sunsari ,Nepal</p>
            <p><i class="fa-solid fa-phone"></i>9742869769</p>
            <p><i class="fa-solid fa-envelope"></i>Travellocal2@gmail.com</p>
          </div>
        </div>
      </div>
      </div>
       <div class="copyright">
       <p> <i class="fa-regular fa-copyright"></i> 2025 TravelLocal Nepal. All
        rights reserved.</p>
       </div>
    </footer>
  </body>
</html>
