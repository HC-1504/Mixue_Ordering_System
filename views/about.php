<?php
// Set a unique title for this page
$page_title = 'About Us - The Mixue Story';

// Include the standard website header
require_once '../includes/header.php';
?>


<!-- =========================================================================
     1. PAGE BANNER
     ========================================================================== -->
<div class="page-banner" style="background-image: url('<?= BASE_URL ?>/assets/images/mixue-logo.png');">
    <div class="banner-content">
        <h1>Our Story</h1>
    </div>
</div>

<!-- The <main> tag is opened in header.php, we add content inside it -->

<!-- =========================================================================
     2. BRAND STORY SECTION
     ========================================================================== -->
<div class="brand-story-section">
    <div class="brand-story-image">
        <img src="<?= BASE_URL ?>/assets/images/snow-king.jpg" alt="Early days of Mixue">
    </div>
    <div class="brand-story-text">
        <h2>A Chain Brand Focusing on Ice Cream & Freshly-made Tea</h2>
        <p>
            MIXUE is a sweet business initiated by Mr. Zhang Hong Chao in Zhengzhou, Henan Province, China in 1997. It specializes in creating trendy ice cream and freshly-made tea drinks for young people. After more than 20 years of development, the number of global stores has exceeded 41,800 in China and overseas markets.
        </p>
        <p>
            Our core belief is that everyone deserves a taste of happiness, and we are dedicated to making that possible through our delicious and affordable treats.
        </p>
    </div>
</div>

<!-- =========================================================================
     3. MISSION & VISION SECTION
     ========================================================================== -->
<div class="mission-vision-section">
    <div class="mission-vision-grid">
        <!-- Vision Card -->
        <div class="vision-card">
            <h3><i class="fas fa-eye"></i> Our Vision</h3>
            <ul>
                <li><strong>Global Leadership:</strong> To be a dominant force in the global ice cream and tea market.</li>
                <li><strong>Innovation and Creativity:</strong> To continuously innovate and create new, exciting products.</li>
                <li><strong>Quality & Affordability:</strong> To maintain a perfect balance between high-quality products and affordable prices.</li>
                <li><strong>Customer Happiness:</strong> To bring joy and satisfaction to our customers with every sip and bite.</li>
            </ul>
        </div>
        <!-- Mission Card -->
        <div class="mission-card">
            <h3><i class="fas fa-rocket"></i> Our Mission</h3>
            <ul>
                <li><strong>Delighting Customers:</strong> To create positive and memorable experiences through our products.</li>
                <li><strong>Innovative Beverages:</strong> To commit to developing new and refreshing ice cream and tea drinks.</li>
                <li><strong>Upholding Standards:</strong> To maintain the strictest standards for quality, affordability, and service.</li>
                <li><strong>Creating Joy:</strong> The core of our mission is to simply bring happiness to our customers' lives.</li>
            </ul>
        </div>
    </div>
</div>

<!-- =========================================================================
     4. COMPANY STRUCTURE SECTION
     ========================================================================== -->
<div class="company-structure-section">
    <h2 class="section-title-mixue" style="margin-bottom: 10px;">A Complete Industrial Chain</h2>
    <p class="section-text-mixue">
        To make every ice cream and freshly-made tea drink perfect, the MIXUE brand is jointly served by three major companies, creating a powerful synergy that ensures quality from farm to cup.
    </p>

    <div class="company-grid">
        <div class="company-card">
            <!-- <img src="<?= BASE_URL ?>/assets/images/mixuebincheng.jpg" alt="MIXUEBINGCHENG Co., Ltd."> -->
            <h4>MIXUEBINGCHENG Co., Ltd.</h4>
            <p>Leads the day-to-day operations and management of all our stores, ensuring a consistent and high-quality customer experience.</p>
        </div>
        <div class="company-card">
            <!-- <img src="<?= BASE_URL ?>/assets/images/daka.jpg" alt="Daka International Foods Co., Ltd."> -->
            <h4>Daka International Foods Co., Ltd.</h4>
            <p>Heads our research, development, and production, constantly innovating to create the delicious new flavors you love.</p>
        </div>
        <div class="company-card">
            <!-- <img src="<?= BASE_URL ?>/assets/images/mixue-logo.png" alt="Shangdao Smart Supply Chain Co., Ltd."> -->
            <h4>Shangdao Smart Supply Chain Co., Ltd.</h4>
            <p>Provides state-of-the-art warehousing and logistics services, making sure fresh ingredients reach every store quickly and efficiently.</p>
        </div>
    </div>
</div>


<?php
// Include the standard website footer
require_once '../includes/footer.php';
?>