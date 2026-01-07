<?php

$viewState = ViewData::getInstance();

$verifiedRole = $viewState->get('verified-role');
$viewerIsAdmin = $verifiedRole === UserRole::Admin->value || $verifiedRole === UserRole::Owner;
$viewState->set('viewer-admin',$isAdmin);