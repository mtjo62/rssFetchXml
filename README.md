<!-- Improved compatibility of back to top link: See: https://github.com/othneildrew/Best-README-Template/pull/73 -->
<a id="readme-top"></a>



<h3 align="center">rssFetchXml</h3>

  <p align="center">
    rssFetchXml is an RSS reader that uses PHP, Javascript and HTML to display XML feeds
    <br />
    <a href="https://github.com/github_username/repo_name">View Demo</a>
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

<b>rssFetchXml Features:</b>

* Avoid using commercial RSS feed APIs that may require a subscription and fees

* Compact code that simplifies adding an RSS feed to a webpage utilizing
  PHP's simplexml, cURL and Javascript.

* Does not require allow_url_fopen enabled.
  
* Using simplexml with cURL sends "friendly" RSS requests that networks such
  as Cloudflare may treat as bots with other methods.

* Auto creates cached XML files. 

* Cache dir, cache expiration time, max number of feed links and custom error
  message set in config file.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



### Built With

* [Javascript](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
* [PHP](https://php.net) (v8.2)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

### Prerequisites

* PHP (v7.2+)
* cURL extension enabled
* libxml extension enabled

### Installation

1. Upload rssFetchXml.php, rss_config.php and rss_fetch.js to a directory on your server. rssFetchXml.php and rss_config.php must be in the same directory.
2. Open `rss_config.php` and edit the following variables
   ```php
   // Path to cache dir
   $rss_cache = "path/to/cache/";
   // Cache expire in secs - ie: 3600 = 1 hr
   $rss_expire = 3600;
   // Custom error message on feed failure
   $rss_error = "RSS fetch failed - RSS url or feed may be unavailable";
   // Max # of links to display
   $max_links = 12;
   ```
3. Open `rss_fetch.js` and edit the following variable
   ```js
   let rssUrl = "https://somesite.com/rss/rssFetchXml.php";
   ```
4. Add `rss_fetch.js` script link to your HTML template right before the closing `</body>` tag
   ```js
   <script src="https://somesite.com/rss/rss_fetch.js"></script>
   ```

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->
## Usage

1. Add an empty HTML div where you want to display the RSS feed. Set its class as `rss-feed` and the data attribute `data-rss` value as the RSS feed URL.
   ```html
   <div class="rss-feed" data-rss="https://somesite.com/rss/feeds/feed.xml"></div>
   ```
2. This will return a HTML formatted block with the following structure:
   ```html
   <div class="rss-feed" data-rss="https://somesite.com/rss/feeds/feed.xml">
    <div class="rss-header">Channel Title</div>
    <div class="rss-wrapper">
      <div class="rss-link"><a href="https://somesite.com/item.html" target="_blank">Item 1 Title</a></div>
      <div class="rss-link"><a href="https://somesite.com/item2.html" target="_blank">Item 2 Title</a></div>
      <div class="rss-link"><a href="https://somesite.com/item3.html" target="_blank">Item 3 Title</a></div>
    </div>
   </div> 
   ```
3. Add the following CSS properties to your CSS file to format your feed
   ```css
   .rss-feed {
    padding: .5rem;
    background-color: #fff;
    border: .063rem solid #aaa;
   }
   .rss-header {
    font-weight: bold;
    padding: .5rem;
    color: #000;
   }
   .rss-wrapper {
    height: 10rem;
    overflow-x: hidden;
    overflow-y: scroll;
    padding: .5rem;
   }
   .rss-link a {
    text-decoration: none;
   }
   ```

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- TROUBLESHOOTING -->
## Troubleshooting

1. If your are displaying the custom error message, check that the RSS feed URL is correct. Some site's CDNs may use strict security
   and are treating you request as a bot and returning forbidden codes. You will need to create a proxy feed from a site such as Google's <a href="https://feedburner.google.com/">Feedburner</a>.

2. If no RSS content or error messages are being displayed, check the cache dir path you set in `rss_config` is valid and exists. Also check the URL set in `rss_fetch.js` is correct as well the
   script URL.  

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- LICENSE -->
## License

Distributed under the MIT license. See `LICENSE.txt` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTACT -->
## Contact

Mike Jordan: mtjo62@gmail.com

Project Link: [https://github.com/mtjo62/rssFetchXml](https://github.com/mtjo62/rssFetchXml)

<p align="right">(<a href="#readme-top">back to top</a>)</p>
