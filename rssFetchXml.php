<?php declare(strict_types=1);

/**
 * RSS aggregator utilizing libxml and cURL
 *
 * @package RssFetchXml
 * @version 1.3
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
    
    /**
     * @var string
     */
    private string $xml_rss_error;
    
    /**
     * @var int
     */
    private int $xml_rss_max;

    /**********************************************
     * Public methods
     *********************************************/

    /**
     * Constructor
     */
    public function __construct() {  
        require_once "rss_config.php";
        $this->xml_cache_dir = $rss_cache;
        $this->xml_cache_expire = $rss_expire;
        $this->xml_rss_error = $rss_error;
        $this->xml_rss_max = $max_links;
    }
    
    /**
     * Return RSS feed HTML
     *
     * @param string $rss_url
     * @return string
     */ 
    public function rssXmlReturn(string $rss_url) {
        $rss_xml = $this->rssXmlFetch($rss_url);
        $rss_output = "";
        $rss_count = 0;
  
        if ($rss_xml !== false) {
            if (empty($rss_xml->channel->title)) {
                $rss_xml->channel->title = $rss_xml->channel->link;
            }
            
            $rss_output .= "<div class=\"rss-header\"><a href=\"" . $rss_xml->channel->link . "\" target=\"_blank\">" . $rss_xml->channel->title . "</a></div>";
            $rss_output .= "<div class=\"rss-wrapper\">";

            foreach ($rss_xml->channel->item as $item) {
                if ($rss_count === $this->xml_rss_max) {
                    break;
                }

                $rss_count++;
                $rss_output .= "<div class=\"rss-link\"><a href=\"" . $item->link . "\" target=\"_blank\">" . $item->title . "</a></div>";
            }

            $rss_output .= "</div>";

        } else {
            $rss_output .= $this->xml_rss_error;
        }

        return $rss_output;
    }

    /**********************************************
     * Private methods
     *********************************************/

    /**
     * Set RSS URL and cache file name
     * Load unexpired XML file or fetch
     * RSS feed and return XML object
     *
     * @param string $rss_url
     * @return mixed
     */
    private function rssXmlFetch(string $rss_url) {
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
        $user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36";
        $curl = curl_init($rss_url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
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
        $user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36";
        $stream_context = stream_context_create( ["http" => ["user_agent" => $user_agent]]);
        $stream_response = file_get_contents($rss_url, false, $stream_context);
        $stream_str_obj = simplexml_load_string((string)$stream_response, "SimpleXMLElement", LIBXML_NOWARNING | LIBXML_NOERROR);  
      
        if (is_string($stream_response) && $stream_str_obj !== false && $stream_str_obj->asXML($cache_file) === true) {
            return $stream_str_obj;
        }

        return false;
    }
}

$rss = new RssFetchXml;
echo json_encode($rss->rssXmlReturn($_GET["file"]));

/* EOF rssFetchXml.php */
/* Location: ./rssFetchXml.php */
