<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>Misery Index - The Denver Post</title>

    <link rel="canonical" href="" />
    <link rel="shortcut icon" href="http://extras.mnginteractive.com/live/media/favIcon/dpo/favicon.ico" type="image/x-icon" />

    <meta name="distribution" content="global" />
    <meta name="robots" content="index" />
    <meta name="title" content="Misery Index" />
    <meta name="language" content="en, sv" />
    <meta name="Copyright" content="Copyright &copy; The Denver Post." />

    <meta name="description" content="">
    <meta name="news_keywords" content="">

    <meta name="twitter:card" value="summary" />
    <meta name="twitter:url" value="" />
    <meta name="twitter:title" value="Misery Index" />
    <meta name="twitter:description" value="" />
    <meta name="twitter:image" value="" />
    <meta name="twitter:site" value="@denverpost" />
    <meta name="twitter:domain" value="denverpost.com" />
    <meta name="twitter:creator" value="@joemurph">
    <meta name="twitter:app:name:iphone" value="Denver Post">
    <meta name="twitter:app:name:ipad" value="Denver Post">
    <meta name="twitter:app:name:googleplay" value="The Denver Post">
    <meta name="twitter:app:id:iphone" value="id375264133">
    <meta name="twitter:app:id:ipad" value="id409389010">
    <meta name="twitter:app:id:googleplay" value="com.ap.denverpost" />

    <meta property="fb:app_id" content="105517551922"/>
    <meta property="og:title" content="Misery Index" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="" />
    <meta property="og:site_name" content="The Denver Post" />
    <meta property="og:description" content="" />
    <meta property="article:publisher" content="https://www.facebook.com/denverpost" />

    <meta name="google-site-verification" content="2bKNvyyGh6DUlOvH1PYsmKN4KRlb-0ZI7TvFtuKLeAc" />
    <style type="text/css">
        iframe#input
        {
            width: 50%;
            height: 100%;
            min-height: 860px;
            float: left;
            margin-right: 10px;
        }
        footer 
        {
            clear: both; 
            margin: auto;
            text-align: center;
        }
        @media only screen and (max-width: 768px) {
            iframe#input
            {
                width: 100%;
                float: none;
                margin-right: 0;
            }
        }
    </style>
    <link rel="stylesheet" type="text/css" href="http://extras.mnginteractive.com/live/css/site67/bartertown.css" />
    <script src="http://local.denverpost.com/common/jquery/jquery-min.js"></script>
</head>
<body>
    <h1>Rockies Misery Index</h1>

    <iframe src="<?php echo $_ENV['FORM_URL']; ?>" seamless id="input"></iframe>
    <h2>Recent Bad Things</h2>
    <script>
        $.getJSON( "output/responses.json", function( data ) {
            var items = [];
            $.each( data, function( key, val ) 
            {
                console.log(key, val);
                items.push( "<li id='" + key + "'>" + val['Bad Thing'] + ": " + val['Timestamp'] + "</li>" );
            });

            items.reverse();
            $( "<ul/>", {
                "class": "my-new-list",
                html: items.join( "" )
            }).appendTo( "#recently" );
        });
    </script>
    <div id="recently">
    </div>

    <div id="dinger">
        <div class="teardrop" id="item01"></div>
    </div>

<style type="text/css">
#dinger
{
    width: 100%;
    height: 100%;
    border: 1px solid red;
}
.teardrop
{
    display: block;
    -webkit-box-sizing: content-box;
    -moz-box-sizing: content-box;
    box-sizing: content-box;
    float: none;
    z-index: auto;
    width: 3em;
    height: 3em;
    position: relative;
    cursor: default;
    opacity: 1;
    margin: 0;
    padding: 0;
    overflow: visible;
    border: none;
    -webkit-border-radius: 80% 0 55% 50% / 55% 0 80% 50%;
    border-radius: 80% 0 55% 50% / 55% 0 80% 50%;
    font: normal 100%/normal Arial, Helvetica, sans-serif;
    color: rgba(0,0,0,1);
    -o-text-overflow: clip;
    text-overflow: clip;
    background: #b3d1ff;
    -webkit-box-shadow: none;
    box-shadow: none;
    text-shadow: none;
    -webkit-transition: none;
    -moz-transition: none;
    -o-transition: none;
    transition: none;
    -webkit-transform: rotateZ(-45deg)   ;
    transform: rotateZ(-45deg)   ;
    -webkit-transform-origin: 50% 50% 0;
    transform-origin: 50% 50% 0;
}
</style>
    <footer>
    <p>Copyright &copy; 2015 <a href="http://www.denverpost.com/">The Denver Post</a></p>
    <p>
        <a href="http://www.denverpost.com/weather#denver">Denver Weather</a> 
        &bull; <a href="http://dptv.denverpost.com/">Denver TV News</a>
        &bull; <a href="http://www.denverpost.com/broncos">Denver Broncos</a>
    </p>
    </footer>
</body
</html>
