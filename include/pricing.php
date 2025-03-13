<style>
  .hidden {
    display: none !important;
  }

  .dottd {
    text-decoration-style: dashed !important;
    text-underline-position: under;
    color: rgba(98, 98, 98, 0.7) !important;
  }

  .dottd-white {
    text-decoration-style: dashed !important;
    text-underline-position: under;
    color: rgba(255, 255, 255, 0.86) !important;
  }

  .bi-dash {
    color: rgba(0, 0, 0, 0.71) !important;
  }

  .white {
    color: white !important;
  }

  .btn {
    margin-top: 10px !important;
    cursor: pointer;
  }

  .text-muted-white {
    color: #ddd !important;
  }

  .pricing-container {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 1.8rem;
  }

  /* Remove Bootstrap column constraints */
  .col-xl-3 {
    flex: 1;
    max-width: unset;
  }
  .pricing-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    /* Ensures exactly 4 cards in one row */
    gap: 0.1rem;
    /* Adds spacing between cards */
    justify-content: center;
    max-width: 100%;
  }

  .pricing-card {
    max-width: 350px;
    /* Limits width to prevent excessive stretching */
    min-width: 300px;
    /* Ensures cards don’t shrink too much */
  }


  .col-xl-3 {
    flex: 1;
    /* Ensures each column takes equal space */
    max-width: unset;
  }


  .pricing-card {
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    background-color: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 380px;
    /* margin: 30px; */
    /* margin-left: 230px; */
  }

  .pricing-card .price {
    font-size: 24px;
    font-weight: bold;
    color: #333;
  }

  .pricing-card .features-list {
    text-align: left;
    margin-top: 15px;
    padding: 0;
    list-style: none;
  }

  .features-list li {
    margin-bottom: 8px;
  }

  .popular {
    border: 2px solid #007bff;
  }

  .popular-badge {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #007bff;
    color: #fff;
    padding: 5px 10px;
    font-size: 12px;
    border-radius: 5px;
  }

  .btn-primary {
    background-color: #007bff;
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
    margin-top: 10px;
  }

  .btn-primary:hover {
    background-color: #0056b3;
  }

  .btn-link {
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
    display: inline-block;
    margin-top: 10px;
  }

  .btn-link:hover {
    text-decoration: underline;
  }
</style>

