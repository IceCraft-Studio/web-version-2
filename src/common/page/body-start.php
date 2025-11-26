<?php
const CURRENT_PAGE_CLASS = "current-page";
$currentHome = $currentProjects = $currentAbout = $currentUser = false;
$uri = substr($_SERVER['REQUEST_URI'],11);

if (str_starts_with($uri,'home')) {
    $currentHome = true;
} elseif (str_starts_with($uri,'projects')) {
    $currentProjects = true;
} elseif (str_starts_with($uri,'about')) {
    $currentAbout = true;
} elseif (str_starts_with($uri,'login') || str_starts_with($uri,'register')  || str_starts_with($uri,'user')  ) {
    $currentUser = true;
}
?>
<nav id="topbar">
    <a class="icon-container" href="/~dobiapa2/home" title="IceCraft Studio">
        <img src="/~dobiapa2/assets/icecraft-logo.svg" alt="IceCraft Icon">
        <span translate="no">ICECRAFT STUDIO</span>
    </a>
    <button class="theme-toggle" title="Toggle Website Theme">
        <img src="/~dobiapa2/assets/icons/sun-moon-icon.png" alt="Theme Icon">
    </button>

    <div class="login-container">
        <a id="user-button-link" class="<?= $currentUser ? CURRENT_PAGE_CLASS : "" ?>" href="/~dobiapa2/login" hreflang="en" title="Login">      
            <img id="user-button-picture" src="/~dobiapa2/assets/icons/steve.webp" alt="Placeholder profile picture">
            <span id="user-button-label">Login</span>
        </a>
    </div>
    <!-- Small viewport fallback (doesn't work without scripts)-->
    <div class="tri-dash-menu">
        <button class="dash-menu-toggle">
            <img src="/~dobiapa2/assets/icons/tri-dash-icon.svg">
        </button>
        <div class="links-dropdown">
            <!-- Contents of this element are inserted via a script (to avoid duplicate) from #topbar > .links-container -->
        </div>
    </div>
</nav>
    <div class="links-dropdown">
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