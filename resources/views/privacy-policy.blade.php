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
            <b>Last updated: June 29, 2026</b>
            <p><br>
                <b>1. Introduction</b><br>
                We are ReliaStatTech. This privacy policy explains what personal information we collect when you use our services, how we use it, and how you can contact us with any questions.
            </p>
            <p>
                <br>
                <b>2. Information We Collect</b><br>
                We collect information that you voluntarily provide to us when you register, express interest in our services, or contact us. This may include:
                <br>
                • <b>Account Information:</b> Names, email addresses, and contact details<br>
                • <b>Log and Usage Data:</b> Information our servers automatically collect when you use our app, such as device IDs, IP addresses, and usage activity.<br>
                • <b>Geolocation Information:</b> We may request access to track location data to provide logistics and delivery services.
            </p>
            <p><br>
                <b>3. How We Use Your Information</b><br>
                We process your information to provide our services and improve your experience. This includes:
                <br>
                • Facilitating account creation and authentication.<br>

                • Delivering and managing logistics and delivery workflows.<br>

                • Responding to user inquiries and offering support.
            </p>
            <br>
            <p>
                <b>4. Data Sharing and Disclosure</b><br>
                We only share information with third parties when necessary, such as with service providers who assist in our business operations (e.g., payment processors or cloud storage providers). We do not sell your personal data.
            </p>
            <br>
            <p>
                <b>5. Data Security</b><br>
                We take appropriate security measures to prevent unauthorized access, disclosure, or destruction of your data. While no system is perfectly secure, we use industry-standard practices to protect your information.
            </p>
            <br><br>
            <b>Proposed HIPAA/PHI Compliance Addendum</b><br><br>
            <p>
                <b>Data Security (HIPAA/PHI Protection)</b><br>
                ReliaStatTech acknowledges the sensitivity of Protected Health Information (PHI) processed through our platform. We are committed to maintaining compliance with the Health Insurance Portability and Accountability Act (HIPAA). All PHI is stored in an encrypted environment utilizing AWS S3 buckets with server-side encryption enabled to ensure data at rest is protected. Access to PHI is restricted to authorized personnel only, and we implement technical, physical, and administrative safeguards to prevent unauthorized access or disclosure.
            </p><br>
            <p>
                <b>Data Retention</b><br>
                In alignment with HIPAA regulations, we retain PHI and associated documentation for a minimum period of six (6) years from the date of creation or the date it was last in effect, whichever is later. Upon the expiration of this retention period, data is securely disposed of in accordance with established industry standards.
            </p>
            <br>
            <p>
                <b>6. Your Privacy Rights</b><br>
                Depending on your location, you may have the right to access, correct, or request the deletion of your personal data. To exercise these rights or for any privacy concerns, please contact us at:
                <br><b> • Email: </b><a href="mailto:support@reliastattech.com">support@reliastattech.com</a>
            </p>
            <br>
            <p>
                <b>7. Updates to This Policy</b><br>
                We may update this policy from time to time. When we do, we will revise the date at the top of this page.
            </p>
        </div>
    </section>
    @include('layouts.footer')
</body>

</html>