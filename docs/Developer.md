# Data Structure

# Used Algorithms

# Design Guidelines

# Backend Architecture

The source code of the website is structured based on a predefined common architecture, which needs to be followed
in order to make the website function correctly.

By default, all requests that don't request a file are sent to a router.
The router checks if the request maps to a directory containing `head.php` or `head.html` and `body.php` or `body.html`.
If those files are present they are used to fill the body and head of the HTML response, 
otherwise 404 page is used instead.
The HTML response is also filled with common elements of all pages, which are located at `/src/common/pages/*`.
This logic is used in `/src/router.php`
