<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>About Us | AutoHive</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/about.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/navbar.css">

    <!-- ✅ AOS Animation CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />

    <style>
    /* hero-section */
    .hero {
        margin: 0;
    }

             @media (max-width: 768px) {
      .hero {
        margin-top: 90px;
      }}
    </style>
</head>

<body>

<?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero" data-aos="fade-down">
        <div class="container">
            <h1 class="display-4 fw-bold">ABOUT | AUTOHIVE</h1>
            <p class="lead">Your trusted digital showroom to explore, buy, sell, or trade vehicles with ease and
                confidence.</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-3 bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="extraimages/about1.jpg" alt="About AutoHive" class="about-img" />
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <h2 class="section-title pt-5">Who We Are</h2>
                    <p>
                        <strong>AutoHive</strong> is more than just a vehicle marketplace — it's a digital ecosystem
                        crafted for modern car buyers, sellers, and dealers. We blend cutting-edge technology with
                        trusted automotive partnerships to create a seamless, transparent, and reliable car transaction
                        experience.
                    </p>
                    <p>
                        Whether you’re buying your first car, upgrading to a better one, or selling a pre-owned vehicle,
                        AutoHive ensures your journey is easy, fast, and secure. We understand the complexities of the
                        automotive market — the trust issues, time delays, misleading listings — and we’ve built
                        solutions that remove the hassle.
                    </p>
                    <p>
                        With verified vehicle listings, AI-driven pricing tools, secure transaction methods, and a
                        responsive support team, AutoHive puts the power back in your hands. No more shady deals. No
                        more endless negotiations. Just a smarter, safer way to move on the road.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="display-6 fw-bold text-center mb-4" data-aos="fade-up">OUR MISSION & VISION</h2>

            <!-- Mission -->
            <div class="row align-items-center mb-5">
                <div class="col-md-6 mb-4 mb-md-0" data-aos="fade-right">
                    <img src="extraimages/about2.jpg" class="img-fluid rounded shadow" alt="Mission Image">
                </div>
                <div class="col-md-6" data-aos="fade-left">
                    <h3 class="mb-3">Our Mission</h3>
                    <p>At <strong>AutoHive</strong>, our mission is to revolutionize the vehicle marketplace by
                        transforming how people buy, sell, or trade vehicles—making it as effortless, transparent, and
                        secure as online shopping. We are dedicated to empowering users with verified listings that
                        ensure trust, encrypted transactions that safeguard every deal, and smart tools like instant car
                        evaluations to provide a seamless experience from start to finish. Our platform is designed not
                        just to connect buyers and sellers, but to remove the stress, uncertainty, and complexity often
                        associated with vehicle transactions. Every feature and service we offer is built around one
                        clear vision: simplifying the entire car journey—from discovery to delivery—so you can focus on
                        what truly matters: driving forward with confidence.</p>
                </div>
            </div>

            <!-- Vision -->
            <div class="row align-items-center flex-md-row-reverse">
                <div class="col-md-6 mb-4 mb-md-0" data-aos="fade-left">
                    <img src="extraimages/about3.jpg" class="img-fluid rounded shadow" alt="Vision Image">
                </div>
                <div class="col-md-6" data-aos="fade-right">
                    <h3 class="mb-3">Our Vision</h3>
                    <p>At <strong>AutoHive</strong>, we envision a future where our platform stands as the most trusted
                        and intelligent automotive hub—uniting car buyers and sellers from every corner of the globe
                        with confidence and ease. Our journey forward is shaped by continuous digital innovation, from
                        AI-driven tools to immersive virtual car previews that redefine how people explore vehicles. We
                        aim to break geographical barriers by creating a seamless platform that offers access and
                        opportunity beyond borders. Rooted in a customer-first culture, every interaction on AutoHive is
                        designed to prioritize satisfaction, service, and simplicity. As we grow and evolve, our focus
                        remains clear: to lead the industry as the region’s most reliable, forward-thinking, and
                        user-friendly car marketplace.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Journey Section -->
    <section class="py-5 bg-white" id="our-journey">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-6 fw-bold">OUR JOURNEY</h2>
                <p class="text-muted">From a spark of an idea to becoming your trusted digital showroom — here's how
                    AutoHive evolved.</p>
            </div>

            <div class="timeline">
                <div class="row gy-5">

                    <!-- Year 1 -->
                    <div class="col-md-6" data-aos="zoom-in">
                        <div class="card shadow-sm border-1 p-4 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://cdn-icons-png.flaticon.com/512/992/992700.png" alt="Launch Icon"
                                    width="40" class="me-3">
                                <h5 class="mb-0">2019 - The Idea Sparks</h5>
                            </div>
                            <p>AutoHive began as a simple idea: to simplify the way people buy and sell cars. We saw a
                                gap in trust, transparency, and convenience — and we decided to fill it.</p>
                        </div>
                    </div>

                    <!-- Year 2 -->
                    <div class="col-md-6" data-aos="zoom-in">
                        <div class="card shadow-sm border-1 p-4 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://cdn-icons-png.flaticon.com/512/3063/3063822.png" alt="Team Icon"
                                    width="40" class="me-3">
                                <h5 class="mb-0">2020 - Building the Team</h5>
                            </div>
                            <p>We brought together developers, designers, and car experts to build the foundation of our
                                platform — a user-first experience, driven by technology.</p>
                        </div>
                    </div>

                    <!-- Year 3 -->
                    <div class="col-md-6" data-aos="zoom-in">
                        <div class="card shadow-sm border-1 p-4 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://cdn-icons-png.flaticon.com/512/3594/3594387.png" alt="Launch Icon"
                                    width="40" class="me-3">
                                <h5 class="mb-0">2021 - Official Launch</h5>
                            </div>
                            <p>AutoHive was officially launched. With a seamless UI, verified listings, and digital
                                support, we started helping thousands of users find their perfect car online.</p>
                        </div>
                    </div>

                    <!-- Year 4 -->
                    <div class="col-md-6" data-aos="zoom-in">
                        <div class="card shadow-sm border-1 p-4 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://cdn-icons-png.flaticon.com/512/1995/1995585.png" alt="Growth Icon"
                                    width="40" class="me-3">
                                <h5 class="mb-0">2022 - Rapid Growth</h5>
                            </div>
                            <p>With growing trust, we expanded into multiple cities, introduced AI-based car
                                evaluations, and partnered with dealerships for exclusive pre-owned listings.</p>
                        </div>
                    </div>

                    <!-- Year 5 -->
                    <div class="col-md-6 mx-auto" data-aos="zoom-in">
                        <div class="card shadow-sm border-1 p-4 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135789.png" alt="Future Icon"
                                    width="40" class="me-3">
                                <h5 class="mb-0">2023 & Beyond</h5>
                            </div>
                            <p>We're investing in innovation — from virtual car tours to instant financing. Our journey
                                continues with one goal: to redefine the automotive experience across the globe.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-5 bg-dark text-white">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-6 fw-bold text-center mb-4">WHY CHOOSE AUTOHIVE?</h2>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-3 col-6" data-aos="fade-up">
                    <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png" width="40" class="mb-2" alt="">
                    <h6>Verified Cars</h6>
                    <p class="small">Only inspected and verified vehicles listed.</p>
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828640.png" width="40" class="mb-2" alt="">
                    <h6>Instant Evaluation</h6>
                    <p class="small">Real-time car value estimates powered by smart AI.</p>
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                    <img src="https://cdn-icons-png.flaticon.com/512/1256/1256650.png" width="40" class="mb-2" alt="">
                    <h6>Secure Transactions</h6>
                    <p class="small">Your safety and privacy is our top priority.</p>
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                    <img src="https://cdn-icons-png.flaticon.com/512/3600/3600923.png" width="40" class="mb-2" alt="">
                    <h6>24/7 Support</h6>
                    <p class="small">Always here to help — anytime, anywhere.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- What We Offer Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-6 fw-bold text-center mb-4">WHAT WE OFFER</h2>
                <p class="text-muted fs-5">AutoHive is your all-in-one digital destination for car buyers, sellers, and
                    traders.</p>
            </div>

            <div class="row g-4">
                <!-- Offer Cards with animation -->
                <div class="col-md-4" data-aos="flip-left">
                    <div class="card h-100 text-center shadow-sm border-1">
                        <div class="card-body">
                            <img src="https://cdn-icons-png.flaticon.com/512/2972/2972703.png" alt="Verified Listings" style="width: 60px;" class="mb-3">
                            <h5 class="card-title fw-semibold">Verified Listings</h5>
                            <p class="card-text text-muted">Every car on our platform is thoroughly verified to ensure
                                authenticity, mileage accuracy, and ownership legitimacy.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4" data-aos="flip-left" data-aos-delay="100">
                    <div class="card h-100 text-center shadow-sm border-1">
                        <div class="card-body">
                            <img src="https://cdn-icons-png.flaticon.com/512/2897/2897784.png" alt="Instant Car Valuation" style="width: 60px;" class="mb-3">
                            <h5 class="card-title fw-semibold">Instant Car Valuation</h5>
                            <p class="card-text text-muted">Get real-time AI-powered car evaluations to help you price
                                or negotiate better — instantly and accurately.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4" data-aos="flip-left" data-aos-delay="200">
                    <div class="card h-100 text-center shadow-sm border-1">
                        <div class="card-body">
                            <img src="https://cdn-icons-png.flaticon.com/512/1828/1828676.png" alt="Secure Transactions" style="width: 60px;" class="mb-3">
                            <h5 class="card-title fw-semibold">Secure Transactions</h5>
                            <p class="card-text text-muted">From digital contracts to secure payments, AutoHive ensures
                                your entire car buying/selling process is safe and smooth.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4" data-aos="flip-left">
                    <div class="card h-100 text-center shadow-sm border-1">
                        <div class="card-body">
                            <img src="https://cdn-icons-png.flaticon.com/512/3462/3462374.png" alt="Car Comparisons" style="width: 60px;" class="mb-3">
                            <h5 class="card-title fw-semibold">Smart Car Comparisons</h5>
                            <p class="card-text text-muted">Compare features, prices, and performance of different
                                models side-by-side to make smarter decisions.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4" data-aos="flip-left" data-aos-delay="100">
                    <div class="card h-100 text-center shadow-sm border-1">
                        <div class="card-body">
                            <img src="https://cdn-icons-png.flaticon.com/512/888/888879.png" alt="Trade-In Options" style="width: 60px;" class="mb-3">
                            <h5 class="card-title fw-semibold">Trade-In Assistance</h5>
                            <p class="card-text text-muted">Easily swap your old car with a newer model using our
                                seamless trade-in process with fair pricing evaluations.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4" data-aos="flip-left" data-aos-delay="200">
                    <div class="card h-100 text-center shadow-sm border-1">
                        <div class="card-body">
                            <img src="https://cdn-icons-png.flaticon.com/512/2620/2620733.png" alt="24/7 Customer Support" style="width: 60px;" class="mb-3">
                            <h5 class="card-title fw-semibold">24/7 Customer Support</h5>
                            <p class="card-text text-muted">Our expert team is always available to assist you with
                                queries, negotiations, or technical support around the clock.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center" data-aos="fade-up">Customer Testimonials</h2>
            <div class="row">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="testimonial">
                        <p>AutoHive made my car buying experience super easy. From browsing to finalizing the deal, everything was smooth and transparent. Highly recommended!</p>
                        <strong>— Ahmed Khan , Karachi</strong>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial">
                        <p>I sold my car on AutoHive within just a week. The secure process and verified buyers gave me complete peace of mind.</p>
                        <strong>— Sara Malik , Lahore</strong>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial">
                        <p>Best platform for car lovers! I found a great imported car at a reasonable price. The filters and categories saved me a lot of time.</p>
                        <strong>— Usman Raza , Islamabad</strong>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="testimonial">
                        <p>AutoHive’s inspection and verification process gave me confidence that I was buying a genuine car. Totally hassle-free!</p>
                        <strong>— Faisal Sheikh , Multan</strong>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial">
                        <p>Unlike other platforms, AutoHive feels modern and trustworthy. The customer support was very responsive and helpful throughout my selling journey.</p>
                        <strong>— Ayesha Noor , Faisalabad</strong>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial">
                        <p>AutoHive truly understands customer needs. I upgraded my car through their platform without any hidden charges or complications.</p>
                        <strong>— Noman Ali , Peshawar</strong>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="testimonial">
                        <p>AutoHive helped me sell my old car within a week. The process was smooth and completely
                            stress-free!</p>
                        <strong>— Adeel Khan , Karachi</strong>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial">
                        <p>"The price estimation tool was spot-on and helped me get a great deal when buying my new
                            car."</p>
                        <strong>— Maria Ahmed , Karachi</strong>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial">
                        <p>"Love the interface and the support team was always helpful. AutoHive is a game changer!"</p>
                        <strong>— Farhan Javed , Karachi</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ AOS Animation JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>

</body>
</html>