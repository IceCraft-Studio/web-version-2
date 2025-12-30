<?php
$viewState = ViewData::getInstance();

// Current Page Logic
const CURRENT_PAGE_CLASS = "current-page";
$currentPage = $viewState->get('current-page');

//User Logic
$userLink = $viewState->get('user-link');
$userName = $viewState->get('username');
$userPictureLink = $viewState->get('user-profile-picture');

?>
<header id="topbar">
    <a class="icon-container" href="/~dobiapa2/home" title="IceCraft Studio">
        <img src="/~dobiapa2/assets/icecraft-logo.svg" alt="IceCraft Icon" class="no-select">
        <span translate="no">ICECRAFT STUDIO</span>
    </a>
    <button class="theme-toggle" title="Toggle Website Theme">
        <img src="/~dobiapa2/assets/icons/sun-moon-icon.png" alt="Theme Icon" class="no-select">
    </button>
    <nav class="links-container">
        <a href="/~dobiapa2/home" class="<?= $currentPage == 'home' ? CURRENT_PAGE_CLASS : "" ?>" hreflang="en" title="The homepage of our web.">
            Home
        </a>
        <a href="/~dobiapa2/projects" class="<?= $currentPage == 'projects' ? CURRENT_PAGE_CLASS : "" ?>" hreflang="en" title="Check out our maps, addons and more content.">
            Projects
        </a>
        <a href="/~dobiapa2/about" class="<?= $currentPage == 'about' ? CURRENT_PAGE_CLASS : "" ?>" hreflang="en" title="Links to our social media accounts and other platforms.">
            About
        </a>
    </nav>
    <div class="login-container">
        <a id="user-button-link" class="<?= $currentpage == 'user' ? CURRENT_PAGE_CLASS : "" ?>" href="<?= $userLink ?>" hreflang="en" title="<?= $userName ?>">      
            <img id="user-button-picture" src="<?= $userPictureLink ?>" alt="Profile picture" class="no-select">
            <span id="user-button-label"><?= $userName ?></span>
        </a>
    </div>
    <!-- Small viewport fallback hamburger menu (doesn't work without scripts)-->
    <button class="burger-menu-toggle">
        <img src="/~dobiapa2/assets/icons/tri-dash-icon.svg" alt="Burger Menu Icon" class="no-select">
    </button>
</header>
<nav id="links-dropdown">
    <!-- Contents of this element are inserted via a script (to avoid duplicate) from #topbar > .links-container -->
</nav>