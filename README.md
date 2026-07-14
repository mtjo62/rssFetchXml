# rssFetchXML
App:       rssFetchXML
Version:   1.2
Author:    MT Jordan <mtjo62@gmail.com>
Copyright: 2026
License:   MIT License

*********************************************************************************

rssFetchXml: RSS feed aggregator

rssFetchXml fetchs RSS feeds using a simple PHP class

*********************************************************************************

rssFetchXML Features:

    * Extremely compact code that simplifies hooking into projects utilizing
      PHP's simplexml and cURL
    * Does not require allow_url_fopen enabled but defaults to file_get_contents
      and stream_context_create if it is enabled
    * Using simplexml with cURL or file_get_contents/stream_context_create sends 
      "friendly" RSS requests that networks such as Cloudflare may treat as bots 
      with other methods
    * Display unlimited RSS feeds with a single class instance
    * Auto creates cached XML files 
    * Cache dir and cache expiration time defaults to 3600 sec and the current 
      working directory in script but can be overridden on the client side
    * See rssFetchXml_demo.php for examples
   
rssFetchXML Requirements:

    * PHP 5.6+
    * Enabled libxml extension
    * Enabled cURL extension

*********************************************************************************
