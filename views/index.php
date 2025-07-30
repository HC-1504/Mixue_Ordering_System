<?php
// Set a unique title for this page
$page_title = 'Welcome to Mixue - Fresh Ice Cream & Quality Tea';

// Include the standard website header
require_once '../includes/header.php';
?>

<!--
This new homepage is built with semantic sections for better structure and SEO.
The <main> tag is now opened in the header, so we just add our content.
-->

<!-- 1. HERO SECTION -->
<div class="hero-section">
    <div class="hero-content">
        <h1>Sweetness & Happiness</h1>
        <p>Discover a world of joy with our fresh ice cream and quality-brewed tea, made with love.</p>
        <a href="<?= BASE_URL ?>/views/index.php?controller=menu&action=index" class="btn btn-primary btn-lg btn-icon">
            <i class="fas fa-ice-cream"></i> Explore Our Menu
        </a>
    </div>
</div>

<!-- 2. FEATURED PRODUCTS SECTION -->
<div class="content-section">
    <h2 class="section-title">Our Fan Favorites</h2>
    <p class="section-subtitle">Loved by millions, these are the treats that keep our customers coming back for more.</p>

    <div class="featured-products">
        <!-- Product Card 1: Ice Cream -->
        <div class="product-card" style="animation-delay: 0.1s;">
            <img src="<?= BASE_URL ?>/assets/images/ice-cream.png" alt="Mixue Ice Cream Cone">
            <h3>Signature King Cone</h3>
            <p>Crispy, fresh-made cone filled with our creamy, rich vanilla soft serve. A timeless classic!</p>
            <a href="<?= BASE_URL ?>/views/menu.php" class="btn btn-secondary btn-icon">
                <i class="fas fa-arrow-right"></i> Order Now
            </a>
        </div>

        <!-- Product Card 2: Lemon Tea -->
        <div class="product-card" style="animation-delay: 0.2s;">
            <img src="<?= BASE_URL ?>/assets/images/lemon.jpeg" alt="Mixue Fresh Lemonade">
            <h3>Fresh-Squeezed Lemonade</h3>
            <p>Perfectly balanced sweet and sour, made from fresh lemons for the ultimate refreshment.</p>
            <a href="<?= BASE_URL ?>/views/menu.php" class="btn btn-secondary btn-icon">
                <i class="fas fa-arrow-right"></i> Order Now
            </a>
        </div>

        <!-- Product Card 3: Milk Tea -->
        <div class="product-card" style="animation-delay: 0.3s;">
            <img src="<?= BASE_URL ?>/assets/images/milk-tea.jpeg" alt="Mixue Pearl Milk Tea">
            <h3>Pearl Milk Tea</h3>
            <p>Our signature black tea blend with creamy milk and chewy, delicious boba pearls.</p>
            <a href="<?= BASE_URL ?>/views/menu.php" class="btn btn-secondary btn-icon">
                <i class="fas fa-arrow-right"></i> Order Now
            </a>
        </div>
    </div>
</div>

<!-- 3. ABOUT US PREVIEW SECTION -->
<div class="content-section" style="background-color: #f8f9fa; padding: 60px 2rem;">
    <h2 class="section-title">Our Story</h2>

    <div class="about-grid">
        <div class="about-image" style="animation-delay: 0.1s;">
            <img src="<?= BASE_URL ?>../assets/images/about-us-image.png" alt="Inside a Mixue store">
        </div>
        <div class="about-text" style="animation-delay: 0.2s;">
            <h3>High Quality & Affordable</h3>
            <p>Since 1997, Mixue has been committed to a simple mission: making high-quality ice cream and tea accessible to everyone. We source our own ingredients and manage our own supply chain to ensure every product we serve is fresh, delicious, and brings a smile to your face.</p>
            <p>We believe that happiness shouldn't be expensive. Come and experience the joy of Mixue!</p>
            <br>
            <a href="<?= BASE_URL ?>/views/about.php" class="btn btn-primary btn-icon">
                <i class="fas fa-book-open"></i> Learn More About Us
            </a>
        </div>
    </div>
</div>

<?php
// Include the standard website footer
require_once '../includes/footer.php';
?>