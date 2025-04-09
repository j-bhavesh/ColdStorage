<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mahaveer Overseas Coldstorage | Landing Page</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('landing-page/images/favicon.png') }}">
    <link href="{{ asset('landing-page/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('landing-page/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('landing-page/css/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('landing-page/css/intlTelInput.css') }}" rel="stylesheet" />
    <link href="{{ asset('landing-page/css/style.css') }}" rel="stylesheet">
</head>

<body>
    
    <!-- start header-section -->
    <header class="header-wrapper section-wrapper fixedHeader">
        <div class="header-main">
            <nav class="navbar navbar-expand-lg custom-navbar">
                <div class="container">
                    <a class="navbar-brand logodefault" href="#">
                        <img src="{{ asset('landing-page/images/logo.webp') }}" alt="brand-logo" class="logo-img">
                    </a>
                    <div class="d-flex align-items-center d-lg-none gap-3 mt-2 mt-lg-0">
                        <a class="btn primary-btn px-3 py-2" href="#" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">
                            Get A Quote
                        </a>
                        <a class="btn primary-btn px-3 py-2" href="#" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">
                            Get A Quote1
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 custom-nav-menu">
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="tel:+91 9099 032 177">
                                    <i class="fas fa-phone-alt icon-box"></i>+91 9099 032 177
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="mailto:bharat@raindropsinfotech.com">
                                    <i class="far fa-envelope icon-box"></i>bharat@raindropsinfotech.com
                                </a>
                            </li>
                            <li class="nav-item mt-1 ps-lg-3 d-none d-lg-block">
                                <a class="btn primary-btn" href="#" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal">Get A Quote</a>
                                <!-- <a class="btn primary-btn" href="#">Signup</a> -->
                                @if (Route::has('login'))
                                    @auth
                                        <a class="btn primary-btn" href="{{ route('admin.dashboard') }}">
                                            Dashboard
                                        </a>
                                    @else
                                        <a class="btn primary-btn" href="{{ route('login') }}">
                                            Login To Dashbaord
                                        </a>
                                    @endauth
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <!-- end header-section -->

    <!-- start banner-section -->
    <section class="home-section home-bg">
        <div class="container">
            <div class="home-content">
                <div class="row align-items-center">
                    <div class="col-lg-6 order-2 orders-lg-1">
                        <div class="subheading mb-3">
                            <span><img src="{{ asset('landing-page/images/subheading-image.webp') }}" alt="subheading-image">Farmer & Dealer
                                Friendly</span>
                        </div>
                        <h1 class="banner-main-title mb-3">Smart Potato Cold Storage
                            Management System</h1>
                        <p class="banner-text mb-4">The Cold Storage App is built for potato dealers, farmers, and cold
                            storage owners to automate booking, payment, and
                            tracking processes. It reduces manual paperwork, ensures transparency, and saves time with
                            built-in reporting and SMS
                            notifications.</p>
                        <div class="d-flex justify-content-center justify-content-lg-start align-items-center gap-3">
                            <a href="#"><img src="{{ asset('landing-page/images/playstore-btn.webp') }}" alt="play-store"></a>
                            <a href="#"><img src="{{ asset('landing-page/images/appstore-btn.webp') }}" alt="app-store"></a>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-5 mb-lg-0 order-1 order-lg-2">
                        <div class="ps-lg-5 bannerright-image">
                            <img src="{{ asset('landing-page/images/potato-01.webp') }}" class="ani-top-bottom d-none d-lg-block" alt="potato-01">
                            <img src="{{ asset('landing-page/images/potato-02.webp') }}" class="ani-zoom d-none d-lg-block" alt="potato-02">
                            <img src="{{ asset('landing-page/images/potato-03.webp') }}" class="ani-left-right d-none d-lg-block" alt="potato-03">
                            <img src="{{ asset('landing-page/images/banner-right-image.webp') }}" alt="banner-image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-shape">
            <img src="{{ asset('landing-page/images/banner-bg.webp') }}" class="img-fluid w-100" alt="slider-shape">
        </div>
    </section>
    <!-- end banner-section -->

    <!-- start about-section -->
    <section class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="pe-lg-4 text-center">
                        <img src="{{ asset('landing-page/images/about-image.webp') }}" alt="about-image" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="subheading mb-3">
                        <span><img src="{{ asset('landing-page/images/subheading-image.webp') }}" alt="subheading-image">Farmer & Dealer
                            Friendly</span>
                    </div>
                    <h2 class="section-title">Transforming The Way Dealers & Farmers Manage Cold Storage</h2>
                    <p class="mb-3 about-text">Our Cold Storage App helps dealers and farmers manage every step of
                        seed booking, payment, packaging, and storage — with
                        automated reports and SMS notifications.
                    </p>
                    <p class="mb-0 about-text">The Cold Storage App simplifies the way farmers, dealers, and storage
                        owners manage seed booking, distribution, and
                        financial tracking. Designed for real-time transparency and efficiency.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- end about-section -->

    <!-- start features-section -->
    <section class="features-section">
        <div class="shape-top">
            <img src="{{ asset('landing-page/images/section-shape-top.webp') }}" class="img-fluid w-100" alt="section-shape-top">
        </div>
        <div class="container">
            <div class="section-heading text-center mb-5">
                <div class="subheading mb-3">
                    <span><img src="{{ asset('landing-page/images/subheading-image.webp') }}" alt="subheading-image">Our Features</span>
                </div>
                <h2 class="section-title">Smart Cold Storage Management Features</h2>
            </div>
            <div class="features-carousel owl-carousel owl-theme">
                <div class="card features-card">
                    <div class="features-icon-wrapper mb-4">
                        <img class="features-icon" src="{{ asset('landing-page/images/feature-01.webp') }}" alt="...">
                        <div class="features-card__shape">
                            <img src="{{ asset('landing-page/images/feature-arrow.webp') }}" alt="shape">
                        </div>
                    </div>
                    <span class="features-card__count">01</span>
                    <div class="card-body p-0">
                        <h3>Farmer Management</h3>
                        <p class="mb-0">Add, search, and manage farmer profiles & IDs automatically.
                        </p>
                    </div>
                </div>
                <div class="card features-card">
                    <div class="features-icon-wrapper mb-4">
                        <img class="features-icon" src="{{ asset('landing-page/images/feature-02.webp') }}" alt="...">
                        <div class="features-card__shape">
                            <img src="{{ asset('landing-page/images/feature-arrow.webp') }}" alt="shape">
                        </div>
                    </div>
                    <span class="features-card__count">02</span>
                    <div class="card-body p-0">
                        <h3>Seeds Booking</h3>
                        <p class="mb-0">Create agreements with rates, quantities, and SMS confirmation.
                        </p>
                    </div>
                </div>
                <div class="card features-card">
                    <div class="features-icon-wrapper mb-4">
                        <img class="features-icon" src="{{ asset('landing-page/images/feature-03.webp') }}" alt="...">
                        <div class="features-card__shape">
                            <img src="{{ asset('landing-page/images/feature-arrow.webp') }}" alt="shape">
                        </div>
                    </div>
                    <span class="features-card__count">03</span>
                    <div class="card-body p-0">
                        <h3>Seed Distribution</h3>
                        <p class="mb-0">Assign seeds, validate quantities, and send farmer notifications.
                        </p>
                    </div>
                </div>
                <div class="card features-card">
                    <div class="features-icon-wrapper mb-4">
                        <img class="features-icon" src="{{ asset('landing-page/images/feature-04.webp') }}" alt="...">
                        <div class="features-card__shape">
                            <img src="{{ asset('landing-page/images/feature-arrow.webp') }}" alt="shape">
                        </div>
                    </div>
                    <span class="features-card__count">04</span>
                    <div class="card-body p-0">
                        <h3>Potato Booking Agreement</h3>
                        <p class="mb-0">Create agreements with rates, quantities, and SMS confirmation.
                        </p>
                    </div>
                </div>
                <div class="card features-card">
                    <div class="features-icon-wrapper mb-4">
                        <img class="features-icon" src="{{ asset('landing-page/images/feature-05.webp') }}" alt="...">
                        <div class="features-card__shape">
                            <img src="{{ asset('landing-page/images/feature-arrow.webp') }}" alt="shape">
                        </div>
                    </div>
                    <span class="features-card__count">05</span>
                    <div class="card-body p-0">
                        <h3>Packaging Distribution</h3>
                        <p class="mb-0">Manage bag quantities and prevent over-distribution.
                        </p>
                    </div>
                </div>
                <div class="card features-card">
                    <div class="features-icon-wrapper mb-4">
                        <img class="features-icon" src="{{ asset('landing-page/images/feature-06.webp') }}" alt="...">
                        <div class="features-card__shape">
                            <img src="{{ asset('landing-page/images/feature-arrow.webp') }}" alt="shape">
                        </div>
                    </div>
                    <span class="features-card__count">06</span>
                    <div class="card-body p-0">
                        <h3>Advance Payment</h3>
                        <p class="mb-0">Record advance payments, track dates, and validate deals.
                        </p>
                    </div>
                </div>
                <div class="card features-card">
                    <div class="features-icon-wrapper mb-4">
                        <img class="features-icon" src="{{ asset('landing-page/images/feature-07.webp') }}" alt="...">
                        <div class="features-card__shape">
                            <img src="{{ asset('landing-page/images/feature-arrow.webp') }}" alt="shape">
                        </div>
                    </div>
                    <span class="features-card__count">07</span>
                    <div class="card-body p-0">
                        <h3>Storage Loading</h3>
                        <p class="mb-0">Log bag quantities, storage names, and transporter info.
                        </p>
                    </div>
                </div>
                <div class="card features-card">
                    <div class="features-icon-wrapper mb-4">
                        <img class="features-icon" src="{{ asset('landing-page/images/feature-08.webp') }}" alt="...">
                        <div class="features-card__shape">
                            <img src="{{ asset('landing-page/images/feature-arrow.webp') }}" alt="shape">
                        </div>
                    </div>
                    <span class="features-card__count">08</span>
                    <div class="card-body p-0">
                        <h3>Storage Unloading</h3>
                        <p class="mb-0">Verify unloading data, company details, and bag weights.
                        </p>
                    </div>
                </div>
                <div class="card features-card">
                    <div class="features-icon-wrapper mb-4">
                        <img class="features-icon" src="{{ asset('landing-page/images/feature-09.webp') }}" alt="...">
                        <div class="features-card__shape">
                            <img src="{{ asset('landing-page/images/feature-arrow.webp') }}" alt="shape">
                        </div>
                    </div>
                    <span class="features-card__count">09</span>
                    <div class="card-body p-0">
                        <h3>Challan Creation</h3>
                        <p class="mb-0">Auto-generate challans and share with farmers via SMS.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="shape-bottom">
            <img src="{{ asset('landing-page/images/section-shape-bottom.webp') }}" class="img-fluid w-100" alt="section-shape-bottom">
        </div>
    </section>
    <!-- end features-section -->

    <!-- start how-work-section -->
    <section class="how-work-section">
        <div class="container">
            <div class="section-heading text-center mb-5">
                <div class="subheading mb-3">
                    <span><img src="{{ asset('landing-page/images/subheading-image.webp') }}" alt="subheading-image">How We Work</span>
                </div>
                <h2 class="section-title">How the Cold Storage Process Works</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-sm-6 col-lg-3 mb-4 mb-md-5 mb-lg-4">
                    <div class="process-wrapper">
                        <div class="process-image d-inline-block">
                            <img src="{{ asset('landing-page/images/process-01.webp') }}" alt="...">
                        </div>
                        <div class="process-contnet">
                            <span>Step 01</span>
                            <h3 class="mb-2">Farmer Registration</h3>
                            <p class="mb-0">The Farmer Registration module allows dealers to easily add and manage
                                farmer details, including name, village, &
                                contact information.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4 mb-md-5 mb-lg-4">
                    <div class="process-wrapper">
                        <div class="process-image d-inline-block">
                            <img src="{{ asset('landing-page/images/process-02.webp') }}" alt="...">
                        </div>
                        <div class="process-contnet">
                            <span>Step 02</span>
                            <h3 class="mb-2">Seeds Booking & Distribute</h3>
                            <p class="mb-0">Real-time validation and SMS notifications keep both dealers and farmers
                                informed throughout the distribution process.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4 mb-md-5 mb-lg-4">
                    <div class="process-wrapper">
                        <div class="process-image d-inline-block">
                            <img src="{{ asset('landing-page/images/process-03.webp') }}" alt="...">
                        </div>
                        <div class="process-contnet">
                            <span>Step 03</span>
                            <h3 class="mb-2">Potato Booking Agreement</h3>
                            <p class="mb-0">Potato Booking Agreement module enables dealers to create & manage
                                booking agreements with farmers for specific
                                seed varieties and rates.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4 mb-md-5 mb-lg-4">
                    <div class="process-wrapper last-arrow">
                        <div class="process-image d-inline-block">
                            <img src="{{ asset('landing-page/images/process-04.webp') }}" alt="...">
                        </div>
                        <div class="process-contnet">
                            <span>Step 04</span>
                            <h3 class="mb-2">Packaging Distribution</h3>
                            <p class="mb-0">The Packaging Distribution module manages the allocation of packaging bags
                                to farmers based on their booking agreements.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4 mb-md-5 mb-lg-0">
                    <div class="process-wrapper">
                        <div class="process-image d-inline-block">
                            <img src="{{ asset('landing-page/images/process-05.webp') }}" alt="...">
                        </div>
                        <div class="process-contnet">
                            <span>Step 05</span>
                            <h3 class="mb-2">Storage Loading</h3>
                            <p class="mb-0">The Storage Loading module handles the process of loading seeds into cold
                                storage with complete details like
                                transporter, vehicle, and storage location.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4 mb-md-5 mb-lg-0">
                    <div class="process-wrapper">
                        <div class="process-image d-inline-block">
                            <img src="{{ asset('landing-page/images/process-06.webp') }}" alt="...">
                        </div>
                        <div class="process-contnet">
                            <span>Step 06</span>
                            <h3 class="mb-2">Storage Unloading</h3>
                            <p class="mb-0">The Storage Unloading module manages the unloading of seeds from transport
                                vehicles into cold storage facilities with
                                complete tracking details.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4 mb-md-5 mb-md-0">
                    <div class="process-wrapper">
                        <div class="process-image d-inline-block">
                            <img src="{{ asset('landing-page/images/process-07.webp') }}" alt="...">
                        </div>
                        <div class="process-contnet">
                            <span>Step 07</span>
                            <h3 class="mb-2">Tracking Payments</h3>
                            <p class="mb-0">The Tracking Payments module monitors all financial transactions, including
                                advance and debit payments for each farmer.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="process-wrapper last-arrow">
                        <div class="process-image d-inline-block">
                            <img src="{{ asset('landing-page/images/process-08.webp') }}" alt="...">
                        </div>
                        <div class="process-contnet">
                            <span>Step 08</span>
                            <h3 class="mb-2">Generate Report</h3>
                            <p class="mb-0">The Generate Report module compiles detailed data on farmers, agreements,
                                payments, and storage activities into
                                easy-to-read reports.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end how-work-section -->

    <!-- start screenshot section -->
    <section class="app-screenshots-area">
        <div class="shape-top">
            <img src="{{ asset('landing-page/images/section-shape-top.webp') }}" class="img-fluid w-100" alt="section-shape-top">
        </div>
        <div class="container">
            <div class="section-heading text-center mb-5">
                <div class="subheading mb-3">
                    <span><img src="{{ asset('landing-page/images/subheading-image.webp') }}" alt="subheading-image">App Screens</span>
                </div>
                <h2 class="section-title">On-Boarding Screens</h2>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="app_screenshots_slides owl-carousel">
                        <div class="single-shot">
                            <img src="{{ asset('landing-page/images/slide-screen-01.png') }}" alt="slide-screen-01" />
                        </div>
                        <div class="single-shot">
                            <img src="{{ asset('landing-page/images/slide-screen-02.png') }}" alt="slide-screen-02" />
                        </div>
                        <div class="single-shot">
                            <img src="{{ asset('landing-page/images/slide-screen-03.png') }}" alt="slide-screen-03" />
                        </div>
                        <div class="single-shot">
                            <img src="{{ asset('landing-page/images/slide-screen-04.png') }}" alt="slide-screen-04" />
                        </div>
                        <div class="single-shot">
                            <img src="{{ asset('landing-page/images/slide-screen-05.png') }}" alt="slide-screen-05" />
                        </div>
                        <div class="single-shot">
                            <img src="{{ asset('landing-page/images/slide-screen-06.png') }}" alt="slide-screen-06" />
                        </div>
                        <div class="single-shot">
                            <img src="{{ asset('landing-page/images/slide-screen-07.png') }}" alt="slide-screen-07" />
                        </div>
                        <div class="single-shot">
                            <img src="{{ asset('landing-page/images/slide-screen-08.png') }}" alt="slide-screen-08" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="shape-bottom">
            <img src="{{ asset('landing-page/images/section-shape-bottom.webp') }}" class="img-fluid w-100" alt="section-shape-bottom">
        </div>
    </section>
    <!-- end screenshot section -->

    <!-- start faq section -->
    <section class="faq-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-2 order-lg-1">
                    <div class="mb-4">
                        <div class="subheading mb-3">
                            <span><img src="{{ asset('landing-page/images/subheading-image.webp') }}" alt="subheading-image">FAQ</span>
                        </div>
                        <h2 class="section-title">Got Questions? We’ve Got Answers!</h2>
                    </div>
                    <div class="accordion" id="accordion">
                        <div class="card mb-3">
                            <h2 class="card-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    What is the purpose of the Cold Storage App ?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                data-bs-parent="#accordion">
                                <div class="card-body">
                                    The Cold Storage App is designed to simplify and automate the entire cold storage
                                    and seed booking process. It helps
                                    dealers manage farmers, seed distribution, agreements, payments, packaging,
                                    transportation, and financial tracking in
                                    one place. It ensures transparency, accuracy, and efficient recordkeeping for all
                                    transactions between farmers and
                                    dealers.
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h2 class="card-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Who can use the Cold Storage App ?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                data-bs-parent="#accordion">
                                <div class="card-body">
                                    The app is primarily built for dealers, admins, and farmers. Admins manage master
                                    data such as companies, transporters,
                                    vehicles, and seed varieties. Dealers handle operations like farmer registration,
                                    booking agreements, payments, loading
                                    and unloading, and reports. Farmers receive notifications about agreements,
                                    payments, and storage activities and can
                                    view their deal information.
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <h2 class="card-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    What features are included in the Cold Storage App ?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                                data-bs-parent="#accordion">
                                <div class="card-body">
                                    The system includes multiple modules to handle different parts of the workflow. It
                                    covers farmer management, agreement
                                    and seed distribution, payment and financial tracking, storage management, and
                                    report generation. It also includes a
                                    notification system that automatically alerts farmers through SMS when key actions
                                    like agreements, payments, or
                                    challans are created.
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <h2 class="card-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    How does app ensure data accuracy & transparency ?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                                data-bs-parent="#accordion">
                                <div class="card-body">
                                    The app ensures data accuracy and transparency through validation, access control,
                                    and real-time notifications.
                                    Duplicate entries are prevented, quantities are validated against agreements, and
                                    only authorized users can make
                                    changes. Farmers are automatically notified at every important stage, ensuring clear
                                    and trustworthy communication
                                    throughout the process.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-5 mb-lg-0 order-1 order-lg-2">
                    <div class="ps-lg-4 text-center">
                        <div class="faq-image-wrapper">
                            <img src="{{ asset('landing-page/images/faq-image.webp') }}" alt="faq-image" class="img-fluid">
                            <img src="{{ asset('landing-page/images/faq-image-small.webp') }}" alt="faq-image" class="img-fluid small-image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end faq section -->

    <!-- start footer section -->
    <footer class="footer-section">
        <div class="shape-top">
            <img src="{{ asset('landing-page/images/section-shape-top.webp') }}" class="img-fluid w-100" alt="section-shape-top">
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0 order-2 order-lg-2">
                    <h3 class="text-white mb-3">Get in Touch</h3>
                    <div class="row align-items-center">
                        <div class="col-lg-12 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="address-icon">
                                        <i class="fas fa-phone-alt"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3 content">
                                    <a href="tel:+91 9099 032 177" class="text-white mb-0">+91 9099 032 177</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="address-icon">
                                        <i class="far fa-envelope"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3 content">
                                    <a href="mailto:bharat@raindropsinfotech.com"
                                        class="text-white mb-0">bharat@raindropsinfotech.com</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="address-icon mt-1">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-white mb-0 lh-lg">A/804, Shivanta one, opp. Nalli Silk Sarees, next
                                        to Hare krishna Complex, Pritam Nagar, Paldi, Ahmedabad, Gujarat
                                        380007</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 mb-5 mb-lg-0 order-1 order-lg-2">
                    <div class="footerbox-wrapper">
                        <div class="mb-4 footer-logo">
                            <img src="{{ asset('landing-page/images/logo.webp') }}" class="logo-img" alt="brand-logo">
                        </div>
                        <p class="mb-0 footer-text">End-to-end platform for bookings, seed distribution, payments, and
                            storage tracking. Built
                            for real agriculture
                            challenges — trusted by dealers and farmers alike. Simplify, Automate, Grow with Smart Cold
                            Storage Technology.</p>
                    </div>
                </div>
                <div class="col-lg-3 order-2 order-lg-3">
                    <div class="mb-5 mb-lg-4">
                        <h3 class="text-white text-lg-end mb-3">Working Hours</h3>
                        <ul class="footer-hour-list">
                            <li>
                                <div class="hours-info">
                                    <p>Mon - Fri :<span>9:00 AM - 7:00 PM</span></p>
                                </div>
                            </li>
                            <li>
                                <div class="hours-info">
                                    <p>Sat - Sun:<span>Closed</span></p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <h3 class="text-white text-lg-end mb-3">Follow Us</h3>
                    <ul class="footer-social-style">
                        <li>
                            <a href="https://www.facebook.com/raindrops.infotech/" target="_blank"><i
                                    class="fab fa-facebook-f"></i></a>
                        </li>
                        <li>
                            <a href="https://api.whatsapp.com/send/?phone=919099032177&text=Hi%2C+would+you+please+give+me+a+call+so+that+we+can+discuss+about+the+requirement%3F&type=phone_number&app_absent=0"
                                target="_blank"><i class="fab fa-whatsapp"></i></a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/raindrops_tech/" target="_blank"><i
                                    class="fab fa-instagram"></i></a>
                        </li>
                        <li>
                            <a href="https://www.linkedin.com/company/raindrops-infotech/" target="_blank"><i
                                    class="fab fa-linkedin-in"></i></a>
                        </li>
                        <li>
                            <a href="https://in.pinterest.com/raindropsinfotech/" target="_blank"><i
                                    class="fab fa-pinterest"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bar">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <p class="d-inline-block mb-0 align-middle copyright-text">Copyright © 2025 All Rights
                                Reserved And
                                Developed By
                                <a href="https://www.raindropsinfotech.com/" target="_blank">Raindrops Infotech.</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end footer section -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Get A Quote</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="Inquiry_Form" method="POST" action="{{ route('quote.submit') }}" id="myForm"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" for="exampleForm.ControlInput1">Your Name</label>
                                <input name="name" placeholder="Enter Your Name" type="text" id="name"
                                    class="form-control " value="">
                                <span class="error text-danger m-1" id="nameError"></span>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" for="exampleForm.ControlInput2">Your Email</label>
                                <input name="email" placeholder="Enter Your Email" type="text" id="email"
                                    class="form-control " value="">
                                <span class="error text-danger m-1" id="emailError"></span>
                            </div>
                        </div>


                        <div class="col-md-12 mb-3">
                            <label for="" class="form-label">Telephone</label>
                            <input name="mobile_code" type="tel" id="mobile_code" class="form-control"
                                placeholder="Phone Number">
                            <input type="hidden" name="full_number" id="full_number">
                            <input type="hidden" name="dialCode" id="dialCode">
                            <span class="error text-danger m-1" id="phoneError"></span>
                        </div>
                        <div class="col-md-12 mb-1">
                            <label class="form-label" for="exampleForm.ControlTextarea1">Message</label>
                            <textarea name="comments" rows="3" placeholder="Requirements/Comments" id="comment"
                                class="form-control "></textarea>
                            <span class="error text-danger m-1" id="commentsError"></span>
                        </div>
                        <!-- CAPTCHA -->
                        <div class="row">
                            <div class="col-md-12 mb-1">
                                <div class="col-md-12">
                                    <label class="form-label" for="captcha">CAPTCHA :</label>
                                    <label class="form-label">&nbsp;</label>
                                    <span id="captchaQuestion" class="text-center"></span>
                                </div>
                                <input name="captcha" placeholder="Enter the result" type="text" id="captcha"
                                    class="form-control">
                                <span class="error text-danger m-1" id="captchaError"></span>
                            </div>
                        </div>
                        <input type="hidden" id="captchaAnswer" name="captchaAnswer">
                        <div class="col-md-12">
                            <button name="submit" type="button" id="myformsubmit"
                                class="btn primary-btn w-100">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Get A Quote Modal -->

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Get A Quote</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <label class="form-label" for="exampleForm.ControlInput1"><b> Your Quote has been submitted
                                </b>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Javascripts -->
    <script src="{{ asset('landing-page/js/jquery.min.js') }}"></script>
    <script src="{{ asset('landing-page/js/popper.min.js') }}"></script>
    <script src="{{ asset('landing-page/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('landing-page/js/owl.carousel.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="{{ asset('landing-page/js/custom.js') }}"></script>
</body>