<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Table</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Open Sans", sans-serif;
        }

        body {
            background-color: #f4f4f4;
            text-align: center;
        }

        .pricing-section {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
        }

        .pricing-header {
            font-size: 36px;
            font-weight: 700;
            color: #222;
            margin-bottom: 30px;
        }

        .pricing-table {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .pricing-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 30%;
            min-width: 300px;
            transition: all 0.3s;
            position: relative;
        }

        .pricing-card:hover {
            transform: scale(1.05);
        }

        .best-value {
            border: 2px solid #ff9800;
        }

        .best-value::before {
            content: "Best Value";
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: #ff9800;
            color: white;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 5px;
        }

        .pricing-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .pricing-price {
            font-size: 28px;
            font-weight: 700;
            color: #0d83fd;
            margin-bottom: 10px;
        }

        .pricing-yearly {
            font-size: 18px;
            color: #555;
            margin-bottom: 15px;
        }

        .divider {
            height: 1px;
            background: #ddd;
            margin: 15px 0;
        }

        .pricing-list {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
        }

        .pricing-list li {
            padding: 10px;
            font-size: 16px;
        }

        .pricing-list li i {
            margin-right: 8px;
            color: #0d83fd;
        }

        .btn {
            display: inline-block;
            background-color: #0d83fd;
            color: #fff;
            padding: 12px 25px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 25px;
            text-transform: uppercase;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: #0d83fd;
            cursor: pointer;
            font-weight: 600;
            margin-top: 10px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>

    <section class="pricing-section">
        <h2 class="pricing-header">Choose Your Plan</h2>
        <div class="pricing-table">

            <div class="pricing-card">
                <h3 class="pricing-title">basic package</h3>
                <p class="pricing-price">₹5999/1st year</p>
                <!-- <p class="pricing-yearly">₹5999</p> -->
                <p class="pricing-yearly">₹5999</p>
                <div class="divider"></div>
                <ul class="pricing-list">
                    <li><i class="fas fa-check"></i> 1 Site</li>
                    <li><i class="fas fa-check"></i> 30 Days Data Retention</li>
                    <li class="extra-features hidden"><i class="fas fa-times"></i> Chart Annotations</li>
                    <li class="extra-features hidden"><i class="fas fa-times"></i> Uptime Monitoring</li>
                </ul>
                <button class="toggle-btn" onclick="toggleAllFeatures()">Show More</button>
                <a href="#" class="btn">Get Started</a>
            </div>

            <div class="pricing-card best-value">
            <div class="price">
            <span class="currency">₹</span>
            <span class="amount">5999</span>
            <span class="period"><b>/1st yr</b></span>
          </div>
                <div class="divider"></div>
                <ul class="pricing-list">
                    <li><i class="fas fa-check"></i> 1 Site</li>
                    <li><i class="fas fa-check"></i> 90 Days Data Retention</li>
                    <li class="extra-features hidden"><i class="fas fa-check"></i> Chart Annotations</li>
                    <li class="extra-features hidden"><i class="fas fa-check"></i> Uptime Monitoring</li>
                </ul>
                <button class="toggle-btn" onclick="toggleAllFeatures()">Show More</button>
                <a href="#" class="btn">Get Started</a>
            </div>

            <div class="pricing-card">
                <h3 class="pricing-title">Pro</h3>
                <p class="pricing-price">₹2,999/month</p>
                <p class="pricing-yearly">₹32,999/year</p>
                <div class="divider"></div>
                <ul class="pricing-list">
                    <li><i class="fas fa-check"></i> 1 Site</li>
                    <li><i class="fas fa-check"></i> 180 Days Data Retention</li>
                    <li class="extra-features hidden"><i class="fas fa-check"></i> Priority Support</li>
                    <li class="extra-features hidden"><i class="fas fa-check"></i> Security Audit</li>
                </ul>
                <button class="toggle-btn" onclick="toggleAllFeatures()">Show More</button>
                <a href="#" class="btn">Get Started</a>
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
        }
    </script>

</body>

</html>



