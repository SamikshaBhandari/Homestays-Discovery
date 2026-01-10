<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['name'] : '';
$userEmail = $isLoggedIn ? $_SESSION['email'] : '';

include 'Backend/databaseconnection.php';

$homestayId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$homestayId) {
  http_response_code(404);
  echo "Invalid homestay id.";
  exit;
}

$stmt = $conn->prepare("SELECT homestay_id, name, location, rating, description, profile_image, host_name FROM homestays WHERE homestay_id = ? LIMIT 1");
$stmt->bind_param("i", $homestayId);
$stmt->execute();
$res = $stmt->get_result();
$homestay = $res->fetch_assoc();
$stmt->close();

if (!$homestay) {
  http_response_code(404);
  echo "Homestay not found.";
  exit;
}
$name = htmlspecialchars($homestay['name'] ?? 'Homestay');
$rating = htmlspecialchars($homestay['rating'] ?? 'N/A');
$location = htmlspecialchars($homestay['location'] ?? 'Unknown location');
$description = nl2br(htmlspecialchars($homestay['description'] ?? 'No description provided.'));
$hostName = htmlspecialchars($homestay['host_name'] ?? 'Host');

$profileFile = trim($homestay['profile_image'] ?? '');
$primaryImg = $profileFile !== '' ? 'images/' . $profileFile : 'images/logo.png';

$galleryImgs = [];
$imgStmt = $conn->prepare("SELECT image FROM homestay_images WHERE homestay_id = ? ORDER BY image_id ASC");
$imgStmt->bind_param("i", $homestayId);
$imgStmt->execute();
$imgRes = $imgStmt->get_result();
while ($imgRow = $imgRes->fetch_assoc()) {
    $img = trim($imgRow['image'] ?? '');
    if ($img === '') continue;
    
    if (strpos($img, 'images/') === 0) {
        $galleryImgs[] = $img;
    } else {
        $galleryImgs[] = 'images/' . $img;
    }
}
$imgStmt->close();

if ($profileFile === '' && !empty($galleryImgs)) {
    $primaryImg = $galleryImgs[0];
    array_shift($galleryImgs);
}

$galleryImgs = array_values(array_filter($galleryImgs, function($g) use ($primaryImg) {
    return $g !== $primaryImg;
}));

