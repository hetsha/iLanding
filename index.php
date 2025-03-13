<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="description" content="Upparac - A top web development and social media marketing in Ahmedabad. We build responsive websites, offer digital marketing, and SEO services to grow your business.">
  <meta name="keywords" content="Web Development in Ahmedabad,Web devlopment,logo make,social media Marketing,seo ,upparac, upparac_technology, SEO services in Paldi,web, website, Website Design, Upparac Web Development, Digital Marketing, Custom Web Solutions, Business Growth, E-commerce Development">
  <!-- <meta name="robots" content="index, follow"> -->
  <link rel="canonical" href="https://upparac.com">
  <title>Upparac | Website Design & Social Media marketing in Ahmedabad</title>

  <meta property="og:title" content="Upparac Web Development | Web Design & SEO in Ahmedabad">
  <meta property="og:description" content="Best web development and SEO services in Paldi, Ahmedabad. We create fast, responsive websites.">
  <meta property="og:image" content="https://upparac.com/logo.png">
  <meta property="og:url" content="https://upparac.com">
  <meta name="twitter:card" content="summary_large_image">

  <!-- Favicons -->
  <link href="assets/img/upparac6.png" rel="icon">
  <link rel="shortcut icon" href="assets/img/upparac6.png" type="image/x-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "Upparac Web Development",
      "url": "https://upparac.com",
      "logo": "https://upparac.com/logo.png",
      "description": "Upparac Web Development - Web design, SEO, and digital marketing services in Ahmedabad, Paldi.",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Paldi",
        "addressLocality": "Ahmedabad",
        "addressCountry": "India"
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+91-9427961426",
        "contactType": "customer service"
      },
      "sameAs": [
        "https://www.facebook.com/Upparac",
        "https://www.twitter.com/Upparac",
        "https://www.linkedin.com/company/Upparac"
        "https://www.instagram.com/upparac_technology"
      ]
    }
  </script>
  <style>
    #none {
      display: none;
    }
    .black {
      color: black !important;
    }
    .gy-four {
      --bs-gutter-y: 6.5rem;
    }

    .card {
      width: 290px !important;
      height: 450px !important;
    }

    /* .new:hover{
      transform: translatey(-15px) scale(1.005) !important;
      transition: transform 0.3s ease-in-out, box-shadow 0.5s ease-in-out;
      /* box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2) !important; */
    /* } */

    .tile {
      padding-bottom: 110px;
    }

    .card img {
      width: auto !important;
      height: 300px !important;
      object-fit: cover;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }
    /* Card animations: Zoom + Up-Down hover effect */
    .animate-card {
      transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .animate-card-no{
      transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }

    /* Hover effect - zoom and lift */
    .animate-card:hover {
      transform: translateY(-30px) scale(3.05);
      box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
    }

    /* Up-Down animation */
    @keyframes float {
      0% {
        transform: translateY(0px);
      }

      50% {
        transform: translateY(-10px);
      }

      100% {
        transform: translateY(0px);
      }
    }

    .animate-card {
      animation: float 3s ease-in-out infinite;
    }
    .animate-card-no {
      animation: float 2s ease-in-out infinite;
    }

    @media (max-width: 768px) {
      .card {
        width: 300px !important;
        height: 440px !important;
      }

      .new {
        width: 450px !important;
        height: 350px !important;
        margin-left: 30px;
      }
    }

  </style>
</head>

<body class="index-page">
  <h1 id="none">Upparac</h1>
  <?php include 'include/header.php'; ?>

  <main class="main">

    <!-- Hero Section -->
    <?php include 'include/hero.php'; ?>

    <!-- About Section -->
    <?php include 'include/about.php'; ?>

    <!-- Services Section -->
    <?php include 'include/services.php'; ?>
    <!-- Features Cards Section -->
    <?php include 'include/features.php'; ?>

    <!-- Clients Section -->

    <?php include 'include/portfolio.php'; ?>

    <!-- Stats Section -->
    <section id="stats" class="stats section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="200" data-purecounter-duration="1"
                class="purecounter"></span>
              <p>Clients</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="156" data-purecounter-duration="1"
                class="purecounter"></span>
              <p>Projects</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="2000" data-purecounter-duration="1"
                class="purecounter"></span>
              <p>Hours Of Support</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="23" data-purecounter-duration="1"
                class="purecounter"></span>
              <p>Workers</p>
            </div>
          </div><!-- End Stats Item -->

        </div>

      </div>

    </section><!-- /Stats Section -->

    <!-- Pricing Section -->
    <?php include 'include/pricing.php'; ?>

    <!-- Faq Section -->
    <section class="faq-9 faq section" id="faq">

      <div class="container">
        <div class="row">

          <div class="col-lg-5" data-aos="fade-up">
            <h2 class="faq-title">Have a question? Check out the FAQ</h2>
            <p class="faq-description">Here are some frequently asked questions to help you understand how we work and
              how we can assist in building your website.</p>
          </div>

          <div class="col-lg-7" data-aos="fade-up" data-aos-delay="300">
            <div class="faq-container">

              <div class="faq-item faq-active">
                <h3>What is the process of building a website with your company?</h3>
                <div class="faq-content">
                  <p>Our process begins with a consultation to understand your business and goals. We then create a
                    custom design and development plan. Once approved, we begin developing your site and ensure it's
                    tested thoroughly before launch. Ongoing support and updates are also available.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>How long will it take to build my website?</h3>
                <div class="faq-content">
                  <p>The time required depends on the complexity of the project. For a simple informational website, it
                    can take around 1 week, while more complex websites may take 2-4 weeks or longer.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Do you offer website maintenance services?</h3>
                <div class="faq-content">
                  <p>Yes, we offer ongoing website maintenance services to ensure your website stays up-to-date, secure,
                    and functional. We can help with updates, security patches, and troubleshooting.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Can I update the website content myself after it's built?</h3>
                <div class="faq-content">
                  <p>Yes, we build websites with user-friendly content management systems (CMS) such as WordPress or
                    custom admin panels, so you can easily update your website’s content, images, and blog posts
                    yourself.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>Will my website be mobile-friendly?</h3>
                <div class="faq-content">
                  <p>Absolutely! We design all of our websites to be responsive, meaning they will look and function
                    great on desktops, tablets, and mobile devices.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item">
                <h3>What kind of support do you offer after my website is launched?</h3>
                <div class="faq-content">
                  <p>We offer post-launch support that includes troubleshooting, content updates, security monitoring,
                    and general maintenance. You can also contact us anytime for assistance with new features or
                    improvements.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

            </div>
          </div>

        </div>
      </div>
    </section><!-- /Faq Section -->

    <!-- Call To Action 2 Section -->
    <!-- <section id="call-to-action-2" class="call-to-action-2 section dark-background">

      <div class="container">
        <div class="row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
          <div class="col-xl-10">
            <div class="text-center">
              <h3>Call To Action</h3>
              <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est
                laborum.</p>
              <a class="cta-btn" href="#">Call To Action</a>
            </div>
          </div>
        </div>
      </div>

    </section>/Call To Action 2 Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section light-background">

      <!-- Section Title -->
      <div class="container section-title tile" data-aos="fade-up">
        <h2>Contact</h2>
        <!-- <p>Contact here</p> -->
        <div class="row gy-4 gy-four">
          <div class="new col-lg-3 col-md-6">
            <div class="card profile-card animate-card" data-aos="fade-up" data-aos-delay="100">
              <img src="assets/img/het.jpg" class="xcard-img-top" alt="Profile Image">
              <div class="card-body text-center">
                <h5 class="card-title black">Het shah</h5>
                <p class="card-text"><strong>Email:</strong>hetshha6315@gmail.com</p>
                <p class="card-text"><strong>Phone:</strong> +91 9427961426</p>
                <div class="social-links">
                  <a href="https://www.instagram.com/hetshah_1102" class="instagram" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-instagram"></i>
                  </a>
                  <a href="https://www.linkedin.com/in/het-shah1102" class="linkedin"><i class="bi bi-linkedin"></i></a>
                  <a href="tel:+919427961426" class="call"><i class="bi bi-telephone"></i></a>
                  <a href="https://wa.me/919427961426" class="whatsapp" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-whatsapp"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="new col-lg-3 col-md-6">
            <div class="card profile-card animate-card" data-aos="fade-up" data-aos-delay="300">
              <img src="assets/img/WhatsApp Image 2025-02-04 at 20.54.22_eda36905.jpg" class="card-img-top" alt="Profile Image">
              <div class="card-body text-center">
                <h5 class="card-title black">Het shah</h5>
                <p class="card-text"><strong>Email:</strong>het3156@gmail.com</p>
                <p class="card-text"><strong>Phone:</strong> +91 9328738382</p>
                <div class="social-links">
                  <a href="https://www.instagram.com/_het_31_5" class="instagram" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-instagram"></i>
                  </a>
                  <a href="https://www.linkedin.com/in/het-shah-229aa32b8/" class="linkedin"><i class="bi bi-linkedin"></i></a>
                  <a href="tel:+919328738282" class="call"><i class="bi bi-telephone"></i></a>
                  <a href="https://wa.me/919328738282" class="whatsapp" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-whatsapp"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="new col-lg-3 col-md-6">
            <div class="card profile-card animate-card" data-aos="fade-up" data-aos-delay="400">
              <img src="assets/img/WhatsApp Image 2025-02-04 at 20.04.02_c52516f2.png" class="card-img-top" alt="Profile Image">
              <div class="card-body text-center">
                <h5 class="card-title black">Akshat shah</h5>
                <p class="card-text"><strong>Email:</strong>akshatjshah2005@gmail.com</p>
                <p class="card-text"><strong>Phone:</strong> +91 9825079765</p>
                <div class="social-links black">
                  <a href="https://www.instagram.com/akshatt_shahh" class="instagram" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-instagram"></i>
                  </a>
                  <a href="https://www.linkedin.com/in/akshat-shah-29b7a4321/" class="linkedin"><i class="bi bi-linkedin"></i></a>
                  <a href="tel:+919825079765" class="call"><i class="bi bi-telephone"></i></a>
                  <a href="https://wa.me/919825079765" class="whatsapp" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-whatsapp"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="new col-lg-3 col-md-6">
            <div class="card profile-card animate-card" data-aos="fade-up" data-aos-delay="200">
              <img src="assets/img/jainam.jpg" class="card-img-top" alt="Profile Image">
              <div class="card-body text-center">
                <h5 class="card-title black">Jainam Patel</h5>
                <p class="card-text"><strong>Email:</strong>jainampatel2602@gmail.com</p>
                <p class="card-text"><strong>Phone:</strong> +91 9104676701</p>
                <div class="social-links">
                  <a href="https://www.instagram.com/jainam_patel_6767" class="instagram" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-instagram"></i>
                  </a>
                  <!-- <a href="https://www.linkedin.com/in/het-shah1102" class="linkedin"><i class="bi bi-linkedin"></i></a> -->
                  <a href="tel:+919104676701" class="call"><i class="bi bi-telephone"></i></a>
                  <a href="https://wa.me/919104676701" class="whatsapp" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-whatsapp"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div><!-- End Section Title -->


      <div class="container  section" data-aos="fade-up" data-aos-delay="100">

        <div class="row g-4 g-lg-5">
          <div class="col-lg-5">
            <div class="info-box" data-aos="fade-up" data-aos-delay="200">
              <h3>Contact Info</h3>

              <div class="info-item" data-aos="fade-up" data-aos-delay="300">
                <div class="icon-box">
                  <i class="bi bi-geo-alt"></i>
                </div>
                <div class="content">
                  <h4>Our Location</h4>
                  <p>Ahmedabad(Gujarat)</p>
                  <p>India</p>
                </div>
              </div>

              <div class="info-item" data-aos="fade-up" data-aos-delay="400">
                <div class="icon-box">
                  <i class="bi bi-telephone"></i>
                </div>
                <div class="content">
                  <h4>Phone Number</h4>
                  <!-- <p><a href="tel:+919427961426">+91 9427961426</a></p> -->
                  <p><a href="tel:+919825079765">+91 9825079765</a></p>
                  <p><a href="tel:+919427961426">+91 9427961426</a></p>
                  <p><a href="tel:+919328738282">+91 9328738282</a></p>
                  <p><a href="tel:+919104676701">+91 9104676701</a></p>
                </div>
              </div>

              <div class="info-item" data-aos="fade-up" data-aos-delay="500">
                <div class="icon-box">
                  <i class="bi bi-envelope"></i>
                </div>
                <div class="content">
                  <h4>Email Address</h4>
                  <!-- <p class="mt-3"><strong>Email:</strong> <a href="mailto:upparactechnology@gmail.com">upparactechnology@gmail.com</a></p> -->
                  <p class="mt-3"><strong>Email:</strong> <a href="mailto:contact@upparac.com">contact@upparac.com</a></p>
                </div>
              </div>
            </div>
          </div>

          <?php include 'include/contact.php'; ?>

  </main>

  <?php include 'include/footer.php'; ?>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
  <!-- Include Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <script>
    var swiper = new Swiper(".portfolio-slider", {
      loop: true,
      spaceBetween: 20,
      slidesPerView: 1,
      autoplay: {
        delay: 3000, // Auto-slide every 3 seconds
        disableOnInteraction: false, // Keeps autoplay even after user interaction
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true
      }
    });
  </script>

</body>

</html>