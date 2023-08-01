<?php
// Check if the "url" parameter exists in the URL
if (isset($_GET['url'])) {
    // Retrieve the value of the "url" parameter
    $url = $_GET['url'];
}

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

if (!isset($video_info['contentUrl'])) {
    echo "Unable to extract contentUrl from JSON-LD data.\n";
    exit;
}

$contentUrl = $video_info['contentUrl'];

// Retrieve the MP4 content
$mp4_content = @file_get_contents($contentUrl);

// Check if the content was fetched successfully
if (!$mp4_content) {
    echo "Unable to fetch MP4 content from the provided URL.\n";
    exit;
}

// Output the MP4 content
header("Content-Type: video/mp4");
echo $mp4_content;
?>
