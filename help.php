<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Help & Support | AutoHive</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/help.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/navbar.css">

  <!-- ✅ AOS Library -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <style>
    .hero {
        margin: 0;
    }

    @media (max-width: 768px) {
      .hero {
        margin-top: 90px;
      }}
    
    
    body {
      background-color: white;
    }
    .support-box {
      background: #f8f9fa;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease;
    }
    .support-box:hover {
      transform: translateY(-5px);
    }
    .btnss {
      background-color: #ffcc00;
      color: #000;
      font-weight: 600;
      padding: 10px 20px;
      border-radius: 8px;
      transition: 0.3s;
    }
    .btnss:hover {
      background-color: #e6b800;
    }

  </style>
</head>

<body>

  <!-- Navbar -->
  <?php include 'navbar.php'; ?>

  <!-- Hero Section -->
  <section class="hero" data-aos="fade-down">
    <div class="container">
      <h1 class="display-4 fw-bold">HELP & SUPPORT | AUTOHIVE</h1>
      <p class="lead">Explore our in-depth guides and support topics for buying, selling, or trading your vehicle on
        AutoHive.</p>
    </div>
  </section>

  <!-- Detailed Support Topics -->
  <section class="py-5">
    <div class="container">
      <h2 class="display-6 fw-bold text-center mb-4 text-uppercase" data-aos="fade-up">What Do You Need Help With?</h2>
      <div class="row g-4">
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
          <div class="support-box">
            <h5>Buying a Car</h5>
            <ul class="list-unstyled">
              <li>🔎 Browsing and advanced filters</li>
              <li>🛠 Vehicle inspection reports</li>
              <li>📆 Scheduling a test drive</li>
              <li>💳 Secure payment & financing options</li>
              <li>📦 Home delivery availability</li>
              <li>📜 Ownership transfer process</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
          <div class="support-box">
            <h5>Selling Your Car</h5>
            <ul class="list-unstyled">
              <li>📸 Uploading car photos & details</li>
              <li>✅ Verification & approval process</li>
              <li>💰 Setting the right price</li>
              <li>📄 Required documents (e.g. registration, ID)</li>
              <li>🤝 Receiving and accepting offers</li>
              <li>🚚 Pickup & payment process</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
          <div class="support-box">
            <h5>Trade-In Support</h5>
            <ul class="list-unstyled">
              <li>🔁 How trade-in works</li>
              <li>📉 Evaluation of your current car</li>
              <li>💸 Price difference and settlement</li>
              <li>📝 Required paperwork</li>
              <li>🚗 Exchanging vehicle at delivery</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Help & Support Accordion -->
  <div class="container my-5">
    <h2 class="display-6 fw-bold text-center mb-4 text-uppercase" data-aos="fade-up">Help & Support</h2>
    <div class="accordion" id="combinedAccordion">
      
      <!-- ✅ Looping through 13 FAQs with animations -->
      <!-- Each accordion-item will have fade-up with increasing delay -->
      <?php
      $faqs = [
        "🔧 Vehicle Inspection & Certification" => "Before Listing a Vehicle: Every vehicle undergoes a <strong>160-point inspection</strong> by AutoHive professionals covering engine performance, mileage, suspension, and more.<br><strong>Certification Tags:</strong><br>✅ AutoHive Certified – Fully inspected and verified<br>⚠️ Seller Verified Only – Inspected by the seller; not AutoHive",
        "📍 Location-Based Services" => "View listings by city or region, get delivery estimates, and use the <strong>'Find Nearby Cars'</strong> tool to locate vehicles close to you.",
        "🛑 Fake Listing Protection" => "Listings go through real-time moderation. AutoHive uses phone verification, document matching, and AI fraud detection to keep listings genuine.",
        "🔑 Ownership Transfer Assistance" => "We assist with document verification and ownership transfer, offering pre-filled forms and courier document pickup in select cities.",
        "⏱️ 7-Day Test Drive Return (Selected Dealers)" => "If the car doesn’t match expectations, return it within 7 days. Applicable only on certified vehicles from AutoHive partner dealers.",
        "💬 Live Chat & Expert Guidance" => "Chat with an advisor to get personalized recommendations based on your budget, usage, fuel type, or future resale value.",
        "📦 Car Shipping & Pickup" => "Door-to-door delivery available in selected regions. Track your vehicle and request car pickup if selling or trading through AutoHive.",
        "🛠️ Post-Sale Support" => "Access discounted maintenance plans, get 1-year roadside assistance (on certified vehicles), and buy extended warranties through AutoHive.",
        "📄 What documents are required to buy a car?" => "To purchase a car, you'll need a valid government-issued ID, proof of income (for financing), and a delivery address. For registration, additional documents may be required based on your location.",
        "🔍 How does AutoHive verify cars before listing?" => "All vehicles undergo a 150+ point inspection that includes mechanical health, exterior/interior condition, accident check, and document verification before being marked as 'Verified' or 'Certified Pre-Owned.'",
        "💸 Can I finance my car through AutoHive?" => "Yes. We partner with multiple financing institutions to offer EMI options, car loans, and zero-down payment plans. Financing options vary depending on your location and credit profile.",
        "📑 What happens during ownership transfer?" => "Once the sale is confirmed, AutoHive handles the title transfer, tax documentation, and registration updates. You'll receive confirmation once all legal ownership is transferred.",
        "🔐 How secure is the transaction?" => "AutoHive uses SSL encryption, payment gateways, and escrow holding accounts to ensure money is only released after verification and user confirmation. Your data and funds are safe throughout."
      ];

      $i = 1;
      foreach ($faqs as $question => $answer) {
        echo '
        <div class="accordion-item" data-aos="fade-up" data-aos-delay="'.($i*100).'">
          <h2 class="accordion-header" id="heading'.$i.'">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$i.'" aria-expanded="false">
              '.$question.'
            </button>
          </h2>
          <div id="collapse'.$i.'" class="accordion-collapse collapse" data-bs-parent="#combinedAccordion">
            <div class="accordion-body">'.$answer.'</div>
          </div>
        </div>';
        $i++;
      }
      ?>
    </div>
  </div>

  <!-- Contact Section -->
  <section class="py-5">
    <div class="container">
      <h2 class="display-6 fw-bold text-center mb-4 text-uppercase" data-aos="fade-up">Still Need Help?</h2>
      <div class="row g-4">
        <div class="col-md-6" data-aos="flip-left" data-aos-delay="100">
          <div class="support-box text-center">
            <h5>📞 Call Support</h5>
            <p>Need to talk to a real person? Call us Mon–Fri, 9am–6pm.</p>
            <a href="tel:+18004567890" class="btn btnss">Call Now</a>
          </div>
        </div>
        <div class="col-md-6" data-aos="flip-right" data-aos-delay="200">
          <div class="support-box text-center">
            <h5>📧 Email Support</h5>
            <p>Send us your queries at <strong>team.autohive@gmail.com</strong></p>
            <a href="mailto:team.autohive@gmail.com" class="btn btnss">Email Us</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- ✅ AOS Script -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 1000,
      once: true
    });
  </script>
</body>
</html>
