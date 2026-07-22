/**
 * RSS Javascript Function
 *
 * @package RssFetchXml
 * @version 1.3
 * @author MT Jordan <mtjo62@gmail.com>
 * @copyright 2026
 * @license MIT
 */
 
let rssUrl = "bin/rss/rssFetchXml_new.php";

window.addEventListener("load", rssFetch(), false);

async function rssFetch() {
    const rssClass = document.getElementsByClassName("rss-feed");

    for (i = 0; i < rssClass.length; i++) {
        const response = await fetch(rssUrl + "?file=" + rssClass[i].getAttribute("data-rss"));
        const data = await response.json();
        rssClass[i].innerHTML = data;
    }
}

/* EOF rss_fetch.js */
/* Location: ./rss_fetch.js */
