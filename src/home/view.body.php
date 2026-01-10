<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/html-gen.php';

$viewState = ViewData::getInstance();
$projectsList = $viewState->get('projects-list');
?>
<main>
    <img id="main-logo" src="/~dobiapa2/assets/icecraft-logo-wide.webp" class="no-select" alt="IceCraft Studio Logo">
    <h1>Free Minecraft mods, VS Code extensions, Steam Workshop items and more!</h1>
    <span id="tagline">Stayin' Icy and Cool</span>
    <div id="about-us" class="info-table">
        <h2>About Us</h2>
        <p>
            We are a small non-profit group and a community creating free and tested content for Minecraft:
            Bedrock Edition.
            Our packs are supported as long as it's technically possible.
            When any breaking changes occur, we fix our packs to be working and updated for the latest version
            of the game. Our goal is to make enjoyable experiences for everyone and for free!
        </p>
    </div>
    <h2>Recently Added Projects</h2>
    <div id="add-ons" class="media-table">
        <?php
            createProjectsListing($projectsList, 'Could not load projects.');
        ?>
    </div>
    <div id="collaborations" class="info-table">
        <h2>Partners</h2>
        <p>
            IceCraft Studio is active within the community of Minecraft: Bedrock Edition and so we collaborate
            with other communities to bring all the awesome creations, out there together. We also help with
            development of the creations that those communities create.
        </p>
    </div>
</main>