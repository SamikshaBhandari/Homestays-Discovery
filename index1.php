<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $isLoggedIn ? $_SESSION['name'] : '';

include 'Backend/databaseconnection.php';

$featured_sql = "SELECT homestay_id, name, location, price, rating, profile_image
                 FROM homestays
                 WHERE is_featured = 1
                 ORDER BY rating DESC, homestay_id DESC
                 LIMIT 6";
$featured_result = mysqli_query($conn, $featured_sql);

if (!$featured_result || mysqli_num_rows($featured_result) === 0) {
    $featured_sql = "SELECT homestay_id, name, location, price, rating, profile_image
                     FROM homestays
                     ORDER BY rating DESC, homestay_id DESC
                     LIMIT 6";
    $featured_result = mysqli_query($conn, $featured_sql);
}

$test_sql = "SELECT rating, comment, guest_name, guest_country, guest_avatar
             FROM testimonials
             ORDER BY created_at DESC
             LIMIT 3";
$test_result = mysqli_query($conn, $test_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Homestays Discovery</title>
  <link rel="stylesheet" href="./css/index.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" />
</head>
<body>
  <header>
    <div class="image">
      <img src="images/logo.png" />
    </div>
    <div class="navigation">
      <a href="index1.php">Home</a>
      <a href="Homestay.php">Homestays</a>
      <a href="Contact.php">Contact</a>
      <a href="#">Notification</a>
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
    <h1>Discover Authentic Nepal</h1>
    <p>
      Experience genuine Nepali hospitality in carefully selected homestays.<br />
      Connect with local families, explore breathtaking landscapes, and create<br />
      unforgettable memories.
    </p>
    <div class="action-buttons">
      <a href="Homestay.php"><button>Find HomeStays</button></a>
    </div>
  </div>

  <div class="Container1">
    <h1>Featured Homestays</h1>
    <p>Stay with local families and experience authentic Nepali culture firsthand</p>
    
    <div class="Featured_destination">
      <?php if ($featured_result && mysqli_num_rows($featured_result) > 0): ?>
        <?php while ($f = mysqli_fetch_assoc($featured_result)):
          $img    = !empty($f['profile_image']) ? htmlspecialchars($f['profile_image']) : 'placeholder.jpg';
          $rating = (int)$f['rating'];
        ?>
        <div class="Destination1">
          <img src="images/<?php echo $img; ?>" alt="<?php echo htmlspecialchars($f['name']); ?>" />
          <div class="home">
            <div class="homestays_name">
              <h3><?php echo htmlspecialchars($f['name']); ?></h3>
            </div>
            <div class="rating-box">
              <span class="stars">
                <?php for ($i=0; $i<5; $i++): ?>
                  <i class="fa-solid fa-star" style="color: <?php echo $i < $rating ? 'rgb(249, 220, 7)' : 'lightgray'; ?>;"></i>
                <?php endfor; ?>
              </span>
              <span class="rating-value"><?php echo $rating; ?></span>
            </div>
          </div>
<p class="location-text">
  <i class="fa-solid fa-location-dot"></i>
  <?php echo htmlspecialchars($f['location']); ?>
</p>
          <div class="price">
            <div class="Rs">
              <p>Rs.<?php echo number_format((float)$f['price'], 2); ?>/Night</p>
            </div>
          </div>
          <a href="homestaydetail.php?id=<?php echo (int)$f['homestay_id']; ?>">
            <button>View Details</button>
          </a>
        </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="width:100%; text-align:center; color:#7f8c8d;">No featured homestays yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <section class="featured">
    <h1>Why Choose NepalStay?</h1>
    <p>We connect travelers with authentic homestay experiences across Nepal</p>
    <section class="discover-place">
      <div class="Discovery">
        <i class="fa-regular fa-house" style="color: rgb(52, 114, 240); background-color: rgb(214, 230, 245);"></i>
        <h3>Authentic Experiences</h3>
        <p>Stay with local families and experience genuine<br />Nepali culture, traditions, and hospitality</p>
      </div>
      <div class="Discovery">
        <i class="fas fa-shield-alt" style="color: rgb(91, 189, 91); background-color: rgb(207, 236, 207);"></i>
        <h3>Verified Homestays</h3>
        <p>All our homestays are carefully selected and <br />verified to ensure quality and safety standards</p>
      </div>
      <div class="Discovery">
        <i class="fa-regular fa-headphones" style="color: rgb(177, 80, 177); background-color: rgb(241, 219, 241);"></i>
        <h3>24/7 Support</h3>
        <p>Our local team provides round-the-clock support to ensure <br />your stay is comfortable and memorable</p>
      </div>
    </section>
  </section>

  <div class="community">
    <h1>What Our Guests Say</h1>
    <p>Read reviews from travelers who experienced authentic Nepal through our homestays</p>
    
    <div class="review">
      <?php if ($test_result && mysqli_num_rows($test_result) > 0): ?>
        <?php while ($t = mysqli_fetch_assoc($test_result)):
          $tRating = (int)$t['rating'];
          $avatar  = !empty($t['guest_avatar']) ? htmlspecialchars($t['guest_avatar']) : 'Images/default-avatar.png';
        ?>
        <div class="re-part">
          <div class="re-part1">
            <div class="re-img">
              <img src="<?php echo $avatar; ?>" alt="<?php echo htmlspecialchars($t['guest_name']); ?>" />
            </div>
            <div class="re-detail">
              <h3><?php echo htmlspecialchars($t['guest_name']); ?></h3>
              <p><?php echo htmlspecialchars($t['guest_country']); ?></p>
              <p>
                <?php for ($i=0; $i<5; $i++): ?>
                  <i class="fa-solid fa-star" style="color: <?php echo $i < $tRating ? 'rgb(249, 220, 7)' : '#ddd'; ?>;"></i>
                <?php endfor; ?>
              </p>
            </div>
          </div>
          <div class="re-info">
            <p>"<?php echo htmlspecialchars($t['comment']); ?>"</p>
          </div>
        </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="width:100%; text-align:center; color:#7f8c8d;">No testimonials yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="explore">
    <div class="explore1">
      <h1>Ready for Your Nepal Adventure?</h1>
      <p>Book your perfect homestay today and experience the warmth of Nepali hospitality</p>
      <div class="Explore_button">
        <a href="Homestay.php" class="hero-btn">Browse Homestays</a>
        <a href="Contact.php" class="hero-btn">Contact Us</a>
      </div>
    </div>
  </div>

  <footer>
    <div class="main_section">
      <div class="media">
        <img src="images/logo.png" alt="Logo" />
        <p>
          Discover authentic Nepali hospitality through our carefully selected homestays.<br />
          Experience local culture, breathtaking landscapes, and unforgettable adventures.
        </p>
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