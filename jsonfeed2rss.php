<?php

function convertrss($content)
{
    $max = NULL;
    //Test if the content is actual JSON
    json_decode($content);
    if( json_last_error() !== JSON_ERROR_NONE) return FALSE;

    //Now, is it valid JSONFeed?
    $jsonFeed = json_decode($content, TRUE);
    //if (!isset($jsonFeed['kind'])) return FALSE;
    if (!isset($jsonFeed['context'])) return FALSE;
    if (!isset($jsonFeed['items'])) return FALSE;

    //Decode the feed to a PHP array
    $jf = json_decode($content, TRUE);

    //Get the latest item publish date to use as the channel pubDate
    $latestDate = 0;
    
    //foreach ($jf['items'] as $item) {
    //	if (strtotime($item['date_published']) > $latestDate) $latestDate = strtotime($item['date_published']);
    //}
    $latestDate = date("Y-m-d H:i:s"); 
    $lastBuildDate =   date("D, d M Y H:i:s O");

    //Create the RSS feed
    
    $xmlFeed = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"></rss>');
    
    $xmlFeed->addChild("channel");

    //Required elements
    $xmlFeed->channel->addChild("title", 'customsearch#search');
    $xmlFeed->channel->addChild("pubDate", $lastBuildDate);
    $xmlFeed->channel->addChild("lastBuildDate", $lastBuildDate);

    //Optional elements
    //if (isset($item['description'])) $xmlFeed->channel->description = $item['description'];
    //if (isset($item['home_page_url'])) $xmlFeed->channel->link = $item['home_page_url'];

    //Items
    foreach ($jf['items'] as $item) {
        $newItem = $xmlFeed->channel->addChild('item');

        //Standard stuff
        if (isset($item['id'])) $newItem->addChild('guid', $item['id']);
        if (isset($item['title'])) $newItem->addChild('title', $item['title']);
        if (isset($item['snippet'])) $newItem->addChild('description', $item['snippet']);
        //if (isset($item['htmlSnippet'])) $newItem->addChild('description', $item['htmlSnippet']);
        //if (isset($item['date_published'])) if (isset($item['date_published'])) $newItem->addChild('pubDate', $item['date_published']);
        $newItem->addChild('pubDate',$lastBuildDate);
        if (isset($item['link'])) $newItem->addChild('link', $item['link']);

        //Enclosures?
        if(isset($item['attachments'])) {
            foreach($item['attachments'] as $attachment) {
                $enclosure = $newItem->addChild('enclosure');
                $enclosure['url'] = $attachment['url'];
                $enclosure['type'] = $attachment['mime_type'];
                $enclosure['length'] = $attachment['size_in_bytes'];
            }
        }
    }

    //Make the output pretty
    $dom = new DOMDocument("1.0");
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xmlFeed->asXML());
    return $dom->saveXML();
}

//$content = @file_get_contents("http://timetable.manton.org/feed.json");
//echo convert_jsonfeed_to_rss($content)."\n";