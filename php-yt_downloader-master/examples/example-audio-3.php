<?php
require('../youtube-dl.class.php');
try
{
	$mytube = new yt_downloader("https://www.youtube.com/watch?v=L9pTBouRz68");

	$mytube->set_audio_format("wav");        # Change default audio output filetype.
	$mytube->set_ffmpegLogs_active(FALSE);   # Disable Ffmpeg process logging.

	$mytube->download_audio();
}
catch (Exception $e) {
	die($e->getMessage());
}