<section id="pricing" class="pricing section light-background">

  <div class="container section-title" data-aos="fade-up">
    <h2>Packages</h2>
    <p>Choose a plan that fits your needs and budget. We offer a range of web design and development packages for businesses of all sizes.</p>
  </div>

  <div class="container gaps" data-aos="fade-up" data-aos-delay="100">
    <div class="row g-9 justify-content-center pricing-container">

      <!-- Basic Plan -->
      <div class="col-xl-3" data-aos="fade-up" data-aos-delay="100" style="margin-left: 5vw;">
        <div class="pricing-card">
          <h3>Basic Plan</h3>
          <p class="description">For small businesses, personal websites, and startups</p>
          <div class="price">
            <span class="currency">₹</span>
            <span class="amount">5999</span>
            <span class="period">/1st yr</span>
          </div>
          <p class="descrition">Renews at ₹1500/yr</p>
          <!-- <hr> -->
          <a href="https://wa.me/+919825079765?text=Hello,%20I%20am%20interested%20in%20the%20Basic%20Package%20(₹5999/1st%20yr)."
            class="btn btn-primary" target="_blank">
            Buy Now <i class="bi bi-whatsapp"></i>
          </a>
          <hr>
          <h4>Features Included:</h4>
          <ul class="features-list">
            <li><i class="bi bi-check"></i>Up to 5 Pages</li>
            <li><i class="bi bi-check"></i>Admin Panel for basic content</li>
            <li><i class="bi bi-check"></i>Standard Design</li>
            <li><i class="bi bi-check"></i>Social Media Integration<br>link & icon </li>
            <li><i class="bi bi-check"></i>SSL certificate</li>
            <li><i class="bi bi-check"></i>24/7 Support</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>standerd SEO & Speed Optimization</li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i>Google Map Integration</li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i>Business Analytics</li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i>Website Maintenance</li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i>Secure Payment Gateway</li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i>Wishlist & Order Tracking</li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i>User Registration & Login</li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i>Stock Management System</li>
          </ul>
          <button class="btn btn-link toggle-btn show-more btn btn-primary" onclick="toggleAllFeatures()">Show More</button>
        </div>
      </div>

      <!-- Standard Plan -->
      <div class="col-xl-3" data-aos="fade-up" data-aos-delay="200">
        <div class="pricing-card popular">
          <div class="popular-badge">Most Popular</div>
          <h3>Standard Plan</h3>
          <p class="description">For growing businesses needing more features</p>
          <div class="price">
            <span class="currency">₹</span>
            <span class="amount">9599</span>
            <span class="period">/1st yr</span>
          </div>
          <p class="descrition">Renews at ₹1500/yr</p>
          <a href="https://wa.me/+919825079765?text=Hello,%20I%20am%20interested%20in%20the%20Standard%20Package%20(₹9599/1st%20yr)."
            class="btn btn-light" target="_blank">
            Buy Now <i class="bi bi-whatsapp"></i>
          </a>
          <hr>
          <h4>Features Included:</h4>
          <ul class="features-list">
            <li><i class="bi bi-check"></i>Up to 7 Pages</li>
            <li><i class="bi bi-check"></i> Limited Admin Panel</li>
            <li><i class="bi bi-check"></i>Fully Responsive Design</li>
            <li><i class="bi bi-check"></i>Social Media intigration <br>link & icon </li>
            <li><i class="bi bi-check"></i>SSL certificate</li>
            <li><i class="bi bi-check"></i>24/7 support</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Basic SEO & Speed <br>Optimization</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Google Map Integration</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Business analytics</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Website maintence</li>
            <li class="text-decoration-underline dottd-white text-muted-white extra-features hidden"><i class="bi bi-dash white"></i>Secure Payment Gateway</li>
            <li class="text-decoration-underline dottd-white text-muted-white extra-features hidden"><i class="bi bi-dash white"></i>Wishlist & Order Tracking</li>
            <li class="text-decoration-underline dottd-white text-muted-white extra-features hidden"><i class="bi bi-dash white"></i>User Registration & Login</li>
            <li class="text-decoration-underline dottd-white text-muted-white extra-features hidden"><i class="bi bi-dash white"></i>Stock Management System</li>
          </ul>
          <button class="btn btn-link toggle-btn show-more btn btn-light" onclick="toggleAllFeatures()">Show More</button>
        </div>
      </div>

      <!-- Advanced Plan -->
      <div class="col-xl-3" data-aos="fade-up" data-aos-delay="300">
        <div class="pricing-card">
          <h3>Advanced Plan</h3>
          <p class="description">For businesses selling online <br>store </p>
          <div class="price">
            <span class="currency">₹</span>
            <span class="amount">13499</span>
            <span class="period">/1st yr</span>
          </div>
          <p class="descrition">Renews at ₹1500/yr</p>
          <a href="https://wa.me/+919825079765?text=Hello,%20I%20am%20interested%20in%20the%20Premium%20Package%20(₹13499/1st%20yr)."
            class="btn btn-primary" target="_blank">
            Buy Now <i class="bi bi-whatsapp"></i>
          </a>
          <hr>
          <h4>Features Included:</h4>
          <ul class="features-list">
            <li><i class="bi bi-check"></i>Customized Pages</li>
            <li><i class="bi bi-check"></i>Limited CRM</li>
            <li><i class="bi bi-check"></i>Responsive Design</li>
            <li><i class="bi bi-check"></i>1-Month Social Media <br>Management</li>
            <li><i class="bi bi-check"></i>SSL certificate</li>
            <li><i class="bi bi-check"></i>24/7 support</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Advanced SEO & Speed Optimization</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Google Map Integration</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>business analytics</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>website maintence</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Secure Payment Gateway</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Wishlist & Order Tracking</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>User Registration & Login</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Stock Management System</li>
          </ul>
          <button class="btn btn-link toggle-btn show-more btn btn-primary" onclick="toggleAllFeatures()">Show More</button>
        </div>
      </div>

      <!-- Premium Plan -->
      <div class="col-xl-3" data-aos="fade-up" data-aos-delay="400" style="margin-right: 5vw;">
        <div class="pricing-card">
          <h3>Premium Plan</h3>
          <p class="description">For custom enterprise solutions & large-scale businesses</p>
          <div class="price">
            <span class="currency">₹</span>
            <span class="amount">15499</span>
            <span class="period">/1st yr</span>
          </div>
          <p class="descrition">Renews at ₹1500/yr</p>
          <a href="https://wa.me/+919825079765?text=Hello,%20I%20am%20interested%20in%20the%20Premium%20Package%20(₹13499/1st%20yr)."
            class="btn btn-primary" target="_blank">
            Buy Now <i class="bi bi-whatsapp"></i>
          </a>
          <hr>
          <h4>Features Included:</h4>
          <ul class="features-list">
            <li><i class="bi bi-check"></i>Unlimited Pages</li>
            <li><i class="bi bi-check"></i>CRM Integration</li>
            <li><i class="bi bi-check"></i>Custom Design</li>
            <li><i class="bi bi-check"></i>6-Month Social Media<br>Management</li>
            <li><i class="bi bi-check"></i>SSL Certificate</li>
            <li><i class="bi bi-check"></i>24/7 Support</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Advanced SEO & Speed Optimization</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Google Map Integration</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>business analytics</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>website maintence</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>API Integration</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Custom Admin Panel</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Multi-Language Support</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i>Custom Features as per Requirement</li>
          </ul>
          <button class="btn btn-link toggle-btn show-more btn btn-primary" onclick="toggleAllFeatures()">Show More</button>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
  function toggleAllFeatures() {
    let extraFeatures = document.querySelectorAll('.extra-features');
    let buttons = document.querySelectorAll('.toggle-btn');

    let allHidden = Array.from(extraFeatures).every(feature => feature.classList.contains('hidden'));

    extraFeatures.forEach(feature => {
      feature.classList.toggle('hidden', !allHidden);
    });

    buttons.forEach(button => {
      button.innerText = allHidden ? "Show Less" : "Show More";
    });

    // Scroll down when showing more
    if (allHidden) {
      document.querySelector('.pricing-card:last-child').scrollIntoView({
        behavior: 'smooth',
        block: 'end'
      });
    }
    // Scroll up when showing less
    else {
      document.querySelector('.pricing').scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  }
</script>