function encodeImagePath($path) {
    return str_replace(' ', '%20', $path);
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $name; ?> - Homestay Detail</title>
    <link rel="stylesheet" href="./css/homestaydetail.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  </head>
  <body>
    <header>
      <div class="image">
        <img src="images/logo.png" alt="Logo" />
      </div>
      <div class="navigation">
        <a href="index1.php">Home</a>
        <a href="Homestay.php">Homestays</a>
        <a href="#">Notification</a>
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
                <img src="<?php echo htmlspecialchars($gravatar_url); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            </a>
            <span style="color: gray; font-weight: bold; font-family: 'Roboto', sans-serif;">
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

    <section class="main_container">
      <h1><?php echo $name; ?></h1>
      <div class="content">
        <div class="review">
          <p>
            <i class="fa-solid fa-star" style="color: rgb(249, 220, 7)"></i>
            <?php echo $rating; ?> Review
          </p>
        </div>
        <div class="location">
          <p><i class="fa-solid fa-location-dot"></i><?php echo $location; ?></p>
        </div>
      </div>

      <section class="main_box">
        <div class="image_section">
          <div class="img1">
            <img src="<?php echo htmlspecialchars(encodeImagePath($primaryImg)); ?>" alt="<?php echo $name; ?>" />
          </div>
          <div class="main_img">
            <?php if (!empty($galleryImgs)): ?>
              <?php foreach ($galleryImgs as $g): ?>
                <div class="img2">
                  <img src="<?php echo htmlspecialchars(encodeImagePath($g)); ?>" alt="<?php echo $name; ?>" />
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="img2">
                <img src="<?php echo htmlspecialchars(encodeImagePath($primaryImg)); ?>" alt="<?php echo $name; ?>" />
              </div>
              <div class="img2">
                <img src="<?php echo htmlspecialchars(encodeImagePath($primaryImg)); ?>" alt="<?php echo $name; ?>" />
              </div>
              <div class="img2">
                <img src="<?php echo htmlspecialchars(encodeImagePath($primaryImg)); ?>" alt="<?php echo $name; ?>" />
              </div>
              <div class="img2">
                <img src="<?php echo htmlspecialchars(encodeImagePath($primaryImg)); ?>" alt="<?php echo $name; ?>" />
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="reserve_box">
          <div class="checking">
            <div class="Box1">
              <p>Rs.1500/ night</p>
            </div>
            <div class="Box2">
              <p>
                <i class="fa-solid fa-star" style="color: rgb(249, 220, 7)"></i><?php echo $rating; ?>
              </p>
            </div>
          </div>
          <form id="ReserveBox" action="confirm_booking.php" method="POST">
            <input type="hidden" name="homestay_id" value="<?php echo (int)$homestay['homestay_id']; ?>">
            <div class="check_detail">
              <div class="Box3">
                <label for="checkIn">Check in</label><br />
                <input type="date" id="checkIn" name="checkIn" required />
              </div>
              <div class="Box4">
                <label for="checkout">Check out</label><br />
                <input type="date" id="checkout" name="checkout" required />
              </div>
            </div>
            <div class="Guests">
              <label for="guest">Guests</label><br />
              <select id="guest" name="guest" required>
                <option value="1">1 guest</option>
                <option value="2">2 guests</option>
                <option value="3">3 guests</option>
                <option value="4">4 guests</option>
                <option value="5">5+ guests</option>
              </select>
            </div>
            <div class="reserve_btn">
              <button type="submit">Reserve Now</button>
            </div>
          </form>
        </div>
      </section>

      <section class="text_box">
        <div class="text1">
          <h2>About this Place</h2>
          <p><?php echo $description; ?></p>
        </div>

        <div class="facilities">
          <h2>Facilities</h2>
          <div class="facilities_content">
            <div class="text2">
              <p><i class="fa-solid fa-check"></i>Free Wifi</p>
            </div>
            <div class="text2">
              <p><i class="fa-solid fa-check"></i>Mountain View</p>
            </div>
            <div class="text2">
              <p><i class="fa-solid fa-check"></i>Garden access</p>
            </div>
          </div>
          <div class="facilities_content">
            <div class="text2">
              <p><i class="fa-solid fa-check"></i>Traditional Meals</p>
            </div>
            <div class="text2">
              <p><i class="fa-solid fa-check"></i>Cultural Activities</p>
            </div>
            <div class="text2">
              <p><i class="fa-solid fa-check"></i>Hot Water</p>
            </div>
          </div>
        </div>

        <div class="available_room">
          <h2>Room Available</h2>
          <div class="Room_box">
            <p>Family Room</p>
            <div class="Room_No">
              <div class="room1">
                <p><i class="fa-regular fa-user"></i>4 Guests</p>
              </div>
              <div class="room1">
                <p><i class="fa-solid fa-bed"></i>2 Double Beds</p>
              </div>
              <div class="room1">
                <p><i class="fa-solid fa-door-closed"></i>Private Bathroom</p>
              </div>
            </div>
          </div>
        </div>
        <div class="available_room">
          <h2>Traditional Double Room</h2>
          <div class="Room_box">
            <p>Family Room</p>
            <div class="Room_No">
              <div class="room1">
                <p><i class="fa-regular fa-user"></i>2 Guests</p>
              </div>
              <div class="room1">
                <p><i class="fa-solid fa-bed"></i>1 Double Bed</p>
              </div>
              <div class="room1">
                <p><i class="fa-solid fa-door-closed"></i>Private Bathroom</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </section>

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
            <a href="#">About Us</a>
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