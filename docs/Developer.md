# Data Structure

# Used Algorithms

# Backend Architecture

The source code of the website is structured based upon a predefined common architecture, which needs to be followed
in order to make the website function correctly.

- By default, all requests that don't request a file are sent to a router.
The router checks if the request maps to a directory containing `head.php` or `head.html` and `body.php` or `body.html`.
- If those files are present they are used to fill the body and head of the HTML response, 
otherwise 404 page is used instead.
The HTML response is also filled with common elements of all pages, which are located at `/src/common/pages/*`.
- The router looks for a `post.php` file inside the directory mapped from a POST request and executes it **before**
sending HTML data in the body. If the file isn't present, it is assumed POST requests shouldn't be called on the URL.
- All **files** located under the directory of `/src/api/libs/*` are also rerouted to this logic, meaning they always
respond with status 404. This is done to prevent executing them arbitrarily with HTTP requests.
- This logic is all implemented in `/src/router.php` and achieved by route rewrites in `/src/.htaccess`.

# Frontend Implemenataion

Based on the backend setup mentioned above, the frontend implementation needs to be adjusted to be compatible.


Middleware is used on the backend side to intercept routes that aren't defined in the filesystem and are instead
rerouted to a "middleware" resource that handles them by serving content dynamically based on the request URL. 
All middleware MUST NOT use relative resources on the server as the files are at a static place
and the URL is variable based on the client's request.



# Design Guidelines
