<?php 
 
// Load and initialize downloader class 
include_once 'YouTubeDownloader.class.php'; 
$handler = new YouTubeDownloader(); 
 
// Youtube video url 
$youtubeURL = 'https://www.youtube.com/watch?v=f7wcKoEbUSA'; 
 
// Check whether the url is valid 
if(!empty($youtubeURL) && !filter_var($youtubeURL, FILTER_VALIDATE_URL) === false){ 
    // Get the downloader object 
    $downloader = $handler->getDownloader($youtubeURL); 
     
    // Set the url 
    $downloader->setUrl($youtubeURL); 
     
    // Validate the youtube video url 
    if($downloader->hasVideo()){ 
        // Get the video download link info 
        $videoDownloadLink = $downloader->getVideoDownloadLink(); 
         
        $videoTitle = $videoDownloadLink[0]['title']; 
        $videoQuality = $videoDownloadLink[0]['qualityLabel']; 
        $videoFormat = $videoDownloadLink[0]['format']; 
        $videoFileName = strtolower(str_replace(' ', '_', $videoTitle)).'.'.$videoFormat; 
        
        $downloadURL = $videoDownloadLink[0]['url'];
        
        $path = "/var/www/html/";
        
        if($_GET['type'] == 'mp4'){
        
        
         
        
        $fileName = preg_replace('/[^A-Za-z0-9.\_\-]/', '', basename($videoFileName)); 
         
        if(!empty($downloadURL)){ 
            // Define header for force download 
            header("Cache-Control: public"); 
            header("Content-Description: File Transfer"); 
            header("Content-Disposition: attachment; filename=$fileName"); 
            header("Content-Type: application/zip"); 
            header("Content-Transfer-Encoding: binary"); 
             
            // Read the file 
            readfile($downloadURL); 
        } 
        //curl_get_file($downloadURL, $path.$videoFileName);
        }
        if($_GET['type'] == 'mp3'){
        $ext = "mp3";
        $ffmpeg_infile = $path . $videoFileName;
        $ffmpeg_outfile = $path . strtolower(str_replace(' ', '_', $videoTitle)) .".". $ext;
        
        //$cmd = "ffmpeg -i \"$ffmpeg_infile\" -ar 44100 -ab 320k -ac 2 \"$ffmpeg_outfile\"";
        $cmd = "ffmpeg -i \"$downloadURL\" -ar 44100 -ab 320k -ac 2 \"$ffmpeg_outfile\"";
        exec($cmd, $output);
        echo getcwd();
        {
        
        $fileName = preg_replace('/[^A-Za-z0-9.\_\-]/', '', basename(strtolower(str_replace(' ', '_', $videoTitle)) .".". $ext)); 
        	header("Cache-Control: public"); 
            header("Content-Description: File Transfer"); 
            header("Content-Disposition: attachment; filename=$fileName"); 
            header("Content-Type: application/zip"); 
            header("Content-Transfer-Encoding: binary"); 
             
            // Read the file 
            readfile($ffmpeg_outfile); 
        }
        }
        /*
        
        $fileName = preg_replace('/[^A-Za-z0-9.\_\-]/', '', basename($videoFileName)); 
         
        if(!empty($downloadURL)){ 
            // Define header for force download 
            header("Cache-Control: public"); 
            header("Content-Description: File Transfer"); 
            header("Content-Disposition: attachment; filename=$fileName"); 
            header("Content-Type: application/zip"); 
            header("Content-Transfer-Encoding: binary"); 
             
            // Read the file 
            readfile($downloadURL); 
        } */
    }else{ 
        echo "The video is not found, please check YouTube URL."; 
    } 
}else{ 
    echo "Please provide valid YouTube URL."; 
} 
 
 $CURL_UA = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko Firefox/11.0";
	$YT_BASE_URL = "http://www.youtube.com/";
 function curl_get_file($remote_file, $local_file)
    {
        $ch = curl_init($remote_file);
        curl_setopt($ch, CURLOPT_USERAGENT, $CURL_UA);
        curl_setopt($ch, CURLOPT_REFERER, $YT_BASE_URL);
        $fp = fopen($local_file, 'w');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec ($ch);
        curl_close ($ch);
        fclose($fp);
    }
?>
