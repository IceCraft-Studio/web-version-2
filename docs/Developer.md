# Deployment

This project is designed to work with Apache 2.4 and PHP 8.1 running on a modern Linux system (kernel 6.0+).
The `/src` directory contains what is intended to be in the root folder for the Apache instance to start the web server.
The information required to access the database must be passed using environment variables. Currently a file hidden from version control `/src/api/libs/secure/database-env.php` is used to inject the env vars.

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
    - Internally reroutes specific requests with dynamic URIs using primitive "middleware".
    - Calls controllers on routes defined separately for each HTTP method. The lack of a controller for a method causes 405. This means all pages need a GET controller to be accesible, even an empty one is fine.
        - All controller file names are expected to follow this pattern - `controller.{method}.php`.
        - Controller for a specific route can set an HTTP status code for error (4xx or 5xx) which reroutes the request to error "middleware".
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

This is a comprehensive list of all forms
In addition to the fields listed for each form they also contain a field with a `csrf-token` to validate the user knowingly initiated the request.

## User Facing

### Login

Fields:
- `username` The username to attempt the login with.
- `password` The password to attempt the login with.

### Register

Fields:
- `username` The username to register a new user with.
- `password` The password to register a new user with.
- `confirm-password` Confirmation of the password.

### User Profile

Fields:
- `profile-picture`
- `delete-profile-picture`

- `display-name`
- `email`
- `social-website`

- `social-reddit`
- `social-twitter`
- `social-instagram`
- `social-discord`

- `password`
- `new-password`
- `confirm-password`

### Create/Edit Project

Fields:
- `title`
- `description`
- `category`
- `slug`
- `article`
-


## Admin Only

### Manage User

### Manage Post

### Manage Links

# Database

The general structure of the database is described in the file `database.sql`.
This file can be called to recreate empty database tables used for the purposes of this website.

# File system

The web stores several different binary files in the filesystem. All data need to be stored in the same root directory e.g. `~/data`.
Specifically we store data as described below:

### User Files
- For this purpose exists a specific directory in the root named `user` consisting of subdirectories based on the specific username used as a unique ID to store the users files. 
- For example the user `admin` would have such path: `user/admin`.
- User Profile Picture
    - Image file in `.webp` format uploaded by the user as a 1:1 icon for their profile.
    - This is a file in user's directory called `profile-picture.webp`.

### Project Files
- For this purpose exists a specific directory in the root named `project` consisting of subdirectories based on the category of the project used as a unique ID to store the users files.
- For example the project `test` in category `apps` would have such path: `project/apps/test`.
- Project Upload
    - Any file (usually `.zip` or a specific project file) which the user interested in the project can open or use directly to get it running.
    The Specific type may vary based on the category of the project.
    - These are n files stored in a subdirectory inside the project's directory called `upload`.
- Project Thumbnail
    - Image file in `.webp` format uploaded by the user as a 16:9 preview for their project.
    - This is a file in project's directory called `thumbnail.webp`.
- Project Article Markdown
    - User created article made in plain text markdown format used to generate the final HTML of the article.
    - This is a file in the project's directory called `article.md`.
- Project Article HTML
    - An HTML file generated by the server whenever the project article markdown contents get changed.
    - This is a file in the project's directory called `article.html`
- Project Gallery Image
    - Additional image files in `.png`, `.jpeg`, `.webp` or `.gif` format uploaded by the user to accompany their project's article.
    - These are n files stored in a subdirectory inside the project's directory called `gallery`.
