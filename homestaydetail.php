<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $_SESSION['user_id'] ?? null;
$userName = $isLoggedIn ? $_SESSION['name'] : '';
$userEmail = $isLoggedIn ? $_SESSION['email'] : '';
$userRole = $_SESSION['role'] ?? 'user'; 

include 'Backend/databaseconnection.php';

$homestayId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$homestayId) {
  http_response_code(404);
  echo "Invalid homestay id.";
  exit;
}

$stmt = $conn->prepare("SELECT homestay_id, name, location, rating, description, profile_image, host_name, price, user_id FROM homestays WHERE homestay_id = ? LIMIT 1");
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
$price = $homestay['price'] ?? 1500;
$creatorId = $homestay['user_id'];

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
    $galleryImgs[] = (strpos($img, 'images/') === 0) ? $img : 'images/' . $img;
}
$imgStmt->close();

if ($profileFile === '' && !empty($galleryImgs)) {
    $primaryImg = $galleryImgs[0];
    array_shift($galleryImgs);
}

$isAdminOrCreator = ($isLoggedIn && ($userRole === 'admin' || $userId == $creatorId));

function encodeImagePath($path) {
    return str_replace(' ', '%20', $path);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $name; ?> Homestay Detail</title>
    <link rel="stylesheet" href="./css/homestaydetail.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
    .admin_edit_box { 
        background: rgb(253, 253, 253); 
        padding: 15px; 
        border: 1px solid rgb(221, 221, 221); 
        border-radius: 8px; 
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); 
    }
    .admin_edit_box h3 { 
        margin-bottom: 12px; 
        font-size: 18px; 
        color: rgb(51, 51, 51); 
        border-bottom: 1px solid rgb(238, 238, 238); 
        padding-bottom: 5px; 
    }
    .edit_label { 
        font-size: 13px; 
        font-weight: bold; 
        color: rgb(85, 85, 85); 
        display: block; 
        margin-bottom: 3px; 
        text-align: left; 
    }
    .edit_input { 
        width: 100%; 
        padding: 10px; 
        margin-bottom: 12px; 
        border: 1px solid rgb(204, 204, 204); 
        border-radius: 5px; 
    }   
    .save_btn { 
        background: rgb(40, 167, 69) !important; 
        color: rgb(255, 255, 255) !important; 
        border: none; 
        padding: 12px; 
        width: 100%; 
        border-radius: 5px; 
        cursor: pointer; 
        font-weight: bold; 
        font-size: 15px; 
    } 
    .delete_btn { 
        background: rgb(220, 53, 69) !important; 
        color: rgb(255, 255, 255) !important; 
        border: none; 
        padding: 12px; 
        width: 100%; 
        border-radius: 5px; 
        cursor: pointer; 
        font-weight: bold; 
        margin-top: 8px; 
        font-size: 15px; 
    }
