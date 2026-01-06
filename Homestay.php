<?php
session_start();
include 'Backend/databaseconnection.php'; 

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['name'] : '';
$userEmail = $isLoggedIn ? $_SESSION['email'] : '';
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
        <a href="#">Notification</a>
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
        <div class="location">
          <label for="location">Location</label><br />
          <input type="text" placeholder="search by location......" />
        </div>

        <div class="Price_range">
          <label for="price">Price Range</label><br />
          <select id="price" name="price">
            <option value="any price">Any price</option>
            <option value="1000-1500">Rs.1000-Rs.1500</option>
            <option value="1500-2000">Rs.1500-Rs.2000</option>
          </select>
        </div>

        <div class="sort">
          <label for="sort">Sort by</label><br />
          <select id="sort" name="sort">
            <option value="recommended">Recommended</option>
            <option value="rate">Highest Rated</option>
          </select>
        </div>

        <div class="filter-action">
          <button class="filter-btn"><i class="fa fa-filter"></i> Filter</button>
        </div>
      </div>

      <div class="right-section">
        <a href="html/create_homestay.html" style="text-decoration: none;">
          <button class="add-homestay-btn">Add Your Homestay</button>
        </a>
      </div>
    </div>

    <div class="Destination">
        <?php
        $sql = "SELECT * FROM homestays"; 
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
        ?>
            <div class="Container_text">
              <img src="images/<?php echo $row['profile_image']; ?>" alt="images" />
              
              <div class="homestay_details">
                <div class="homestay_name">
                  <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                </div>
                <div class="review">
                  <p>
                    <i class="fa-solid fa-star" style="color: rgb(249, 220, 7)"></i>
                    <?php echo $row['rating']; ?>
                  </p>
                </div>
              </div>

              <div class="location_icon">
                <p><i class="fa-solid fa-location-dot"></i><?php echo htmlspecialchars($row['location']); ?></p>
              </div>

              <p><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . '...'; ?></p>

              <div class="nepal_side">
                <i class="fa-regular fa-user"></i><p>Hosted by <?php echo htmlspecialchars($row['host_name']); ?></p>
              </div>

              <div class="View_detail">
                <a href="homestaydetail.php?id=<?php echo $row['user_id']; ?>">
                  <button>View Details</button>
                </a>
              </div>
            </div>
        <?php 
            }
        } else {
            echo "<p style='padding: 20px;'>No homestays found in the database.</p>";
        }
        ?>
    </div>

    <footer>
        <div class="main_section">
          <div class="media">
            <img src="Images/logo.png" />
            <p>Discover authentic Nepali hospitality through our carefully selected homestays.</p>
            <div class="icons">
              <a href="#" class="fa-brands fa-facebook" style="color: blue; font-size: 24px; margin-right: 10px;"></a>
              <a href="#" class="fa-brands fa-instagram" style="color: red; font-size: 24px;"></a>
            </div>
          </div>
          <div class="link">
            <h2>Quick Links</h2>
            <div class="tags">
              <a href="Homestay.php">Homestays</a>
              <a href="Contact.php">Contact Us</a>
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