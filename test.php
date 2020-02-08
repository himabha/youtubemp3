<html>
<head>
    <link href="styles.css"/>
</head>
<body>
    <div class="row">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="text" name="videoUrl" id="videoUrl" required>

        <select name="format" id="format">
            <option value="mp3">Mp3</option>
            <option value="mp4">Mp4</option>
        </select>
        <input type="submit" name="submit" id="submit" value="Convert">
    </form>
    </div>
<?php
 // Load and initialize downloader class
include_once 'YouTubeDownloader.class.php';
$handler = new YouTubeDownloader();
ini_set("display_errors", 1);
error_reporting(E_ALL);
if (isset($_POST['submit'])) {
    // Youtube video url
    $youtubeURL = $_POST['videoUrl'];

    // Check whether the url is valid
    if (!empty($youtubeURL) && !filter_var($youtubeURL, FILTER_VALIDATE_URL) === false) {
        // Get the downloader object
        $downloader = $handler->getDownloader($youtubeURL);

        // Set the url
        $downloader->setUrl($youtubeURL);

        /*$data = file_get_contents('https://www.youtube.com/get_video_info?video_id=XpJWb_so29c');
        parse_str($data, $output);
        echo "<pre>";
        print_r($output);*/


        // Validate the youtube video url
        if ($downloader->hasVideo()) {
            // Get the video download link info
            $videoDownloadLink = $downloader->getVideoDownloadLink();

            $videoTitle = $videoDownloadLink[0]['title'];
            $videoQuality = $videoDownloadLink[0]['qualityLabel'];
            $videoFormat = $videoDownloadLink[0]['format'];
            if (isset($videoDownloadLink[0]['url'])) {
                $downloadURL = $videoDownloadLink[0]['url'];
            } else {
                $downloadURL = urldecode(str_replace("url=", "", $videoDownloadLink[0]['cipher']));
            }
            if (isset($_POST['format'])) {
                //$videoFileName = strtolower(str_replace(' ', '_', $videoTitle)).'.'.$_POST['format'];
                //$fileName = preg_replace('/[^A-Za-z0-9.\_\-]/', '', basename($videoFileName));
                //$downloader->download_video($downloadURL, $fileName, $_POST['format']);
                $ffmpeg_infile = $downloadURL;
                $ffmpeg_outfile = strtolower(str_replace(' ', '_', $videoTitle)) .".". $_POST['format'];
                $downloader->download_stream($videoTitle, $downloadURL, $ffmpeg_outfile, $_POST['format']);
            }
        } else {
            echo "The video is not found, please check YouTube URL.";
        }
    } else {
        echo "Please provide valid YouTube URL.";
    }
}

?>
</body>
</html>
