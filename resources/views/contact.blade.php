<!DOCTYPE html>
<html lang="en">
@include('layouts.header')

<body>
    <header class="d-flex align-items-center">
        <div class="container border-1 border-bottom py-3">
            <div class="d-flex align-items-center justify-content-between ">
                <div class="logo">
                    <a href="/"><img src="/assets/img/logo.png" width="76%" class="img-fluid" alt="logo"></a>
                </div>
                <div class="head-btn d-flex gap-2 top-right">
                    <button class="primary-btn" onclick="location.href='{{ url('/') }}#get-touch'">
                        Contact Us
                    </button>
                    <!-- <button class="primary-btn" onclick="location.href='/login'">
                  Login
                  </button> -->
                </div>
            </div>
        </div>
    </header>
    <section class="py-3 py-md-5" id="hippa">
        <div class="container">
            <b>We’re Here to Help</b>
            <p>
                Have a question, need assistance, or want to learn more about ReliaStatTech? Our team is here to help. Whether you need support with our services, have a technical question, or would like to discuss a business opportunity, feel free to reach out.
            </p>
            <br>
            <b>Get in Touch</b>
                <b>Email Support</b>
            <p>
                For general inquiries, technical assistance, and customer support, contact us at:

                <strong>support@reliastattech.com</strong>

                We aim to respond to all inquiries as promptly as possible.    
            </p>
            <br>
            <p>
                <b>How We Can Help?</b>
                <ul>
                    <li>Safe and reliable transportation of medical specimens and laboratory samples.</li>
                    <li>Coordinated pickup and delivery services for hospitals, laboratories, clinics, and healthcare facilities.</li>
                    <li>Dependable transportation for urgent and scheduled medical deliveries.</li>
                    <li>Support for specimens and medical materials that require specific temperature conditions during transit.</li>
                    <li>Real-time delivery status and tracking to help you stay informed throughout the transportation process.</li>
                </ul>
            </p>
            <br>
            <p>
                <b>Send Us a Message</b>
            
                Please include your name, email address, and a brief description of your inquiry so our team can assist you efficiently.
                <br>
                <strong>ReliaStatTech — Technology You Can Rely On.</strong>
            </p>
        </div>
    </section>
    @include('layouts.footer')
</body>

</html>