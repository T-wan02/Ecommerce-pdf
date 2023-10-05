<div class="nav">
    <nav id="navbar">
        <!-- Left part of nav -->
        <div class="left">
            <div class="responsive-container">
                <!-- Burgur Menu -->
                <button class="toggle-nav" id="toggleNav">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <!-- X Mark -->
                <button><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="nav-item-container">
                <a href="#" class="linked active">Guides</a>
                <a href="#" class="linked">Content</a>
            </div>
        </div>

        <!-- Mid part of nav -->
        <div class="mid">
            <a href="{{ url('/') }}" class="logo"><img src="{{ asset('assets/vendor/brand/logo.png') }}"
                    width="150" alt=""></a>
        </div>

        <!-- Right part of nav -->
        <div class="right">
            <div class="nav-social-container">
                <a href="#" class="logo-link nav-logo">
                    <i class="fa-brands fa-youtube"></i>
                </a>
                <a href="#" class="logo-link nav-logo">
                    <i class="fa-brands fa-linkedin-in"></i>
                </a>
            </div>
            <div></div>
        </div>
    </nav>

    <!-- Responsive Navbar container -->
    <div class="responsive-nav-item-container animate__animated animate__fadeIn animate__fast"
        id="responsiveNavItemContainer">
        <div></div>
        <div class="nav-item-container animate__animated animate__fadeInUp animate__fast">
            <a href="#" class="linked active animate_animated animate__fadeInUp">Guides</a>
            <a href="#" class="linked">Content</a>
        </div>
        <div class="social-container d-flex gap-1 animate__animated animate__fadeInUp animate__faster">
            <a href="#" class="logo-link nav-logo">
                <i class="fa-brands fa-youtube"></i>
            </a>
            <a href="#" class="logo-link nav-logo">
                <i class="fa-brands fa-linkedin-in"></i>
            </a>
        </div>
    </div>
</div>
