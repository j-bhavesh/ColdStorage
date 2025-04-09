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
        .contact-page{
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
        .contact-maintitle{
            text-align: center;
            font-size: 40px;
            margin: 20px 0;
        }
        .contact-content{
            border: 1px solid #ededed;
            padding: 30px 20px;
            border-radius: 10px;
            text-align: center;
        }
        .contact-content img{
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .contact-content address{
            font-size: 18px;
            font-style: normal;
        }
       </style>
    </head>
    <body class="">
        <section class="contact-page">
            <div class="main-header">
                <img src="{{ asset('assets/images/brand-logo.png') }}" class="main-logo" alt="Ubrand-logo">
                <a href="/administrator/login" target="_blank" class="home-btn">Home</a>
            </div>
            <h2 class="contact-maintitle">Contact</h2>
            <div class="contact-content">
                <img src="{{ asset('assets/images/contact-image.webp') }}" alt="contact-image">
                <address>
                    Mahaveer overseas cold storage<br/>
                    Chandrala, Gandhinagar, Gujarat 382320<br/>
                    Contact No : 9428481410
                </address>
            </div>
        </section>

    </body>
</html>
