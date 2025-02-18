<footer class="py-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="footer-menu">
                    <img src="{{ asset('images/logo.png') }}" alt="logo">
                    <div class="social-links mt-4">
                        <ul class="d-flex list-unstyled gap-2">
                            <li>
                                <a href="#" class="btn btn-outline-light">
                                    <svg width="24" height="24"><use xlink:href="#facebook"></use></svg>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-outline-light">
                                    <svg width="24" height="24"><use xlink:href="#twitter"></use></svg>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-outline-light">
                                    <svg width="24" height="24"><use xlink:href="#instagram"></use></svg>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-outline-light">
                                    <svg width="24" height="24"><use xlink:href="#youtube"></use></svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="footer-menu">
                    <h5 class="widget-title">Quick Links</h5>
                    <ul class="menu-list list-unstyled">
                        <li class="menu-item">
                            <a href="{{ route('home') }}" class="nav-link">Home</a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('shop') }}" class="nav-link">Shop</a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('about') }}" class="nav-link">About Us</a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('contact') }}" class="nav-link">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="footer-menu">
                    <h5 class="widget-title">Help & Info</h5>
                    <ul class="menu-list list-unstyled">
                        <li class="menu-item">
                            <a href="#" class="nav-link">FAQ</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Privacy Policy</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="nav-link">Terms & Conditions</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="footer-menu">
                    <h5 class="widget-title">Subscribe Us</h5>
                    <p>Subscribe to our newsletter to get updates about our grand offers.</p>
                    <form class="d-flex mt-3 gap-0">
                        <input type="email" class="form-control rounded-start rounded-0" placeholder="Email Address">
                        <button class="btn btn-dark rounded-end rounded-0">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</footer>

<div id="footer-bottom">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 copyright">
                <p>© {{ date('Y') }} Foodmart. All rights reserved.</p>
            </div>
            <div class="col-md-6 credit-link text-start text-md-end">
                <p>Free HTML Template by <a href="https://templatesjungle.com/">TemplatesJungle</a> Distributed by <a href="https://themewagon">ThemeWagon</a></p>
            </div>
        </div>
    </div>
</div>