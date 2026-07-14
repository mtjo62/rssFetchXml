<?php declare(strict_types=1);

/**
 * RSS aggregator utilizing libxml and cURL
 *
 * @package RssFetchXml
 * @version 1.2
 * @author MT Jordan <mtjo62@gmail.com>
 * @copyright 2026
 * @license MIT
 */

class RssFetchXml
{
    /**********************************************
     * Private Class Properties
     *********************************************/

    /**
     * @var string
     */
    private string $xml_cache_dir;
    
    /**
     * @var int
     */
    private int $xml_cache_expire;

    /**********************************************
     * Public methods
     *********************************************/

    /**
     * Constructor
     *
     * @param string $cache_dir
     * @param int $expire_time
     */
    public function __construct(string $cache_dir="./", int $expire_time=3600) {
        $this->xml_cache_dir = $cache_dir;
        $this->xml_cache_expire = $expire_time;
    }

    /**
     * Set RSS URL and cache file name
     * Load unexpired XML file or fetch
     * RSS feed and return XML object
     *
     * @param string $rss_url
     * @return mixed
     */
    public function rssXmlFetch(string $rss_url) {
        $xml_cache_file = $this->xml_cache_dir . str_replace(" ", "", preg_replace("/[^A-Za-z0-9 ]/", "", $rss_url) . ".xml");
        $xml_cache_obj = $this->rssXmlCache($xml_cache_file);

        if ($xml_cache_obj !== false) {
            return $xml_cache_obj;
        }

        $xml_str_obj = $this->rssXmlStr($rss_url, $xml_cache_file);

        if ($xml_str_obj !== false) {
            return $xml_str_obj;
        }

        return false;
     }

    /**********************************************
     * Private methods
     *********************************************/

    /**
     * Return unexpired XML cache object
     *
     * @param string $cache_file
     * @return mixed
     */
    private function rssXmlCache(string $cache_file) {
        if (file_exists($cache_file) && time() - filemtime($cache_file) < $this->xml_cache_expire) {
            return simplexml_load_file($cache_file, 'SimpleXMLElement', LIBXML_NOWARNING | LIBXML_NOERROR);
        }

        return false;
    }

    /**
     * Return XML object
     *
     * @param string $rss_url
     * @param string $cache_file
     * @return mixed
     */
    private function rssXmlStr(string $rss_url, string $cache_file) {
        if (ini_get("allow_url_fopen")) {
            return $this->rssXmlStream($rss_url, $cache_file);
        } else {
            return $this->rssXmlCurl($rss_url, $cache_file);
        }
    }
    
    /**
     * Fetch RSS XML via cURL
     *
     * @param string $rss_url
     * @param string $cache_file
     * @return mixed
     */
    private function rssXmlCurl(string $rss_url, string $cache_file) {
        $curl = curl_init($rss_url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.3");
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_ENCODING, "utf-8");
        $curl_result = curl_exec($curl);
        $curl_str_obj = simplexml_load_string((string)$curl_result, 'SimpleXMLElement', LIBXML_NOWARNING | LIBXML_NOERROR);

        //Sanity check for simplexml object, $curl_result and HTTP_CODE
        //Prevents fatal errors triggered by asXML - not 100% accurate
        if (!is_string($curl_result) || $curl_str_obj === false || curl_getinfo($curl, CURLINFO_HTTP_CODE) !== 200) {
            return false;
        }
        
        if ($curl_str_obj->asXML($cache_file) === true) {
            return $curl_str_obj;
        }

        return false;
    }
    
    /**
     * Fetch RSS XML via stream_context_create & file_get_contents
     *
     * @param string $rss_url
     * @param string $cache_file
     * @return mixed
     */
    private function rssXmlStream(string $rss_url, string $cache_file) {
        $stream_context = stream_context_create( ["http" => ["user_agent" => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36"]]);
        $stream_response = file_get_contents($rss_url, false, $stream_context);
        $stream_str_obj = simplexml_load_string((string)$stream_response, "SimpleXMLElement", LIBXML_NOWARNING | LIBXML_NOERROR);  
      
        if (is_string($stream_response) && $stream_str_obj !== false && $stream_str_obj->asXML($cache_file) === true) {
            return $stream_str_obj;
        }

        return false;
    }
}
/* EOF rssFetchXml.php */
/* Location: ./rssFetchXml.php */
