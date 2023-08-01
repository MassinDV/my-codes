<?php
// Check if the "url" parameter exists in the URL
if (isset($_GET['url'])) {
    // Retrieve the value of the "url" parameter
    $url = $_GET['url'];
}

// Fetch the webpage content and suppress any warnings in case of errors
$html = @file_get_contents($url);

// Fetch the webpage content and suppress any warnings in case of errors
$html = @file_get_contents($url);

// Find the JSON-LD data
$pattern = '/<script type="application\/ld\+json">(.*?)<\/script>/s';
preg_match($pattern, $html, $matches);

if (!isset($matches[1])) {
    echo "Unable to find JSON-LD data.\n";
    exit;
}

$json_data = $matches[1];
$video_info = json_decode($json_data, true);

if (!isset($video_info['contentUrl'], $video_info['embedUrl'])) {
    echo "Unable to extract contentUrl and embedUrl from JSON-LD data.\n";
    exit;
}

$contentUrl = $video_info['contentUrl'];
$embedUrl = $video_info['embedUrl'];

// Function to generate the HLS M3U8 content
function generateHLSM3U8Content($mp4_url, $embed_url) {
    $hls_m3u8_url = str_replace('.mp4', '.mp4', $mp4_url);
    return "#EXTM3U\n##EXTINF:-1,\n#EXTVLCOPT:http-referrer=$embed_url\n#EXTVLCOPT:http-user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36\n" . $hls_m3u8_url . "\n";
}

// Generate the HLS M3U8 content
$hls_m3u8_content = generateHLSM3U8Content($contentUrl, $embedUrl);

// Output the HLS M3U8 content
header('Content-Type: application/vnd.apple.mpegurl');
header('Content-Disposition: inline; filename=stream.m3u8');
header('Access-Control-Allow-Origin: *'); // Optional - if you need to allow cross-origin access

echo $hls_m3u8_content;
?>
