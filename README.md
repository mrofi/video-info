# video-info
Get Info from path of embed video URL

### use case
Embed from youtube : //www.youtube.com/embed/szcZVMi9cMw
Embed from vimeo : //player.vimeo.com/video/166319350?title=0&amp;amp;byline=0

### youtube API example 
sourcce : http://stackoverflow.com/a/30761150/4711810
```` php
class Youtube
{
    static $api_key = '<API_KEY>';
    static $api_base = 'https://www.googleapis.com/youtube/v3/videos';
    static $thumbnail_base = 'https://i.ytimg.com/vi/';

    // $vid - video id in youtube
    // returns - video info
    public static function getVideoInfo($vid)
    {
        $params = array(
            'part' => 'contentDetails',
            'id' => $vid,
            'key' => self::$api_key,
        );

        $api_url = Youtube::$api_base . '?' . http_build_query($params);
        $result = json_decode(@file_get_contents($api_url), true);

        if(empty($result['items'][0]['contentDetails']))
            return null;
        $vinfo = $result['items'][0]['contentDetails'];

        $interval = new DateInterval($vinfo['duration']);
        $vinfo['duration_sec'] = $interval->h * 3600 + $interval->i * 60 + $interval->s;

        $vinfo['thumbnail']['default']       = self::$thumbnail_base . $vid . '/default.jpg';
        $vinfo['thumbnail']['mqDefault']     = self::$thumbnail_base . $vid . '/mqdefault.jpg';
        $vinfo['thumbnail']['hqDefault']     = self::$thumbnail_base . $vid . '/hqdefault.jpg';

        $vinfo['thumbnail']['sdDefault']     = self::$thumbnail_base . $vid . '/sddefault.jpg';
        $vinfo['thumbnail']['maxresDefault'] = self::$thumbnail_base . $vid . '/maxresdefault.jpg';

        return $vinfo;
    }
}
````

### Vimeo API example 
https://vimeo.com/api/oembed.json?url=https://vimeo.com/166319350?title=0&amp;amp;byline=0

visit : https://developer.vimeo.com/apis/oembed#json-example

and the result is in json : 

```` json
{
type: "video",
version: "1.0",
provider_name: "Vimeo",
provider_url: "https://vimeo.com/",
title: "How Does an Editor Think and Feel?",
author_name: "Tony Zhou",
author_url: "https://vimeo.com/tonyzhou",
is_plus: "1",
html: "<iframe src="https://player.vimeo.com/video/166319350" width="1920" height="1080" frameborder="0" title="How Does an Editor Think and Feel?" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>",
width: 1920,
height: 1080,
duration: 563,
description: "For the past ten years, I’ve been editing professionally. Yet one question always stumps me: “How do you know when to cut?” And I can only answer that it’s very instinctual. On some level, I’m just thinking and feeling my way through the edit. So today, I’d like to describe that process: how does an editor think and feel? A very special thanks to David Poland for the use of DP/30 clips. And a very special thanks to Aso for the use of his music. For educational purposes only. You can donate to support the channel at Patreon: http://www.patreon.com/everyframeapainting And you can follow us through Taylor’s Instagram: https://instagram.com/taylor.ramos/ Taylor’s Twitter: https://twitter.com/glassesattached Tony’s Twitter: https://twitter.com/tonyszhou Tony’s Facebook: https://www.facebook.com/everyframeapainting Music: Aso - Soul Traveling (Freddie Joachim Remix) Harry James - I’ve Heard That Song Before Nat King Cole - Aquellos Ojos Verdes Aso - Jazz Intro Nujabes - Perfect Circle (Instrumental) George Benson - On Broadway (Live) Interview Clips: DP/30 Michael Kahn (2011) https://www.youtube.com/watch?v=xjdOG-w0Zz4 Michael Caine - Acting in Film (1987) https://www.youtube.com/watch?v=bZPLVDwEr7Y DP/30 Thelma Schoonmaker (2013) https://www.youtube.com/watch?v=KIKRcV4kHzg DP/30 Thelma Schoonmaker (2011) https://www.youtube.com/watch?v=KgXcpZqQy8M BAFTA - Walter Murch on Editing (2013) https://www.youtube.com/watch?v=WcBpXLNmS3Q Recommended Reading & Viewing: On Film Editing by Edward Dmytryk http://amzn.com/dp/0240517385 Cut to the Chase by Sam O’Steen & Bobbie O’Steen http://amzn.com/dp/094118837X In the Blink of an Eye by Walter Murch http://amzn.com/dp/1879505622 The Conversations with Walter Murch by Michael Ondaatje http://amzn.com/dp/0375709827",
thumbnail_url: "https://i.vimeocdn.com/video/570375706_1280.webp",
thumbnail_width: 1280,
thumbnail_height: 720,
thumbnail_url_with_play_button: "https://i.vimeocdn.com/filter/overlay?src=https://i.vimeocdn.com/video/570375706_1280.webp&src=http://f.vimeocdn.com/p/images/crawler_play.png",
upload_date: "2016-05-12 03:02:41",
video_id: 166319350,
uri: "/videos/166319350"
}
````
Code example for Vimeo :
```` php
<?
/*
You may want to use oEmbed discovery instead of hard-coding the oEmbed endpoint.
*/
$oembed_endpoint = 'http://vimeo.com/api/oembed';
// Grab the video url from the url, or use default
$video_url = ($_GET['url']) ? $_GET['url'] : 'http://vimeo.com/7100569';
// Create the URLs
$json_url = $oembed_endpoint . '.json?url=' . rawurlencode($video_url) . '&width=640';
$xml_url = $oembed_endpoint . '.xml?url=' . rawurlencode($video_url) . '&width=640';
// Curl helper function
function curl_get($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $return = curl_exec($curl);
    curl_close($curl);
    return $return;
}
// Load in the oEmbed XML
$oembed = simplexml_load_string(curl_get($xml_url));
/*
    An alternate approach would be to load JSON,
    then use json_decode() to turn it into an array.
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Vimeo PHP oEmbed Example</title>
</head>
<body>

    <h1><?php echo $oembed->title ?></h1>
    <h2>by <a href="<?php echo $oembed->author_url ?>"><?php echo $oembed->author_name ?></a></h2>

    <?php echo html_entity_decode($oembed->html) ?>

</body>
</html>
````


