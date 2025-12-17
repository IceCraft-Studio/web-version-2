# Deployment

This project is designed to work with Apache 2.4 and PHP 8.1 running on a modern Linux system (kernel 6.0+).
The `/src` directory contains what is intended to be in the root folder for the Apache instance to start the web server.

# Backend Architecture and Code Structure

The source code of the website is structured based upon a predefined common architecture, which needs to be followed
in order to make the website function correctly.

- The primary design principal is structuring code into files in directories based on their relation.
    - All source code files related only to a single specific page (PHP, JS, CSS) should be in their own directory - its path in `/src` is the URL route the user is presented with.
    - All static assets belong in `/src/assets`. This is a reserved path for that purpose.
    - All CSS, JS and PHP files common to ALL pages belong in `/common`.
    - All resuable PHP scripts (e.g. models, database access) belong in `/api/libs`.
    - API requests must be a `.php` file scripted with a standalone PHP logic. Can use reusuable logic scripts. 
        - All API requests for generic data should follow the standart JSON format. 
        - All API requests used for internal functions of the website belong in `/src/api/internal`.
        - All API requests used for access by external parties belong in `/src/api` outside of `/src/api/internal`.
    - All dynamic (e.g. user-generated) assets should be handled as an API request. This API access should use GET method and url query parameters to determine what to load.
    - All AJAX data access and modification (e.g. getting categories, banning users etc.) should be handled as an API request.
- This codebase uses mostly functional programming paradigm and minimal amount of OOP.
- The code should aim to be stright forward, with minimal boilerplate and as simple as possible for the desired function. No over-engineering.
- The HTTP access to a specific pages is done by URL directories. The user SHOULD NOT see file extensions in page URLs!
- URL parameters are used when they have some semantic sense, not for e.g. routing or any other internal logic. A good use of URL parameters is for paging and filtering of list pages.

- The main orchestrator of the entire website is the Front Controller in `/src/router.php`.
- The basic precondition for this system is `/src/.htaccess` file rewrites the requests recieved by Apache to let the Front Controller handle them based on this logic:
    - To cause 404 or 405 for files that shouldn't be accessed from the outside:
        - It rewrites all requests to `/api/libs/`.
        - It rewrites all requests to `.php` files outside `/api/`.
    - To delegate control to the correct controller:
        - It rewrites all requests to non files (existing and non-existing directories).
- The Front Controller then handles its requests in the following manner:
    - Redirects `/` to `/home/`
    - Redirects file requests not ending in `/` to directory requests ending in `/` to ensure relative resources always resolve correctly.
    - Internally reroutes specific requests with dynamic URIs using primitive "middleware".
    - Calls controllers on routes defined separately for each HTTP method. The lack of a controller for a method causes 405. This means all pages need a GET controller to be accesible, even an empty one is fine.
        - All controller file names are expected to follow this pattern - `controller.{method}.php`.
    - Calls views after controllers have run.
        - These include views common for all HTML responses and page specific ones.
        - If a view isn't defined, it is silently skipped as long as the controller exists.
        - All view file names are expected to follow this pattern - `view.{name}.php`.
        - Valid names are:
            - For common files - `head`, `body-start`, `body-end`.
            - For generic files - `head`, `body`.
        - The HTML response is constructed in a logical manner from these 5 pieces.

# Frontend Implemenataion

Based on the backend setup mentioned above, the frontend implementation needs to be adjusted to be compatible.


Middleware is used on the backend side to intercept routes that aren't defined in the filesystem and are instead
rerouted to a "middleware" resource that handles them by serving content dynamically based on the request URL. 
All middleware MUST NOT use relative resources on the server as the files are at a static place
and the URL is variable based on the client's request.



# Design Guidelines


# Forms

## User Facing

### Login

### Register

### User Profile

### Create Project

### Edit Project

## Admin

### Manage User

### Manage Post

### Manage Links

# Database
