<?php
?>


<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
  <title>Grinyard - Connect Farmers with Extension Officers</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/theme.min.css">
  <style>
    /* Custom styling for Grinyard index page */
    body {
      background-color: #f9fafb;
      color: #333333;
      font-family: Arial, sans-serif;
    }
    .navbar-brand {
      font-size: 30px;
      color: #4c9a2a; /* Earthy green for the Grinyard brand */
      font-weight: bold;
    }
    .navbar-brand:hover {
      color: #367a1f; /* Darker green on hover */
    }
    /* Hero Section with Background Image */
    .hero-section {
      background: url('../Grinyard/assets/img/farmland1.jpg') no-repeat center center/cover;
      color: white;
      padding: 120px 0;
      text-align: center;
      position: relative;
    }
    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5); /* Dark overlay for readability */
    }
    .hero-section .container {
      position: relative;
      z-index: 1;
    }
    .hero-section h1 {
      font-size: 3.5rem;
      font-weight: bold;
    }
    .section-heading {
      color: #367a1f; /* Accent color for section headings */
      font-size: 2rem;
      margin-bottom: 20px;
      font-weight: bold;
    }
    .btn-primary {
      background-color: #4c9a2a;
      border: none;
    }

    /* Profile and Testimonial Cards with Hover Effect */
    .profile-card, .testimonial-card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 20px;
      background-color: rgba(0, 0, 0, 0.625);
      color: white;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .profile-card:hover, .testimonial-card:hover {
      transform: scale(1.05); /* Slightly enlarge the box on hover */
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Add shadow for depth */
    }

    /* Styling for Feature Sections */
    .feature-section {
      background-color: #eaf4ea; /* Light greenish background */
      padding: 60px 0;
      color: #333333;
    }
    .footer {
      background-color: #333;
      color: #fff;
      padding: 40px 0;
      text-align: center;
    }
    .footer a {
      color: #4c9a2a;
      text-decoration: none;
    }
    .footer a:hover {
      color: #367a1f;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Grinyard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item"><a class="nav-link" href="#services">Our Services</a></li>
        <li class="nav-item"><a class="nav-link" href="#officers">Extension Officers</a></li>
        <li class="nav-item"><a class="nav-link" href="#testimonials">Testimonials</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
        <li class="nav-item"><a class="btn btn-primary" href="../Grinyard/view/LOGIN_Grinyard.php">Login</a></li>
      </ul>
    </div>
  </nav>

  <!-- Hero Section with Background Image -->
  <section class="hero-section">
    <div class="container">
      <h1>Empowering Farmers with Expert Support</h1>
      <p class="lead">Connect with specialized extension officers to improve your agricultural productivity.</p>
      <a href="../Grinyard/view/signup.php" class="btn btn-primary btn-lg mt-3">Join Now</a>
    </div>
  </section>

  <!-- Our Services -->
  <section id="services" class="feature-section text-center">
    <div class="container">
      <h2 class="section-heading">Our Services</h2>
      <p class="mb-5">Grinyard connects farmers with skilled extension officers who provide guidance on crop management, livestock care, and sustainable farming practices.</p>
      <div class="row">
        <div class="col-md-4">
          <div class="profile-card p-4">
            <h4>Personalized Farm Support</h4>
            <p>Get one-on-one support tailored to your farm's needs, from soil health to pest management.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="profile-card p-4">
            <h4>Specialized Expertise</h4>
            <p>Access officers with expertise in areas like crop science, animal husbandry, and agricultural technology.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="profile-card p-4">
            <h4>Booking and Scheduling</h4>
            <p>Easily book and schedule sessions with extension officers at your convenience.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Extension Officers -->
  <section id="officers" class="feature-section">
    <div class="container">
      <h2 class="section-heading text-center">Meet Our Extension Officers</h2>
      <p class="text-center mb-5">Browse profiles of our skilled extension officers and book a consultation with the right expert for your farm.</p>
      <div class="row">
        <!-- Officer Card -->
        <div class="col-md-4">
          <div class="profile-card text-center">
            <img src="../Grinyard/assets/img/officer1.jpeg" class="img-fluid rounded-circle mb-3" alt="Officer Name" width="240" height="240">
            <h5>John Terrance</h5>
            <p class="text-muted">Crop Management Specialist</p>
            <p>Expert in sustainable crop management and soil health improvement.</p>
            
          </div>
        </div>
        <div class="col-md-4">
          <div class="profile-card text-center">
            <img src="../Grinyard/assets/img/officer2.jpeg" class="img-fluid rounded-circle mb-3" alt="Officer Name" width="240" height="240">
            <h5>Patrick Manful</h5>
            <p class="text-muted">Livestock Care Specialist</p>
            <p>Specializes in animal health, nutrition, and disease prevention.</p>
            
          </div>
        </div>
        <div class="col-md-4">
          <div class="profile-card text-center">
            <img src="../Grinyard/assets/img/officer3.jpeg" class="img-fluid rounded-circle mb-3" alt="Officer Name" width="90" height="90">
            <h5>Emma Brown</h5>
            <p class="text-muted">Agricultural Technology Expert</p>
            <p>Provides insights on modern farming techniques and tech tools.</p>
           
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section id="testimonials" class="feature-section text-center">
    <div class="container">
      <h2 class="section-heading">What Farmers Are Saying</h2>
      <div class="row">
        <div class="col-md-6">
          <div class="testimonial-card p-4">
            <p>"Grinyard helped me improve my crop yield tremendously. The extension officer was knowledgeable and provided me with customized solutions."</p>
            <p class="text-muted">- Kwame Mensah, Farmer</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="testimonial-card p-4">
            <p>"I am grateful for the guidance on livestock management. My animals are healthier and more productive now, thanks to Grinyard."</p>
            <p class="text-muted">- Abena Owusu, Livestock Farmer</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <p>&copy; 2023 Grinyard. All rights reserved.</p>
      <p><a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
