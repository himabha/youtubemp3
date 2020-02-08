<?php
/**
 * CodexWorld
 *
 * This Downloader class helps to download youtube video.
 *
 * @class       YouTubeDownloader
 * @author      CodexWorld
 * @link        http://www.codexworld.com
 * @license     http://www.codexworld.com/license
 */
class YouTubeDownloader
{
    /*
     * Video Id for the given url
     */
    private $video_id;

    /*
     * Video title for the given video
     */
    private $video_title;

    /*
     * Full URL of the video
     */
    private $video_url;

    /*
     * store the url pattern and corresponding downloader object
     * @var array
     */
    private $link_pattern;

    const CURL_UA = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko Firefox/11.0";
    const YT_BASE_URL = "http://www.youtube.com/";
    public function __construct()
    {
        $this->link_pattern = "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed)\/))([^\?&\"'>]+)/";
    }

    /*
     * Set the url
     * @param string
     */
    public function setUrl($url)
    {
        $this->video_url = $url;
    }

    /*
     * Get the video information
     * return string
     */
    private function getVideoInfo()
    {
        return file_get_contents("https://www.youtube.com/get_video_info?video_id=".$this->extractVideoId($this->video_url)."&cpn=CouQulsSRICzWn5E&eurl&el=adunit");
    }

    /*
     * Get video Id
     * @param string
     * return string
     */
    private function extractVideoId($video_url)
    {
        //parse the url
        $parsed_url = parse_url($video_url);
        if ($parsed_url["path"] == "youtube.com/watch") {
            $this->video_url = "https://www.".$video_url;
        } elseif ($parsed_url["path"] == "www.youtube.com/watch") {
            $this->video_url = "https://".$video_url;
        }

        if (isset($parsed_url["query"])) {
            $query_string = $parsed_url["query"];
            //parse the string separated by '&' to array
            parse_str($query_string, $query_arr);
            if (isset($query_arr["v"])) {
                return $query_arr["v"];
            }
        }
    }

    /*
     * Get the downloader object if pattern matches else return false
     * @param string
     * return object or bool
     *
     */
    public function getDownloader($url)
    {
        /*
         * check the pattern match with the given video url
         */
        if (preg_match($this->link_pattern, $url)) {
            return $this;
        }
        return false;
    }

    /*
     * Get the video download link
     * return array
     */
    public function getVideoDownloadLink()
    {
        //parse the string separated by '&' to array
        parse_str($this->getVideoInfo(), $data);
        $videoData = json_decode($data['player_response'], true);
        $videoDetails = $videoData['videoDetails'];
        $streamingData = $videoData['streamingData'];
        $streamingDataFormats = $streamingData['formats'];

        //set video title
        $this->video_title = $videoDetails["title"];

        //Get the youtube root link that contains video information
        $final_stream_map_arr = array();

        //Create array containing the detail of video
        foreach ($streamingDataFormats as $stream) {
            $stream_data = $stream;
            $stream_data["title"] = $this->video_title;
            $stream_data["mime"] = $stream_data["mimeType"];
            $mime_type = explode(";", $stream_data["mime"]);
            $stream_data["mime"] = $mime_type[0];
            $start = stripos($mime_type[0], "/");
            $format = ltrim(substr($mime_type[0], $start), "/");
            $stream_data["format"] = $format;
            unset($stream_data["mimeType"]);
            $final_stream_map_arr [] = $stream_data;
        }
        return $final_stream_map_arr;
    }

    /*
     * Validate the given video url
     * return bool
     */
    public function hasVideo()
    {
        $valid = true;
        parse_str($this->getVideoInfo(), $data);
        if ($data["status"] == "fail") {
            $valid = false;
        }
        return $valid;
    }
    //To download file in specific folder
    public function curl_get_file($remote_file, $local_file)
    {
        $ch = curl_init($remote_file);
        curl_setopt($ch, CURLOPT_USERAGENT, self::CURL_UA);
        curl_setopt($ch, CURLOPT_REFERER, YT_BASE_URL);
        $fp = fopen($local_file, 'w');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $result = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $result;
    }

    public function download_stream($videoTitle, $ffmpeg_infile, $ffmpeg_outfile, $ext)
    {
        $path = getcwd()."/tmp/";
        $ffmpeg_outfile = $path . $ffmpeg_outfile;
        $cmd = "ffmpeg -i \"$ffmpeg_infile\" -ar 44100 -ab 320k -ac 2 \"$ffmpeg_outfile\" 2>&1";
        $output = shell_exec($cmd);
        if ($output) {
            $fileName = preg_replace('/[^A-Za-z0-9.\_\-]/', '', basename(strtolower(str_replace(' ', '_', $videoTitle)) .".". $ext));
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Content-Type: application/octet-stream");
            header("Content-Transfer-Encoding: binary");
            // Read the file
            if (readfile($ffmpeg_outfile)) {
                unlink($ffmpeg_outfile);
            }
        }
    }
}
