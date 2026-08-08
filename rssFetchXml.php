<?php declare(strict_types=1);

$rss_config = require "rss_config.php";

/**
 * RSS aggregator utilizing libxml and cURL
 *
 * @package RssFetchXml
 * @version 1.3.1
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
        global $rss_config;
        $this->rssXmlConfig($rss_config);
    }

    /**
     * Return RSS/Atom feed HTML
     *
     * @param string $rss_url
     * @return string
     */
    public function rssXmlReturn(string $rss_url) {
        $rss_xml = $this->rssXmlFetch($rss_url);

        if ($rss_xml instanceof SimpleXMLElement) {

            if ($rss_xml->getName() !== "feed") {
                $rss_output = $this->rssXmlFormat($rss_xml);
            } else {
                $rss_output = $this->atomXmlFormat($rss_xml);
            }

        } else {
            $rss_output = $this->xml_rss_error;
        }

        return $rss_output;
    }

    /**********************************************
     * Private methods
     *********************************************/

    /**
     * Set RSS config values to class properties
     *
     * @param array<string, string|int> $rss_config
     * @return void
     */
    private function rssXmlConfig(array $rss_config) {
        $this->xml_cache_dir = (string)$rss_config["cache_dir"];
        $this->xml_cache_expire = (int)$rss_config["cache_expire"];
        $this->xml_rss_error = (string)$rss_config["rss_error"];
        $this->xml_rss_max = (int)$rss_config["rss_max"];
    }

    /**
     * Return formatted RSS feed
     *
     * @param SimpleXMLElement $rss_xml
     * @return string
     */
    private function rssXmlFormat(SimpleXMLElement $rss_xml) {
        $rss_count = 0;

        if (empty($rss_xml->channel->title)) {
           $rss_xml->channel->title = (string)$rss_xml->channel->link;
        }

        $rss_output = "<div class=\"rss-header\">
                       <a href=\"" . htmlspecialchars((string)$rss_xml->channel->link) . "\" target=\"_blank\">" .
                       htmlspecialchars((string)$rss_xml->channel->title) . "</a>
                       </div><div class=\"rss-wrapper\">";

        foreach ($rss_xml->channel->item as $item) {
            if ($rss_count === $this->xml_rss_max) {
                break;
            }

            $rss_count++;
            $rss_output .= "<div class=\"rss-link\">
                            <a href=\"" . htmlspecialchars((string)$item->link) . "\" target=\"_blank\">" .
                            htmlspecialchars((string)$item->title) . "</a>
                            </div>";
        }

        return $rss_output . "</div>";
    }

    /**
     * Return formatted Atom feed
     *
     * @param SimpleXMLElement $rss_xml
     * @return string
     */
    private function atomXmlFormat(SimpleXMLElement $rss_xml) {
        $rss_count = 0;
        $rss_output = "<div class=\"rss-header\"><a href=\"" . htmlspecialchars((string)$rss_xml->link["href"]) .
        "\" target=\"_blank\">" . htmlspecialchars((string)$rss_xml->title) . "</a></div><div class=\"rss-wrapper\">";

        foreach ($rss_xml->entry as $entry) {
            if ($rss_count === $this->xml_rss_max) {
              break;
            }

            $rss_count++;
            $atom_link = "";

            if (isset($entry->link)) {
                $atom_link = (string)$entry->link["href"];
            }

            $rss_output .= "<div class=\"rss-link\"><a href=\"" . htmlspecialchars((string)$atom_link) . "\">" .
            htmlspecialchars((string)$entry->title) . "</a></div>";
        }

        return $rss_output . "</div>";
    }


    /**
     * Set RSS URL and cache file name
     * Load unexpired XML file or fetch
     * RSS feed and return XML object
     *
     * @param string $rss_url
     * @return SimpleXMLElement|false
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
     * @return SimpleXMLElement|false
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
     * @return SimpleXMLElement|false
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
     * @return SimpleXMLElement|false
     */
    private function rssXmlCurl(string $rss_url, string $cache_file) {
        $curl = curl_init($rss_url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36");
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
     * @return SimpleXMLElement|false
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

$rss = new RssFetchXml;
$file = $_GET["file"] ?? "";

if (!is_string($file)) {
    $file = "";
}

echo json_encode($rss->rssXmlReturn(htmlspecialchars($file, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8")));

/* EOF rssFetchXml.php */
/* Location: ./rssFetchXml.php */
