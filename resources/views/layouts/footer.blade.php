<footer>
         <div class="container">
            <div class="row mb-4 gap-4 gap-md-0">
               <div class="col-md-5 col-12">
                  <div class=" d-flex gap-2 flex-column">
                     <img src="/assets/img/logo.png" width="120px" alt="Footer logo">
                     <p class="fs-6">A complete chain of custody platform to manage drivers, track medical specimens, and monitor deliveries in real time with full compliance and audit-ready reporting.</p>
                  </div>
               </div>
               <div class="col-md-4 col-12">
                  <div class="footer-menu d-flex gap-4 justify-content-lg-evenly">
                     <div class="d-flex gap-2 flex-column">
                        <h4>Product</h4>
                        <ul class="list-unstyled">
                           <li onclick="location.href='{{ request()->is('/') ? '#features' : url('/').'#features' }}'">Features</li>
                           <li onclick="location.href='{{ request()->is('/') ? '#pricing' : url('/').'#pricing' }}'">Pricing</li>
                        </ul>
                     </div>
                     <div class="d-flex gap-2 flex-column">
                        <h4>Company</h4>
                        <ul class="list-unstyled">
                            
                           <li onclick="location.href='{{ request()->is('/') ? '#about-platform' : url('/').'#about-platform' }}'">About</li>
                           <li onclick="location.href='{{ request()->is('/') ? '#get-touch' : url('/').'#get-touch' }}'">Contact</li>
                        </ul>
                     </div>
                  </div>
               </div>
               <!--div class="col-md-3 col-12">
                  <div class="social-links d-flex justify-content-md-end align-items-center gap-3">
                     <div class="d-flex border-1 align-items-center justify-content-center border-dark border p-2 icon rounded-circle">
                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M2.02429 4.04833C3.1422 4.04833 4.04845 3.14208 4.04845 2.02416C4.04845 0.906249 3.1422 0 2.02429 0C0.906371 0 0.00012207 0.906249 0.00012207 2.02416C0.00012207 3.14208 0.906371 4.04833 2.02429 4.04833Z" fill="black"/>
                           <path d="M3.71097 5.40039H0.337361C0.151138 5.40039 0 5.55153 0 5.73775V15.8586C0 16.0448 0.151138 16.1959 0.337361 16.1959H3.71097C3.89719 16.1959 4.04833 16.0448 4.04833 15.8586V5.73775C4.04833 5.55153 3.89719 5.40039 3.71097 5.40039Z" fill="black"/>
                           <path d="M13.7642 4.83878C12.3223 4.34489 10.5188 4.77873 9.43719 5.55669C9.40008 5.41162 9.26784 5.30367 9.11063 5.30367H5.73702C5.5508 5.30367 5.39966 5.4548 5.39966 5.64103V15.7618C5.39966 15.9481 5.5508 16.0992 5.73702 16.0992H9.11063C9.29685 16.0992 9.44799 15.9481 9.44799 15.7618V8.48835C9.99316 8.01874 10.6955 7.86896 11.2704 8.11321C11.8277 8.34868 12.1469 8.92355 12.1469 9.68935V15.7618C12.1469 15.9481 12.298 16.0992 12.4842 16.0992H15.8578C16.0441 16.0992 16.1952 15.9481 16.1952 15.7618V9.00991C16.1567 6.23748 14.8525 5.21123 13.7642 4.83878Z" fill="black"/>
                        </svg>
                     </div>
                     <div class="d-flex border-1 align-items-center justify-content-center border-dark border p-2 icon rounded-circle">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M16.4555 1.93604H6.77562C4.10397 1.93604 1.93567 4.10433 1.93567 6.77599V16.4559C1.93567 19.1276 4.10397 21.2959 6.77562 21.2959H16.4555C19.1272 21.2959 21.2955 19.1276 21.2955 16.4559V6.77599C21.2955 4.10433 19.1272 1.93604 16.4555 1.93604ZM11.6156 16.4559C8.94392 16.4559 6.77562 14.2876 6.77562 11.6159C6.77562 8.94429 8.94392 6.77599 11.6156 6.77599C14.2872 6.77599 16.4555 8.94429 16.4555 11.6159C16.4555 14.2876 14.2872 16.4559 11.6156 16.4559ZM16.7943 7.37614C16.2619 7.37614 15.8263 6.94055 15.8263 6.40815C15.8263 5.87576 16.2619 5.44016 16.7943 5.44016C17.3267 5.44016 17.7623 5.87576 17.7623 6.40815C17.7623 6.94055 17.3267 7.37614 16.7943 7.37614Z" fill="black"/>
                           <path d="M11.6159 14.5199C13.2197 14.5199 14.5199 13.2197 14.5199 11.6159C14.5199 10.0121 13.2197 8.71191 11.6159 8.71191C10.0121 8.71191 8.71191 10.0121 8.71191 11.6159C8.71191 13.2197 10.0121 14.5199 11.6159 14.5199Z" fill="black"/>
                        </svg>
                     </div>
                     <div class="d-flex border-1 align-items-center justify-content-center border-dark border p-2 icon rounded-circle">
                        <svg width="10" height="17" viewBox="0 0 10 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M6.94919 2.75717H9.14367C9.34556 2.75717 9.50942 2.60277 9.50942 2.41252V0.344646C9.50942 0.154401 9.34556 0 9.14367 0H6.94919C4.7313 0 2.92597 1.70048 2.92597 3.79111V6.20363H0.365747C0.163855 6.20363 0 6.35803 0 6.54828V8.61615C0 8.8064 0.163855 8.9608 0.365747 8.9608H2.92597V16.1984C2.92597 16.3886 3.08983 16.543 3.29172 16.543H5.4862C5.68809 16.543 5.85195 16.3886 5.85195 16.1984V8.9608H8.41218C8.56945 8.9608 8.70916 8.86568 8.75964 8.72506L9.49113 6.65719C9.52844 6.55241 9.50942 6.43661 9.44066 6.34631C9.37117 6.25671 9.26144 6.20363 9.14367 6.20363H5.85195V3.79111C5.85195 3.22106 6.34424 2.75717 6.94919 2.75717Z" fill="black"/>
                        </svg>
                     </div>
                  </div>
               </div-->
            </div>
            <div class="d-flex flex-wrap flex-md-nowrap justify-content-between align-items-center bootom-footer pb-3">
               <p>Copyright © 2026 {{env('APP_NAME')}}</p>
               <div class="d-flex justify-content-end terms-footer">
                  <ul class="list-unstyled d-flex gap-4 mb-0">
                        <!-- <li><a id="terms_of_use" href="{{ route('terms-of-use') }}">Terms of Use</a></li> -->
                        <li><a id="privacy_policy" href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                        <!-- <li><a id="cookie_policy" href="{{ route('cookie-policy') }}">Cookie Policy</a></li> -->
                  </ul>
               </div>
            </div>
         </div>
      </footer>
      <!-- Popup -->
      <div class="modal fade modal-lg wishlist-pop" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
               <div class="modal-header d-flex flex-column border-0 p-4">
                  <h2 class="modal-title text-center fs-2" id="exampleModalToggleLabel">Reserve Your Spot Today</h2>
                  <p>Submit the form to get early access and updates.</p>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body p-4">
                  <form id="reserveForm">
                     @csrf
                     <div class="row row-gap-4">
                        <div class="col-md-6 col-12">
                           <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="col-md-6 col-12">
                           <input type="text" name="company_name" class="form-control" placeholder="Company Name">
                        </div>
                        <div class="col-md-6 col-12">
                           <input type="text" name="phone" class="form-control" placeholder="Mobile no." required>
                        </div>
                        <div class="col-md-6 col-12">
                           <input type="email" name="email" class="form-control" placeholder="Email address" required>
                        </div>
                        <div class="col-12">
                           <textarea name="message" class="form-control" placeholder="Message" rows="4" id=""></textarea>
                        </div>
                        <input type="hidden" value="" name="plan_id" id="plan_id" >
                        <div class="d-flex justify-content-center align-items-center">
                           <button type="submit" class="primary-btn text-dark">Join for Early Access</button>
                           &nbsp;&nbsp;&nbsp;&nbsp;<div class="form-spinner spinner-border text-success" role="status"></div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>