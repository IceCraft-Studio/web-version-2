<?php
// Current Page Logic
const CURRENT_PAGE_CLASS = "current-page";
$currentHome = $currentProjects = $currentAbout = $currentUser = false;
$route = normalizeUriRoute($_SERVER['REQUEST_URI']);

if (str_starts_with($route,'home')) {
    $currentHome = true;
} elseif (str_starts_with($route,'projects') || str_starts_with($route,'users/')) {
    $currentProjects = true;
} elseif (str_starts_with($route,'about')) {
    $currentAbout = true;
} elseif (str_starts_with($route,'login') || str_starts_with($route,'register')  || str_starts_with($route,'profile')  ) {
    $currentUser = true;
}

//User Logic
$userLink = "/~dobiapa2/login";
$userName = "Login";
$userPictureLink = "/~dobiapa2/assets/icons/steve.webp";


?>
<nav id="topbar">
    <a class="icon-container" href="/~dobiapa2/home" title="IceCraft Studio">
        <img src="/~dobiapa2/assets/icecraft-logo.svg" alt="IceCraft Icon" class="no-select">
        <span translate="no">ICECRAFT STUDIO</span>
    </a>
    <button class="theme-toggle" title="Toggle Website Theme">
        <img src="/~dobiapa2/assets/icons/sun-moon-icon.png" alt="Theme Icon" class="no-select">
    </button>
    <div class="links-container">
        <a href="/~dobiapa2/home" class="<?= $currentHome ? CURRENT_PAGE_CLASS : "" ?>" hreflang="en" title="The homepage of our web.">
            Home
        </a>
        <a href="/~dobiapa2/projects" class="<?= $currentProjects ? CURRENT_PAGE_CLASS : "" ?>" hreflang="en" title="Check out our maps, addons and more content.">
            Projects
        </a>
        <a href="/~dobiapa2/about" class="<?= $currentAbout ? CURRENT_PAGE_CLASS : "" ?>" hreflang="en" title="Links to our social media accounts and other platforms.">
            About
        </a>
    </div>
    <div class="login-container">
        <a id="user-button-link" class="<?= $currentUser ? CURRENT_PAGE_CLASS : "" ?>" href="<?= $userLink ?>" hreflang="en" title="<?= $userName ?>">      
            <img id="user-button-picture" src="<?= $userPictureLink ?>" alt="Profile picture" class="no-select">
            <span id="user-button-label"><?= $userName ?></span>
        </a>
    </div>
    <!-- Small viewport fallback hamburger menu (doesn't work without scripts)-->
    <button class="burger-menu-toggle">
        <img src="/~dobiapa2/assets/icons/tri-dash-icon.svg" alt="Burger Menu Icon" class="no-select">
    </button>
</nav>
<div id="links-dropdown">
    <!-- Contents of this element are inserted via a script (to avoid duplicate) from #topbar > .links-container -->
</div>