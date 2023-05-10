<?php
require_once "../class/webstoreclass.php";
require "../PHPMailer/src/PHPMailer.php";
require "../PHPMailer/src/SMTP.php";
$user = $store->get_userdata();
$title = "About Us";
include_once "../includes/header.php";
?>
  <body>
    <div class="page-container">
      <?php include_once "../includes/navbar.php"; ?>
      <!-- start of about section -->
      <main>
        <!-- start of story section -->
        <section id="our-story">
          <div data-sal="slide-right" data-sal-duration="1200" data-sal-delay="200" data-sal-easing="ease-out-bounce" class="container story-container">
            <div class="line"></div>
            <div class="story">
              <h1>Our Story</h1>
              <p>
                <span>DL-Collections is a rising local shoe store</span>
                <span>bringing the trend upon customers.</span>
              </p>
            </div>
          </div>
        </section>
        <section id="mission-vision">
          <div class="container missionVision-container">
            <div data-sal="slide-left" data-sal-duration="1200" data-sal-delay="200" data-sal-easing="ease-out-bounce" class="mission">
              <div class="statement">
                <h1>Our Mission</h1>
                <p>
                  <span
                    >To <span class="highlight">empower</span> local rising
                    brand</span
                  >
                  <span
                    >and <span class="highlight">uplift</span> local
                    products</span
                  >
                  <span
                    >together with the
                    <span class="highlight">success</span> of</span
                  >
                  <span>DL-Collections</span>
                </p>
              </div>
              <img src="./assets/img/mission.svg" alt="Mission" />
            </div>
            <div data-sal="slide-right" data-sal-duration="1200" data-sal-delay="400" data-sal-easing="ease-out-bounce" class="vision">
              <img src="./assets/img/vision.svg" alt="Vision" />
              <div class="statement">
                <h1>Our Vision</h1>
                <p>
                  <span>To make street wears fits to</span>
                  <span
                    >everyone and become
                    <span class="highlight">top tier</span></span
                  >
                  <span
                    >and <span class="highlight">valued</span> clothing
                    line.</span
                  >
                </p>
              </div>
            </div>
          </div>
        </section>
        <!-- end of mission & vision section -->
      </main>
      <!-- end of about section -->
      <?php require_once "../includes/footer.php"; ?>
    </div>
    <?php require_once "../includes/scripts.php"; ?>
    <script src="./assets/js/header.js"></script>
    <script src="./assets/js/user.js"></script>
    <script src="./assets/js/cart.js"></script>
    <script src="./assets/js/lazy.js"></script> 
  </body>
</html>