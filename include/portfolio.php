<?php
// Include database connection
require_once 'admin/config/db.php';

// Query to get portfolio data
$sql = "SELECT project_id, project_name, project_description,project_link, image1 FROM portfolio_projects WHERE status = 'current' LIMIT 10"; // Adjust the query as needed
$result = $conn->query($sql);
?>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

<section id="portfolio" class="portfolio section light-background">
  <div class="container section-title" data-aos="fade-up">
    <h2 class="text-center">Our Portfolio</h2>
    <p class="text-center">Check out our latest projects</p>
    <div class="container">
      <div class="swiper mySwiper">
        <div class="swiper-wrapper">
          <?php
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              // Dynamically output each portfolio item
              echo '
                <div class="swiper-slide">
                  <div class="card">
                    <img src="admin/uploads/' . $row['image1'] . '" class="card-img-top img-fluid" alt="' . $row['project_name'] . '">
                    <div class="card-body text-center">
                      <h5 class="card-title">
                        <a href="portfolio-details.php?id=' . $row['project_id'] . '" class="portfolio-link">' . $row['project_name'] . '</a>
                      </h5>
                      <p class="card-text">' . $row['project_description'] . '</p>
                      <a href="' . $row['project_link'] . '" class="btn btn-primar">View Project</a>
                    </div>
                  </div>
                </div>
              ';
            }
          }
          ?>
        </div>

        <!-- Swiper Pagination -->
        <!-- <div class="swiper-pagination"></div> -->

        <!-- Optional Navigation Buttons
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        -->

      </div>
    </div>
  </div>
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
  var swiper = new Swiper(".mySwiper", {
    loop: true,
    autoplay: {
      delay: 2500,
      disableOnInteraction: false,
    },
    spaceBetween: 20,
    slidesPerView: 1,
    centeredSlides: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      576: { slidesPerView: 1 },
      768: { slidesPerView: 2 },
      1024: { slidesPerView: 3 }
    }
  });
</script>

<style>
  /* Adjust card layout */
  .swiper {
    height: 530px !important; /* Fixed height for uniformity */
    background-color: #f3f9ff; /* Light blue background color */
  }

  .swiper-wrapper {
    display: flex;
  }

  .swiper-slide {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .card {
    width: 350px; /* Fixed card size */
    height: 430px; /* Fixed height for uniformity */
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
    background: linear-gradient(145deg, #e6f7ff, #cceeff); /* Soft gradient background */
    position: relative; /* Enable absolute positioning for button */
  }

  .card:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
  }

  .card img {
    height: 200px;
    object-fit: cover;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
  }

  .card-body {
    padding: 17px;
    color: #333;
    background: #ffffff; /* White background for text area */
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
    text-align: center;
    overflow-y: auto; /* Enable vertical scrolling if content overflows */
    height: calc(100% - 200px); /* Adjust height to fit within card */
  }

  .card-title {
    font-size: 20px;
    font-weight: bold;
    color: #007BFF; /* Blue color for title */
    margin-bottom: 10px;
  }

  .card-text {
    font-size: 14px;
    color: #666;
    margin-bottom: 15px;
    max-height: 100px; /* Limit height of description */
    overflow-y: auto; /* Enable scrolling for description */
  }

  .btn-primar {
    background-color: #007BFF; /* Matching blue button */
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    transition: background-color 0.3s;
    position: absolute; /* Position button at the bottom */
    bottom: 10px; /* Adjust as needed */
    left: 50%;
    transform: translateX(-50%);
  }

  .btn-primar:hover {
    background-color: #0056b3; /* Darker blue on hover */
  }

  .swiper-pagination {
    margin-top: 10px;
    text-align: center;
  }

  /* Pagination dots styling */
  .swiper-pagination-bullet {
    background-color: #007BFF; /* Blue pagination dots */
  }

  .swiper-pagination-bullet-active {
    background-color: #0056b3; /* Dark blue for active dot */
  }

  /* Adjusting styles for better appearance */
  @media (max-width: 768px) {
    .card {
      width: 250px; /* Slightly smaller cards on mobile */
      height: 350px;
    }
  }
</style>