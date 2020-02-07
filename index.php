<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>How to download youtube video using PHP script | Mitrajit's Tech Blog</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <script src="jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
         
    <style type="text/css">
        .mtb-margin-top { margin-top: 20px; }
        .top-margin { border-bottom:2px solid #ccc; margin-bottom:20px; display:block; font-size:1.3rem; line-height:1.7rem;}

        .demo-page-header {
            text-align: center;
            font-size: 17px;
            text-transform: uppercase;
        }
        .thumbnail_img {
            float:left;
        }
        .video_title {
            border: 0px solid red;
        }
        .margin-top-bottom {
            margin:20px 0;
            border: 4px solid #ccc;
            border-radius: 5px;
            padding: 8px 0;
        }
        .error {
            text-align: center;
            font-size: 15px;
            color: #ff0000;
            margin-top: 10px;
        }
        @media screen and (max-width:300px) {
            p {font-size:11px;}
            h1.top-margin {font-size:15px; line-height: 1.5em;}
        }
    </style>
    <link rel="stylesheet" href="styles.css" type="text/css">
    <script type="text/javascript">
        $(document).ready(function() {
            $("#submit").click(function() {
                $(".loading").css("display","block");
            });
        });
    </script>

</head>
<body>
    <div class="loading">Loading&#8230;</div>
    
    <div class="container mtb-margin-top">
        <div class="row">
            <div class="col-md-12">
                <h1 class="top-margin">Read the full article -- <a href="http://www.mitrajit.com/how-to-download-youtube-videos-using-php-script/" target="_blank" title="How to copy text to clipboard using jQuery">How to download youtube videos using PHP script</a> on <a href="http://www.mitrajit.com/" title="Mitrajit's Tech Blog">Mitrajit's Tech Blog</a></h1>
            </div>
        </div>
    </div>

    
    <div class="container">
        <form method="post" action="">
            <div class="row">   
                <div class="col-lg-12">
                    <h1 class="demo-page-header">-------    Download youtube video from the youtube link    -------</h1>
                </div>         
                <div class="col-lg-12">                    
                    <div class="input-group">
                      <input type="text" class="form-control" name="video_link" placeholder="Paste link here.. e.g. https://www.youtube.com/watch?v=ie_atoiaEN4">
                      <span class="input-group-btn">
                        <button type="submit" name="submit" id="submit" class="btn btn-default" type="button">Go!</button>
                      </span>
                    </div><!-- /input-group -->
                </div>            
            </div><!-- .row -->
        </form>


        <?php
        include("functions.php");
        $url = "https://www.youtube.com/watch?v=ie_atoiaEN4";
        $playlist = "https://www.youtube.com/watch?v=f4R_h2gU_6Y&list=PLSgJINFF_7drDY209Uvk5wPzwub105bgq";
        if(isset($_POST['submit'])) {
            $video_link = trim($_POST['video_link']);
            if($video_link != "") {
                $video_id = "";
                $video_id_arr = getVideoId($video_link);
                if(sizeof($video_id_arr) == 3) $video_id = $video_id_arr[2];
                if($video_id != "") {
                    parse_str(file_get_contents("https://youtube.com/get_video_info?video_id=".$video_id),$info);

                    if(!empty($info) && $info['status'] == 'ok') {
                        $streams = $info['url_encoded_fmt_stream_map']; //the video's location info
                        $streams = explode(',',$streams);                        
                        $video_title = $info['title'];
                        ?>
                        <div class="row margin-top-bottom">
                            <div class="col-md-12 text-center">
                                <img class="img-responsive " src="<?php echo $info['thumbnail_url']; ?>">
                                <label class="video_title"><?php echo $video_title; ?></label>
                            </div>                                                        
                        </div>
                        
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <table class="table table-striped">
                                    <thead>
                                      <tr>
                                        <th>Quality</th>
                                        <th>Format</th>
                                        <th>Size</th>
                                        <th>Action</th>
                                      </tr>
                                    </thead>
                                    <?php
                                    foreach($streams as $stream) {
                                        parse_str($stream, $data); //decode the stream
                                        
                                        $formated_size = getRemoteFilesize($data['url']);
                                        $size_in_bytes = getRemoteFilesize($data['url'], false);

                                        $video_type = explode(";", $data['type']);
                                        $ext  = str_replace(array('/', 'x-'), '', strstr($video_type[0], '/'));
                                        ?>
                                        <tr>
                                            <td><?php echo $data['quality']; ?></td>
                                            <td><?php echo $ext; ?></td>
                                            <td><?php ($formated_size != "-1" ? print $formated_size : print "---") ?></td>
                                            <td><a href="download.php?url=<?php echo base64_encode($data['url']);?>&title=<?php echo $video_title; ?>&mime=<?php echo $video_type[0]; ?>&ext=<?php echo $ext; ?>&size=<?php echo $size_in_bytes; ?>" target="_blank">Download</a>
                                            </td>
                                        </tr>
                                    <?php                                    
                                    } 
                                    ?>
                                </table>
                            </div>
                        </div>
                    <?php
                    } else {
                        echo "<h4 class='error'>Something wrong! Please try again.</h4>";
                    }
                } else {
                    echo "<h4 class='error'>Invalid video link!</h4>";
                }
            }
        }
        ?>

</body>
</html>