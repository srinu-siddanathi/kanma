<header>
    <div class="container-fluid">
        <div class="row py-3 border-bottom align-items-center g-0">
            <div class="col-sm-4 col-lg-2 text-center text-sm-start">
                <div class="main-logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="logo" class="img-fluid">
                    </a>
                </div>
            </div>

            <div class="col-sm-4 col-lg-2 pe-lg-2">
                <div class="location-selector d-flex align-items-center bg-light p-2 rounded-4" data-bs-toggle="modal"
                    data-bs-target="#locationModal" style="cursor: pointer;">
                    <svg width="24" height="24" class="me-2">
                        <use xlink:href="#location"></use>
                    </svg>
                    <div class="fw-medium text-truncate">Select your location</div>
                    <svg width="16" height="16" class="ms-auto">
                        <use xlink:href="#chevron-down"></use>
                    </svg>
                </div>
            </div>

            <div class="col-sm-6 offset-sm-2 offset-md-0 col-lg-6 d-none d-lg-block ps-lg-2">
                <div class="search-bar row bg-light p-2 my-2 rounded-4">
                    <div class="col-md-4 d-none d-md-block">
                        <select class="form-select border-0 bg-transparent">
                            <option>All Categories</option>
                            <option>Vegetables</option>
                            <option>Fruits</option>
                            <option>Meat</option>
                        </select>
                    </div>
                    <div class="col-11 col-md-7">
                        <form id="search-form" class="text-center" action="index.html" method="post">
                            <input type="text" class="form-control border-0 bg-transparent"
                                placeholder="Search for more than 20,000 products">
                        </form>
                    </div>
                    <div class="col-1">
                        <svg width="24" height="24">
                            <use xlink:href="#search"></use>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="col-sm-8 col-lg-2 d-flex justify-content-end gap-3 align-items-center mt-4 mt-sm-0">
                <ul class="d-flex justify-content-end list-unstyled m-0">
                    <li>
                        <a href="#" class="rounded-circle bg-light p-2 mx-1" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasCart" aria-controls="offcanvasCart">
                            <svg width="24" height="24">
                                <use xlink:href="#cart"></use>
                            </svg>
                            <span class="badge bg-primary rounded-pill">3</span>
                        </a>
                    </li>
                    @auth
                    <li class="dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('orders') }}">My Orders</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li>
                        <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#loginModal">Login</a>
                    </li>
                    @endauth
                </ul>

            </div>
        </div>
    </div>

    <!-- Navigation Bar -->
    <div class="container-fluid">
        <div class="row py-3">
            <div class="d-flex  justify-content-center justify-content-sm-between align-items-center">
                <nav class="main-menu d-flex navbar navbar-expand-lg">

                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                        aria-labelledby="offcanvasNavbarLabel">

                        <div class="offcanvas-header justify-content-center">
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>

                        <div class="offcanvas-body">

                            <select class="filter-categories border-0 mb-0 me-5">
                                <option>Shop by Departments</option>
                                <option>Groceries</option>
                                <option>Drinks</option>
                                <option>Chocolates</option>
                            </select>

                            <ul class="navbar-nav justify-content-end menu-list list-unstyled d-flex gap-md-3 mb-0">
                                <li class="nav-item">
                                    <a href="{{ route('home') }}" class="nav-link">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('shop') }}" class="nav-link">Shop</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('about') }}" class="nav-link">About Us</a>
                                </li>
                                <li class="nav-item active">
                                    <a href="{{ route('cart') }}" class="nav-link">Cart</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="{{ route('checkout') }}" class="nav-link">Checkout</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('product.show', ['id' => 1]) }}" class="nav-link">Product View</a>
                                </li>
                                <!-- <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" role="button" id="pages"
                                        data-bs-toggle="dropdown" aria-expanded="false">Pages</a>
                                    <ul class="dropdown-menu" aria-labelledby="pages">
                                        <li><a href="index.html" class="dropdown-item">About Us </a></li>
                                        <li><a href="index.html" class="dropdown-item">Shop </a></li>
                                        <li><a href="index.html" class="dropdown-item">Single Product </a></li>
                                        <li><a href="index.html" class="dropdown-item">Cart </a></li>
                                        <li><a href="index.html" class="dropdown-item">Checkout </a></li>
                                        <li><a href="index.html" class="dropdown-item">Blog </a></li>
                                        <li><a href="index.html" class="dropdown-item">Single Post </a></li>
                                        <li><a href="index.html" class="dropdown-item">Styles </a></li>
                                        <li><a href="index.html" class="dropdown-item">Contact </a></li>
                                        <li><a href="index.html" class="dropdown-item">Thank You </a></li>
                                        <li><a href="index.html" class="dropdown-item">My Account </a></li>
                                        <li><a href="index.html" class="dropdown-item">404 Error </a></li>
                                    </ul>
                                </li> -->

                            </ul>

                        </div>

                    </div>
            </div>
        </div>
    </div>
</header>

<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Choose your delivery location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="location-search mb-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <svg width="24" height="24">
                                <use xlink:href="#location"></use>
                            </svg>
                        </span>
                        <input type="text" class="form-control border-start-0"
                            placeholder="Enter your delivery location">
                    </div>
                </div>
                <div class="detect-location d-flex align-items-center mb-4">
                    <button class="btn btn-outline-primary btn-sm">
                        <svg width="16" height="16" class="me-2">
                            <use xlink:href="#location-crosshairs"></use>
                        </svg>
                        Detect current location
                    </button>
                    <span class="text-muted ms-2 small">Using GPS</span>
                </div>
                <div class="saved-locations">
                    <h6 class="mb-3">Saved Locations</h6>
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Home</h6>
                                <small class="text-muted">Default</small>
                            </div>
                            <p class="mb-1 small text-muted">123 Main St, New York, NY 10001</p>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Office</h6>
                            </div>
                            <p class="mb-1 small text-muted">456 Business Ave, New York, NY 10002</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="loginModalLabel">Login to your account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('login') }}" class="px-3">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <svg width="16" height="16" class="password-toggle-icon">
                                    <use xlink:href="#eye"></use>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        <!-- Remove or comment out this line -->
                        {{-- <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot password?</a>
                        --}}
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>

                    <div class="text-center mb-4">
                        <span class="text-muted">or login with</span>
                    </div>

                    <div class="d-flex gap-2 mb-4">
                        <a href="#" class="btn btn-outline-secondary w-50">
                            <svg width="18" height="18" class="me-2">
                                <use xlink:href="#google"></use>
                            </svg>
                            Google
                        </a>
                        <a href="#" class="btn btn-outline-secondary w-50">
                            <svg width="18" height="18" class="me-2">
                                <use xlink:href="#facebook"></use>
                            </svg>
                            Facebook
                        </a>
                    </div>

                    <div class="text-center">
                        <span class="text-muted">Don't have an account?</span>
                        <a href="{{ route('register') }}" class="text-decoration-none">Register now</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>