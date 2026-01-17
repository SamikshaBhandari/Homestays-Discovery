<?php
session_start();
include 'Backend/databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.html?redirect=review.php?id=' . (isset($_GET['id']) ? (int)$_GET['id'] : ''));
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['name'] : '';

$homestay_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($homestay_id <= 0) {
    header('Location: Homestay.php');
    exit;
}

$stmt = $conn->prepare("SELECT name, location FROM homestays WHERE homestay_id = ?");
$stmt->bind_param("i", $homestay_id);
$stmt->execute();
$result = $stmt->get_result();
$homestay = $result->fetch_assoc();

if (!$homestay) {
    header('Location: Homestay.php');
    exit;
}

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $guest_name = trim($_POST['guest_name']);
    $guest_country = trim($_POST['guest_country']);
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);
    $guest_avatar = 'Images/default-avatar.png';

    if (!empty($guest_name) && !empty($comment) && $rating >= 1 && $rating <= 5) {
        $guest_name = $conn->real_escape_string($guest_name);
        $guest_country = $conn->real_escape_string($guest_country);
        $comment = $conn->real_escape_string($comment);

        $sql = "INSERT INTO testimonials (homestay_id, rating, comment, guest_name, guest_country, guest_avatar)
                VALUES ($homestay_id, $rating, '$comment', '$guest_name', '$guest_country', '$guest_avatar')";

        if (mysqli_query($conn, $sql)) {
            $success_msg = 'Thank you! Your review has been submitted successfully.';
        } else {
            $error_msg = 'Error: '. mysqli_error($conn);
        }
    } else {
        $error_msg = 'Please fill all required fields and select a rating.';
    }
}

$reviews_sql = "SELECT rating, comment, guest_name, guest_country, guest_avatar, created_at
                FROM testimonials
                WHERE homestay_id = $homestay_id
                ORDER BY created_at DESC";
$reviews_result = mysqli_query($conn, $reviews_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Review  <?php echo htmlspecialchars($homestay['name']); ?></title>
    <link rel="stylesheet" href="./css/review.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
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
            <a href="Notifications.php">Notifications</a>
        </div>
        <div class="Login_container">
            <?php if ($isLoggedIn): 
                $email = trim($_SESSION['email'] ?? '');
                $gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower($email)) . "?d=mp&s=40";
            ?>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <a href="Backend/profile.php">
                        <div style="width: 38px; height: 38px; border-radius: 50%; overflow: hidden; border: 2px solid gray;">
                            <img src="<?php echo $gravatar_url; ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </a>
                    <span style="color: gray; font-weight: bold;">
                        <?php echo htmlspecialchars($userName); ?>
                    </span>
                </div>
            <?php else: ?>
                <div class="login"><a href="Login.html">Login</a></div>
                <div class="Sign"><a href="Signup.html">Sign Up</a></div>
            <?php endif; ?>
        </div>
    </header>

    <div class="review-page">
        <a href="Homestay.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Homestays</a>
        
        <div class="homestay-header">
            <h1><?php echo htmlspecialchars($homestay['name']); ?></h1>
            <p><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($homestay['location']); ?></p>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <div class="review-form">
            <h2><i class="fa fa-star" style="color: rgb(249, 220, 7); margin-right: 10px;"></i>Write Your Review</h2>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Your Name *</label>
                    <input type="text" name="guest_name" placeholder="Enter your name" required />
                </div>
                
                <div class="form-group">
                    <label>Country (Optional)</label>
                    <input type="text" name="guest_country" placeholder="e.g., Japan, USA" />
                </div>

                <div style="text-align: center; margin: 30px 0;">
                    <label style="display: block; font-weight: 700; margin-bottom: 15px; color: rgb(44, 62, 80); font-size: 18px;">Rate Your Experience *</label>
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

                <div class="form-group">
                    <label>Your Review *</label>
                    <textarea name="comment" placeholder="Share your experience at this homestay..." required></textarea>
                </div>

                <button type="submit" name="submit_review" class="submit-btn">
                    <i class="fa fa-paper-plane"></i> Submit Review
                </button>
            </form>
        </div>

        <div class="reviews-list">
            <h2>Guest Reviews (<?php echo mysqli_num_rows($reviews_result); ?>)</h2>
            
            <?php if ($reviews_result && mysqli_num_rows($reviews_result) > 0): ?>
                <?php while ($r = mysqli_fetch_assoc($reviews_result)): 
                    $r_rating = (int)$r['rating'];
                    $gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower($r['guest_name'])) . "?d=identicon&s=60";
                ?>
                <div class="review-card">
                    <div class="review-header">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, rgb(76, 175, 80) 0%, rgb(56, 142, 60) 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold; flex-shrink: 0;">
                            <?php echo strtoupper($r['guest_name'][0]); ?>
                        </div>
                        <div class="review-info">
                            <h3><?php echo htmlspecialchars($r['guest_name']); ?></h3>
                            <p><?php echo htmlspecialchars($r['guest_country']); ?></p>
                            <p class="review-stars">
                                <?php for ($i=0; $i<5; $i++): ?>
                                    <i class="fa-solid fa-star" style="color: <?php echo $i < $r_rating ? 'rgb(249, 220, 7)' : 'rgb(221, 221, 221)'; ?>;"></i>
                                <?php endfor; ?>
                            </p>
                        </div>
                    </div>
                    <p class="review-text">"<?php echo htmlspecialchars($r['comment']); ?>"</p>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-reviews">
                    <p><i class="fa fa-info-circle"></i> No reviews yet. Be the first to review this homestay!</p>
                </div>
            <?php endif; ?>
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