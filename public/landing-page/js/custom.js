$(document).ready(function() {
    
	// features-carousel
        $('.features-carousel').owlCarousel({
            loop: true,
            responsiveClass: true,
            autoplay: true,
            smartSpeed: 1500,
            nav: false,
            dots: true,
            center:false,
            margin: 30,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1200: {
                    items: 3
                }
            }
        });

    // App screenshot slide
    $(".app_screenshots_slides").owlCarousel({
        items: 1,
        loop: true,
        responsiveClass: true,
        autoplay: true,
        smartSpeed: 1500,
        margin: 40,
        center: true,
        dots: true,
        responsive: {
            0: {
                items: 1,
                center: false,
                margin:0
            },
            576: {
                items: 1,
                center: false,
                margin:0
            },
            768: {
                items: 3
            },                
            992: {
                items: 3
            },
            1200: {
                items: 3
            }                
        }
    });

    var phone_number = window.intlTelInput(document.querySelector("#mobile_code"), {
            separateDialCode: true,
            preferredCountries:["in"],
            hiddenInput: "full",
            utilsScript: "//cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/js/utils.js"
    });

    $("#myForm").submit(function (event) {
        // Prevent default form submission
        event.preventDefault();

        // Get the selected country code
        var countryData = phone_number.getSelectedCountryData();
        var countryCode = countryData.dialCode;

        // Get the full phone number
        var full_number = phone_number.getNumber(intlTelInputUtils.numberFormat.E164);

        // Update the hidden input field with the full phone number
        $("input[name='full_number']").val(full_number);

        // Perform form submission
        this.submit();
    });
});

$(document).ready(function() {
    generateCaptcha();

    // Mobile Number
    var iti = window.intlTelInput(document.querySelector("#mobile_code"), {
        separateDialCode: true,
        preferredCountries:["in"],
        hiddenInput: "full",
        utilsScript: "//cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/js/utils.js"
    });

    // Attach click event handler to the submit button
    $("#myformsubmit").click(function(event) {
        // Prevent default button click behavior
        event.preventDefault();

        $(".error").html("");

        var fullNumber = iti.getNumber();
        var dialCode = iti.getSelectedCountryData().dialCode;
        $("#full_number").val(fullNumber);
        $("#dialCode").val(dialCode);

        // Create FormData object
        var formData = new FormData($("#myForm")[0]);

        // Your validation code here
        var name = formData.get("name");
        var email = formData.get("email");
        // var file = formData.get("file");
        var phone = formData.get("mobile_code");
        var comments = formData.get("comments");
        var captcha = formData.get("captcha");
        var captchaAnswer = $("#captchaAnswer").val(); // Get captcha answer from hidden input
        var isValid = true;

        if (name == "") {
            $("#nameError").html("Please enter your name");
            isValid = false;
        }
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            $("#emailError").html("Please enter a valid email address");
            isValid = false;
        }
        if (email == "") {
            $("#emailError").html("Please enter your email");
            isValid = false;
        }
        //if (file && file.size > 2 * 1024 * 1024) { // 2MB limit
            //$("#fileError").html("File size must be less than 2MB");
            //isValid = false;
        //}
        if (phone == "") {
            $("#phoneError").html("Please enter your Phone Number");
            isValid = false;
        } else if (!/^\d{10}$/.test(phone)) {
            if (phone.length < 10) {
                $("#phoneError").html("Phone number must be at least 10 digits");
            } else if (phone.length > 10) {
                $("#phoneError").html("Phone number must be at most 10 digits");
            } else {
                $("#phoneError").html("Phone number must contain only digits");
            }
            isValid = false;
        }

        if (comments == "") {
            $("#commentsError").html("Please enter your Comments/Message");
            isValid = false;
        }

        if (captcha == "") {
            $("#captchaError").html("Please enter your Captcha");
            isValid = false;
        } else if (captcha !== captchaAnswer) { // Check if captcha answer is correct
            $("#captchaError").html("Incorrect Captcha answer");
            isValid = false;
        }

        // If all fields are valid, submit the form
        if (isValid) {
            // $('#myformsubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Sending...');
            $.ajax({
                url: $("#myForm").attr("action"),
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#myformsubmit').prop('disabled', false).html('Send');

                    if (response.status === 'success') {
                        $("#exampleModal").modal("hide");
                        $('#staticBackdrop').modal('show');

                        generateCaptcha();
                        $('#myForm')[0].reset();
                        $("#nameError").html("");
                        $("#emailError").html("");
                        $("#phoneError").html("");
                        $("#commentsError").html("");
                        // setTimeout(() => location.reload(true), 2000);
                    } else {
                        alert(response.message || 'Something went wrong.');
                    }

                },
                error: function(xhr, status, error) {
                    $('#myformsubmit').prop('disabled', false).html('Send');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.name) $("#nameError").html(errors.name[0]);
                        if (errors.email) $("#emailError").html(errors.email[0]);
                        if (errors.mobile_code) $("#phoneError").html(errors.mobile_code[0]);
                        if (errors.comments) $("#commentsError").html(errors.comments[0]);
                    } else {
                        console.error(xhr);
                        alert('Server error. Please try again later.');
                    }

                }

            });

        }
    });
});

function generateCaptcha()
{
    var num1 = Math.floor(Math.random() * 10) + 1;
    var num2 = Math.floor(Math.random() * 10) + 1;
    var operator = ['+', '-', '*'][Math.floor(Math.random() * 3)];
    var answer;

    switch (operator) {
        case '+':
            answer = num1 + num2;
            break;
        case '-':
            answer = num1 - num2;
            break;
        case '*':
            answer = num1 * num2;
            break;
    }

    $('#captchaQuestion').text(num1 + ' ' + operator + ' ' + num2 + ' = ?');
    $('#captchaAnswer').val(answer);
}

// $(document).ready(function() {
    
// });

