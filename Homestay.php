<?php
session_start();
include 'Backend/databaseconnection.php'; 

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['name'] : '';
$userEmail = $isLoggedIn ? $_SESSION['email'] : '';

$location_search = isset($_GET['location']) && $_GET['location'] !== '' ? trim($_GET['location']) : '';
$price_range = isset($_GET['price']) && $_GET['price'] !== '' ? $_GET['price'] : '';
$sort_by = isset($_GET['sort']) && $_GET['sort'] !== '' ? $_GET['sort'] : 'recommended';

$where_conditions = [];

if (!empty($location_search)) {
    $location_search_escaped = $conn->real_escape_string($location_search);
    $where_conditions[] = "h.location LIKE '%$location_search_escaped%'";
}

if (!empty($price_range)) {
    if ($price_range === '1000-1500') {
        $where_conditions[] = "h.price >= 1000 AND h.price <= 1500";
    } elseif ($price_range === '1500-2000') {
        $where_conditions[] = "h.price >= 1500 AND h.price <= 2000";
    }
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

$order_by = 'h.homestay_id DESC';
if ($sort_by === 'rate') {
    $order_by = 'avg_rating DESC';
} elseif ($sort_by === 'recommended') {
    $order_by = 'h.homestay_id DESC';
}

$sql = "SELECT h.*, IFNULL(AVG(t.rating), 0) as avg_rating 
        FROM homestays h 
        LEFT JOIN testimonials t ON h.homestay_id = t.homestay_id 
        $where_clause 
        GROUP BY h.homestay_id 
        ORDER BY $order_by"; 

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

$count = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Available Homestays</title>
    <link rel="stylesheet" href="./css/homestay.css" />
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
          <?php if ($isLoggedIn): ?>
              <a href="Backend/my_bookings.php">My Bookings</a>  
          <?php endif; ?>
          <a href="Contact.php">Contact</a>
      </div>
      <div class="Login_container">
        <?php if ($isLoggedIn): 
            $gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($userEmail))) . "?d=mp&s=40";
        ?>
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="Backend/profile.php">
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
      <h1>Available Homestays</h1>
      <p>Discover authentic Nepali culture through local family experiences</p>
    </div>

    <div class="container1">
      <div class="left-section">
        <div class="filter-section">
          <form method="GET" action="Homestay.php">
            <div class="filter-group">
              <label for="location"><i class="fa fa-map-marker"></i> Location</label>
              <input 
                type="text" 
                id="location" 
                name="location" 
                placeholder="Search by location..." 
                value="<?php echo htmlspecialchars($location_search); ?>" 
              />
            </div>
            <div class="filter-group">
              <label for="price"><i class="fa fa-money-bill"></i> Price Range</label>
              <select id="price" name="price">
                <option value="">Any price</option>
                <option value="1000-1500" <?php echo $price_range === '1000-1500' ? 'selected' : ''; ?>>
                  Rs. 1000 - Rs. 1500
                </option>
                <option value="1500-2000" <?php echo $price_range === '1500-2000' ? 'selected' : ''; ?>>
                  Rs. 1500 - Rs. 2000
                </option>
              </select>
            </div>
            <div class="filter-group">
              <label for="sort"><i class="fa fa-sort"></i> Sort by</label>
              <select id="sort" name="sort">
                <option value="recommended" <?php echo $sort_by === 'recommended' ? 'selected' : ''; ?>>
                  Recommended
                </option>
                <option value="rate" <?php echo $sort_by === 'rate' ? 'selected' : ''; ?>>
                  Highest Rated
                </option>
              </select>
            </div>
            <div class="filter-buttons">
              <button type="submit" class="filter-btn">
                <i class="fa fa-filter"></i> Filter
              </button>
              <a href="Homestay.php" style="flex: 1; text-decoration: none;">
                <button type="button" class="filter-btn reset-btn" style="width: 100%;">
                  <i class="fa fa-redo"></i> Reset
                </button>
              </a>
            </div>
          </form>
        </div>
      </div>
      <div class="right-section">
        <a href="html/create_homestay.html" style="text-decoration: none;">
          <button class="add-homestay-btn">Add Your Homestay</button>
        </a>
      </div>
    </div>

    <?php if ($count > 0): ?>
    <div class="results-info">
        Found <strong><?php echo $count; ?></strong> homestay(s)
        <?php if (!empty($location_search)): ?>
            in <strong><?php echo htmlspecialchars($location_search); ?></strong>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="Destination">
        <?php if ($count > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): 
                $avgRating = number_format($row['avg_rating'], 1);
                $starRating = round($row['avg_rating']);
            ?>
            <div class="Container_text">
              <img src="images/<?php echo htmlspecialchars($row['profile_image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" />
              <div class="homestay_details">
                <div class="homestay_name">
                  <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                </div>
                <div class="review">
                  <p>
                    <i class="fa-solid fa-star" style="color: rgb(249, 220, 7)"></i>
                    <?php echo $avgRating; ?>
                  </p>
                </div>
              </div>
              <div class="location_icon">
                <p><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($row['location']); ?></p>
              </div>

              <p><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . '...'; ?></p>
              <div class="nepal_side">
                <i class="fa-regular fa-user"></i>
                <p>Hosted by <?php echo htmlspecialchars($row['host_name']); ?></p>
              </div>

              <div class="price-display">
                Rs. <?php echo number_format($row['price'], 2); ?> / night
              </div>
              <div class="View_detail">
               <a href="homestaydetail.php?id=<?php echo $row['homestay_id']; ?>">
                <button class="view-btn">View Detail</button>
               </a>
              </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results">
              <i class="fa fa-search" style="font-size: 48px; color: #ccc;"></i>
              <p>No homestays found matching your filters.</p>
              <p><a href="Homestay.php">Clear filters and try again</a></p>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="main_section">
          <div class="media">
            <img src="images/logo.png" alt="TravelLocal Nepal" />
          <p>Discover authentic Nepali hospitality through our homestays.<br>Experience local culture and breathtaking landscapes.</p>
            <div class="icons">
              <button><i class="fa-brands fa-facebook" style="color: blue;"></i></button>
              <button><i class="fa-brands fa-instagram" style="color: red;"></i></button>
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
  </body>
</html>
<?php mysqli_close($conn); ?>