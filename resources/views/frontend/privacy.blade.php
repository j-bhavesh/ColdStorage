<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

         <style>
         *{
            margin: 0;
        }
        body {
              font-family: Arial, sans-serif;
              line-height: 1.5;
            }
         h1,h2,h3,h4,h5,h6{
            margin: 0;
         }
        .privacy-page{
            padding: 20px;
        }
        .main-header{
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #e9ecef;
            padding: 15px 20px;
            border-radius: 10px;
        }
        .main-header .main-logo{
            max-width: 140px;
        }
        .home-btn{
            background-color: #0d6efd;
            color: #fff;
            padding: 8px 24px;
            font-size: 16px;
            line-height: 24px;
            font-weight: 500;
            border-radius: 6px;
            display: inline-block;
            text-decoration: none;
        }

        .policy-maintitle{
            text-align: center;
            font-size: 40px;
            margin: 20px 0;
        }
        .policy-content{
            border: 1px solid #ededed;
            padding: 30px 20px;
            border-radius: 10px;
        }
        .policy-content .content{
            margin-bottom: 25px;
        }
        .policy-content .content h3{margin-bottom: 20px;font-size: 22px;}
        .policy-content .content h4{margin-bottom: 15px;}
        .policy-content .content p{
            margin: 0;
            color: #565151;
            margin-bottom: 20px;
        }
        .policy-content .content ul{margin-bottom: 20px;margin-top: 0;}
        .policy-content .content ul li{
            padding-bottom: 15px;
            color: #232323;
        }
        .content.no-margin{margin-bottom: 0;}
        .policy-content .content ul li:last-child{padding-bottom: 0;}
        .policy-content .content .no-margin{margin-bottom: 0;}
       </style>
    </head>
    <body>
        <section class="privacy-page">
            <div class="main-header">
                <img src="{{ asset('assets/images/brand-logo.png') }}" class="main-logo" alt="Ubrand-logo">
                <a href="/administrator/login" target="_blank" class="home-btn">Home</a>
            </div>
            <h2 class="policy-maintitle">Privacy Policy</h2>
            <div class="policy-content">
                <div class="content">
                    <h3>1. Introduction</h3>
                    <p>This Privacy Policy explains how we collect, use, store, and protect your information when you use the Cold Storage App (“App”). By using the App, you agree to the terms of this policy.</p>
                </div>
                <div class="content">
                    <h3>2. Information We Collect</h3>
                    <h4>Personal Information:</h4>
                    <ul>
                      <li>Farmer Name, Village, and Contact Number</li>
                      <li>Vehicle Numbers, Seed Distribution Records</li>
                      <li>Payment Details (Advance, Debit, Booking Info)</li>
                    </ul>
                    <h4>System Information:</h4>
                    <ul>
                      <li>Device ID and IP Address (for security/logging)</li>
                      <li>Timestamps of app usage and transactions</li>
                    </ul>
                </div>
                <div class="content">
                    <h3>3. How We Use Your Information</h3>
                    <ul class="no-margin">
                      <li>To manage farmer and booking records</li>
                      <li>To generate agreements, challans, and reports</li>
                      <li>To send SMS notifications for updates</li>
                      <li>To process and record payments and distributions</li>
                      <li>To improve user experience and system performance</li>
                    </ul>
                </div>
                <div class="content">
                    <h3>4. Data Sharing</h3>
                    <p>We do not sell, trade, or rent user information. Data may only be shared:</p>
                    <ul class="no-margin">
                      <li>With SMS gateway providers (for sending alerts)</li>
                      <li>With authorized dealers and admins (internal use only)</li>
                      <li>If required by law enforcement or government authorities</li>
                    </ul>
                </div>
                <div class="content">
                    <h3>5. Data Security</h3>
                    <p>We implement industry-standard measures to protect your data:</p>
                    <ul>
                      <li>Secure storage of records using Laravel backend</li>
                      <li>SSL encryption for all app-server communications</li>
                      <li>Role-based access controls to restrict data exposure</li>
                    </ul>
                </div>
                <div class="content">
                    <h3>6. User Rights</h3>
                    <p>Users (farmers or dealers) can:</p>
                    <ul>
                      <li>Request access to their data</li>
                      <li>Request correction or deletion of inaccurate data</li>
                      <li>Opt out of SMS notifications (where applicable)</li>
                    </ul>
                </div>
                <div class="content">
                    <h3>7. Data Retention</h3>
                    <p>Data is retained for operational, seasonal, and audit purposes. Old financial records are archived and accessible for transparency.</p>
                </div>
                <div class="content">
                    <h3>8. Third-Party Services</h3>
                    <p>The app uses third-party SMS gateways and may use cloud hosting providers. These services are contractually obligated to maintain data privacy and security.</p>
                </div>
                <div class="content">
                    <h3>9. Children's Privacy</h3>
                    <p>The App is not intended for children under 18. We do not knowingly collect data from minors.</p>
                </div>
                <div class="content no-margin">
                    <h3>10. Changes to This Policy</h3>
                    <p class="no-margin">We may update this policy occasionally. Users will be notified via app update or email.</p>
                </div>
            </div>
        </section>

    </body>
</html>
