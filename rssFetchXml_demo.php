<?php include 'rssFetchXml.php'; ?>

<!doctype html>
<html lang="en">
    <head>
        <title>rssFetchXml Demo</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <link rel="stylesheet" href="" />
        <style>
        <!--
        html { font-size: 75%; font-family: verdana; }
        h1 { padding: 5px; text-align: center; }
        h3 { background: #eee; padding: 5px; }
        p { background: #eef7fb; padding: 5px; margin: 5px; }
        div { float: right; width: 30%; height: 400px;
              overflow-y: scroll; background: #aaa; padding: 10px; margin: 10px; }
        h4 { padding: 5px; text-align: center; }
        -->
        </style>
    </head>
    <body>
        <header>
        <!-- Begin header content -->
            <h1>rssFetchXML Demo</h1>
        <!-- End header content -->
        </header>
        <main>
        <!-- Begin main content -->
        <?php
            $new = new rssFetchXml("path/to/cache/",48000);
            echo rss_feed( 'https://sports.yahoo.com/rss', $new );
            echo rss_feed( 'https://www.stereogum.com/feed/', $new );
            echo rss_feed( 'https://www.panthers.com/rss/news', $new );
            echo rss_feed( 'https://www.tdpri.com/forums/-/index.rss', $new );
            echo rss_feed( 'https://www.dailymail.co.uk/ushome/index.rss', $new );
            echo rss_feed( 'https://stackoverflow.blog/feed/', $new );
         ?>
        <!-- End main content -->
        </main>
        <footer>
        <!-- Begin footer content -->
            <h4>rssFetchXml &copy 2026</h4>
        <!-- End footer content -->
        </footer>
    </body>
</html>

<?php

function rss_feed( $url, $new ) {
    $rss = $new->rssXmlFetch($url);
    $output = '';

    if (is_object($rss)) {
        $output .= '<div><h3><a href="' . $rss->channel->link . '" target="_blank">'. $rss->channel->title . '</a></h3>';

        foreach ( $rss->channel->item as $item )
            $output .= '<p><a href="'. $item->link .'" target="_blank" style="">' . $item->title . '</a></p>';

        $output .= '</div>';
    } else {
        $output .= '<div>RSS fetch failed - RSS url or feed may be invalid</div>';
    }

        return $output;
    }
