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
    <script src="js/d3.v3.min.js"></script>
</head>
<body>
    <h1>Rockies Misery Index</h1>

    <?php if ( trim($_ENV['FORM_URL']) !== '' ): ?>
    <iframe src="<?php echo $_ENV['FORM_URL']; ?>" seamless id="input"></iframe>
    <?php endif; ?>
    <script>
        $.getJSON( "output/responses.json", function( data ) {
            var items = [];
            $.each( data, function( key, val ) 
            {
                console.log(key, val);
                var timestamp = val['Timestamp'];
                if ( val['Date'] !== '' ) timestamp = val['Date'];

                var text = val['Bad Thing'] + ": " + timestamp;
                if ( val['URL'] !== '' ) text = "<a href='" + val['URL'] + "'>" + text + "</a>";

                items.push( "<li id='" + key + "'>" + text + "</li>" );
            });

            items.reverse();
            $( "<ul/>", {
                html: items.join( "" )
            }).appendTo( "#recently" );
        });
    </script>
<h2>Misery, by day</h2>
<svg class="chart" id="chart"></svg>
<style>
.chart 
{ 
    color: black; 
    min-width: 220px;
    width: 100%;
    max-width: 960px;
}
.chart rect {
  fill: #42298E;
}

.chart text {
  fill: #666;
  font: 10px sans-serif;
  text-anchor: middle;
}
.axis text {
  font: 10px sans-serif;
    color: #666;
}

.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.x.axis path {
  display: none;
}
</style>
<script>
var data = [];
        $.getJSON( "output/scores.json", function( data ) {
            var items = [];
            $.each( data, function( key, val ) 
            {
                var obj = {"count": val, "date": key};
                window.data.push(obj);
                //items.push( "<li id='" + key + "'>" + key + ": " + val + "</li>" );
            });

var $chart = $('#chart');
var mobile_threshold = 500;
var aspect = { width: 12, height: 6 };

var margin = { top: 20, right: 20, bottom: 30, left: 30 },
    width = $chart.width() - margin.left - margin.right,
    height = 200 - margin.top - margin.bottom;

var x = d3.scale.ordinal()
    //.rangeRoundBands([0, width], .1);
    .rangeRoundBands([5, 60], .1);
var y = d3.scale.linear()
    .range([height, 0]);

var x_axis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var y_axis = d3.svg.axis()
    .scale(y)
    .orient("left")
    .ticks(10);

var chart = d3.select(".chart")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var data = window.data;

data.forEach(function(d) 
{
    var format = d3.time.format("%m/%d/%Y");
    var format_axis = d3.time.format("%b %_d");
    d.date = format.parse(d.date);
    var date_orig = d.date;
    d.date = format_axis(d.date);
    if ( width < mobile_threshold )
    {
        var year_format = d3.time.format("%Y");
        var year = year_format(date_orig);
        year = year.replace('2015', '');
        var month_letter = d.date[0];
        d.date = month_letter + year;
    }
    else
    {
        d.date = d.date.replace('2015', '\'');
    }
    d.count = +d.count;
    previous_date = d.date;
});

x.domain(data.map(function(d) { return d.date; }));
y.domain([0, d3.max(data, function(d) { return d.count; })]);


chart.append("g")
    .attr("class", "x axis")
    .attr("transform", "translate(0," + height + ")")
    .call(x_axis);

chart.append("g")
    .attr("class", "y axis")
    .call(y_axis)
    .append("text")
    .attr("transform", "rotate(-90)")
    .attr("y", 6)
    .attr("dy", ".71em")
    .style("text-anchor", "end")
    .text("Misery");

chart.selectAll("bar")
    .data(data)
    .enter().append("rect")
    .attr("class", "bar")
    .attr("x", function(d) { return x(d.date); })
    //.attr("width", x.rangeBand())
    .attr("width", 5)
    .attr("y", function(d) { return y(d.count); })
    .attr("height", function(d) { return height - y(d.count); });

/*
chart.append("text")
    .attr("x", x.rangeBand() / 2)
    .attr("y", function(d) { return y(d.count) + 3; })
    .attr("dy", ".75em")
    .text(function(d) { return d.count; });
*/
        });
</script>

    <h2>Recent Bad Things</h2>
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