<section id="pricing" class="pricing section light-background">
  <!-- Section Title -->

  <div class="container section-title" data-aos="fade-up">
    <h2>Packages</h2>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row g-4 justify-content-center">

      <!-- Basic Package -->
      <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
        <div class="pricing-card">
          <h3>Basic Package</h3>
          <div class="price">
            <span class="currency">₹</span>
            <span class="amount">5999</span>
            <span class="period"><b>/1st yr</b></span>
          </div>
          <p class="description">This Plan For Only Starter Websites.<br>(Not For E-Commerce Website)</p>
          <a href="https://wa.me/+919825079765?text=Hello,%20I%20am%20interested%20in%20the%20Basic%20Package%20(₹5999/1st%20yr)."
            class="btn btn-primary" target="_blank">
            Buy Now <i class="bi bi-whatsapp"></i>
          </a>
          <hr>
          <h4>Features Included:</h4>
          <ul class="features-list">
            <li><i class="bi bi-check"></i> 5-Page Website (Home, About Us, Services, Contact, Gallery)</li>
            <li><i class="bi bi-check"></i> Admin Panel (CRM) for Basic Content Management</li>
            <li><i class="bi bi-check"></i> Free Domain & Hosting For 1 Year (xyz.in, xyz.com, xyz.online)</li>
            <li><i class="bi bi-check"></i> Fully Responsive Design</li>
            <li><i class="bi bi-check"></i> Social Media Integration (Links & Icons)</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> SSL Certificate (HTTPS Secure)</li>
            <!-- <li ><i class="bi bi-check-circle-fill"></i> Mobile Optimization</li> -->
            <li class="hidden"><i class="bi bi-check"></i>24/7 Technical Support</li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i> SEO Optimization</li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i> Google My Business Setup</li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i> Basic Website Analytics (Google Analytics Setup)</li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i> free Logo Design </li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i> Website Maintenance </li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i> Basic Social Media Marketing (Facebook, Instagram)</li>
            <li class="text-decoration-underline dottd text-muted extra-features hidden"><i class="bi bi-dash"></i> Free Animation in Website</li>
          </ul>
          <button class="btn btn-link toggle-btn show-more btn btn-primary" onclick="toggleAllFeatures()">Show More</button>
        </div>
      </div>

      <!-- Standard Package -->
      <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
        <div class="pricing-card popular">
          <div class="popular-badge">Most Popular</div>
          <h3>Standard Package</h3>
          <div class="price">
            <span class="currency">₹</span>
            <span class="amount">9599</span>
            <span class="period">/1st yr</span>
          </div>
          <p class="description">This Plan For Business Websites.<br>(Not For E-Commerce Website)</p>
          <a href="https://wa.me/+919825079765?text=Hello,%20I%20am%20interested%20in%20the%20Standard%20Package%20(₹9599/1st%20yr)."
            class="btn btn-light" target="_blank">
            Buy Now <i class="bi bi-whatsapp"></i>
          </a>

          <hr>
          <h4>Features Included:</h4>
          <ul class="features-list">
            <li><i class="bi bi-check"></i> 7-Page Website (Home, About, Services, Blog, Contact, Gallery, Portfolio)</li>
            <li><i class="bi bi-check"></i> Admin Panel (CRM) Limited with Blog Management</li>
            <li><i class="bi bi-check"></i> Free Domain & Hosting For 1 Year (xyz.in, xyz.com, xyz.online)</li>
            <li><i class="bi bi-check"></i> Fully Responsive Design</li>
            <li><i class="bi bi-check"></i> Social Media Integration (Links & Icons)</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> SSL Certificate (HTTPS Secure)</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> 24/7 Technical Support</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> SEO Optimization (Basic)</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> Google My Business Setup</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> Basic Website Analytics (Google Analytics Setup)</li>
            <li class="text-decoration-underline dottd-white text-muted-white extra-features hidden"><i class="bi bi-dash white"></i>free Logo Design </li>
            <li class="text-decoration-underline dottd-white text-muted-white extra-features hidden"><i class="bi bi-dash white"></i> Website Maintenance </li>
            <li class="text-decoration-underline dottd-white text-muted-white extra-features hidden"><i class="bi bi-dash white"></i> Basic Social Media Marketing (Facebook, Instagram)</li>
            <li class="text-decoration-underline dottd-white text-muted-white extra-features hidden"><i class="bi bi-dash white"></i> Free Animation in Website</li>
          </ul>
          <button class="btn btn-link toggle-btn show-more btn btn-light" onclick="toggleAllFeatures()">Show More</button>
         </div>
      </div>

      <!-- Premium Package -->
      <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
        <div class="pricing-card">
          <h3>Premium Package</h3>
          <div class="price">
            <span class="currency">₹</span>
            <span class="amount">13499</span>
            <span class="period">/1st yr</span>
          </div>
          <p class="description">Fully Customized Website (For E-Commerce Websites)</p>
          <a href="https://wa.me/+919825079765?text=Hello,%20I%20am%20interested%20in%20the%20Premium%20Package%20(₹13499/1st%20yr)."
            class="btn btn-primary" target="_blank">
            Buy Now <i class="bi bi-whatsapp"></i>
          </a>
          <hr>
          <h4>Features Included:</h4>
          <ul class="features-list">
            <li><i class="bi bi-check"></i> Unlimited Pages (Product Pages, Blog, Gallery, Services, etc.)</li>
            <li><i class="bi bi-check"></i> Admin Panel (CRM) with Full Website Management</li>
            <li><i class="bi bi-check"></i> Free Domain & Hosting For 1 Year (xyz.in, xyz.com, xyz.online)</li>
            <li><i class="bi bi-check"></i> Fully Responsive & Mobile-Friendly Design</li>
            <li><i class="bi bi-check"></i> Social Media Integration</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> SSL Certificate (HTTPS Secure)</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> 24/7 Technical Support</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> Advanced SEO Optimization</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> Google My Business Setup</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> Google Analytics & Performance Tracking with demo</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> Free Logo Design</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> Website Maintenance (3 Months Free)</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> Basic Social Media Marketing (Facebook, Instagram)</li>
            <li class="extra-features hidden"><i class="bi bi-check"></i> Free Animation in Website</li>
            <!-- <li><i class="bi bi-check-circle-fill"></i> E-Commerce Functionality (Product Listings, Cart, Checkout)</li> -->
          </ul>
          <button class="btn btn-link toggle-btn show-more btn btn-primary" onclick="toggleAllFeatures()">Show More</button>
        </div>
      </div>
    </div>
  </div>

</section>