</style>
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
        <?php if ($isLoggedIn): 
            $email = trim($_SESSION['email'] ?? '');
            $gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower($email)) . "?d=mp&s=40";
        ?>
          <div style="display: flex; align-items: center; gap: 12px;">
            <a href="Backend/profile.php">
              <div style="width: 38px; height: 38px; border-radius: 50%; overflow: hidden; border: 2px solid gray;">
                <img src="<?php echo htmlspecialchars($gravatar_url); ?>" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            </a>
            <span style="color: gray; font-weight: bold;"><?php echo htmlspecialchars($userName); ?></span>
          </div>
        <?php else: ?>
          <div class="login"><a href="Login.html">Login</a></div>
          <div class="Sign"><a href="Signup.html">Sign Up</a></div>
        <?php endif; ?>
      </div>
    </header>

    <section class="main_container">
      <h1><?php echo $name; ?></h1>
      <div class="content">
        <div class="review"><p><i class="fa-solid fa-star" style="color: rgb(249, 220, 7)"></i> <?php echo $rating; ?> Review</p></div>
        <div class="location"><p><i class="fa-solid fa-location-dot"></i><?php echo $location; ?></p></div>
      </div>

      <section class="main_box">
        <div class="image_section">
          <div class="img1"><img src="<?php echo htmlspecialchars(encodeImagePath($primaryImg)); ?>" /></div>
          <div class="main_img">
            <?php if (!empty($galleryImgs)): ?>
              <?php foreach ($galleryImgs as $g): ?>
                <div class="img2"><img src="<?php echo htmlspecialchars(encodeImagePath($g)); ?>" /></div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="img2"><img src="<?php echo htmlspecialchars(encodeImagePath($primaryImg)); ?>" /></div>
              <div class="img2"><img src="<?php echo htmlspecialchars(encodeImagePath($primaryImg)); ?>" /></div>
              <div class="img2"><img src="<?php echo htmlspecialchars(encodeImagePath($primaryImg)); ?>" /></div>
              <div class="img2"><img src="<?php echo htmlspecialchars(encodeImagePath($primaryImg)); ?>" /></div>
            <?php endif; ?>
          </div>
        </div>

        <div class="reserve_box">
          <?php if ($isAdminOrCreator): ?>
            <div class="admin_edit_box">
                <h3><i class="fa fa-cog"></i> Manage Homestay</h3>
                <form action="Backend/update_homestay.php" method="POST">
                    <input type="hidden" name="homestay_id" value="<?php echo $homestayId; ?>">
                    <label class="edit_label">Homestay Name</label>
                    <input type="text" name="name" class="edit_input" value="<?php echo $name; ?>" required>
                    <label class="edit_label">Location</label>
                    <input type="text" name="location" class="edit_input" value="<?php echo $location; ?>" required>
                    <label class="edit_label">Price per Night (Rs.)</label>
                    <input type="number" name="price" class="edit_input" value="<?php echo $price; ?>" required>
                    <button type="submit" class="save_btn">Save Changes</button>
                    <button type="button" class="delete_btn" onclick="confirmDelete(<?php echo $homestayId; ?>)">Delete Homestay</button>
                </form>
            </div>
          <?php else: ?>
            <div class="checking">
                <div class="Box1"><p>Rs.<?php echo number_format($price); ?>/ night</p></div>
                <div class="Box2"><p><i class="fa-solid fa-star" style="color: rgb(249, 220, 7)"></i><?php echo $rating; ?></p></div>
            </div>
            <form id="ReserveBox" action="confirm_booking.php" method="GET">
              <input type="hidden" name="id" value="<?php echo $homestayId; ?>">
              <div class="check_detail">
                <div class="Box3"><label>Check in</label><input type="date" name="checkIn" required /></div>
                <div class="Box4"><label>Check out</label><input type="date" name="checkout" required /></div>
              </div>
              <div class="Guests">
                <label>Guests</label>
                <select name="guest" required>
                  <option value="1">1 guest</option><option value="2">2 guests</option><option value="3">3 guests</option><option value="4">4 guests</option>
                </select>
              </div>
              <div class="reserve_btn">
                <?php if ($isLoggedIn): ?><button type="submit">Reserve Now</button>
                <?php else: ?><button type="button" onclick="loginPrompt()">Reserve Now</button><?php endif; ?>
              </div>
              <a href="review.php?id=<?php echo $homestayId; ?>" class="write-review-link">
                <button type="button" class="write_review_btn_custom"><i class="fa fa-star"></i> Write a Review</button>
              </a>
            </form>
          <?php endif; ?>
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
            <div class="text2"><p><i class="fa-solid fa-check"></i>Free Wifi</p></div>
            <div class="text2"><p><i class="fa-solid fa-check"></i>Mountain View</p></div>
            <div class="text2"><p><i class="fa-solid fa-check"></i>Garden access</p></div>
          </div>
          <div class="facilities_content">
            <div class="text2"><p><i class="fa-solid fa-check"></i>Traditional Meals</p></div>
            <div class="text2"><p><i class="fa-solid fa-check"></i>Cultural Activities</p></div>
            <div class="text2"><p><i class="fa-solid fa-check"></i>Hot Water</p></div>
          </div>
        </div>

        <div class="available_room">
          <h2>Room Available</h2>
          <div class="Room_box">
            <p>Family Room</p>
            <div class="Room_No">
              <div class="room1"><p><i class="fa-regular fa-user"></i>4 Guests</p></div>
              <div class="room1"><p><i class="fa-solid fa-bed"></i>2 Double Beds</p></div>
              <div class="room1"><p><i class="fa-solid fa-door-closed"></i>Private Bathroom</p></div>
            </div>
          </div>
          <div class="Room_box" style="margin-top: 20px;">
            <p>Traditional Double Room</p>
            <div class="Room_No">
              <div class="room1"><p><i class="fa-regular fa-user"></i>2 Guests</p></div>
              <div class="room1"><p><i class="fa-solid fa-bed"></i>1 Double Bed</p></div>
              <div class="room1"><p><i class="fa-solid fa-door-closed"></i>Private Bathroom</p></div>
            </div>
          </div>
        </div>
      </section>
    </section>

    <footer>
      <div class="main_section">
        <div class="media">
          <img src="images/logo.png" />
          <p>Discover authentic Nepali hospitality through our homestays.<br>Experience local culture and breathtaking landscapes.</p>
          <div class="icons">
            <button><i class="fa-brands fa-facebook" style="color: blue"></i></button>
            <button><i class="fa-brands fa-instagram" style="color: red"></i></button>
          </div>
        </div>
        <div class="link">
          <h2>Quick Links</h2>
          <div class="tags"><a href="Homestay.php">Homestays</a><a href="Contact.php">Contact Us</a><a href="index1.php">Home</a></div>
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
      <div class="copyright"><p>© 2025 TravelLocal Nepal. All rights reserved.</p></div>
    </footer>

    <script>
      function loginPrompt() {
        if (confirm('You must login to book this homestay. Login now?')) {
            window.location.href = 'Login.html';
        }
      }
      function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this homestay? This action cannot be undone.')) {
            window.location.href = 'Backend/delete_homestay.php?id=' + id;
        }
      }
    </script>
</body>
</html>