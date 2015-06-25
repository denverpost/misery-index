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
    <script>
        var iframe = '';
        if ( document.location.hash === '#iframe' ) iframe = 1
    </script>
    <script src="js/d3.v3.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/misery.css" />
    <h2>Rockies Misery Index</h2>
    <p>How miserable are the Colorado Rockies today? We rate how miserable recent events have been and turn that into today's Misery Index, measured in Dinger's tears.</p>
    <div id="dinger">
        <img src="http://extras.mnginteractive.com/live/media/site36/2015/0624/20150624_043103_sad_dinger.gif" alt="Sad Dinger" width="300" height="284" />
        <p>Dinger's crying <span id="tears">0</span> tear<span id="s">s</span> today.</p>
    </div>

    <?php if ( array_key_exists('FORM_URL', $_ENV) ): ?>
    <iframe src="<?php echo $_ENV['FORM_URL']; ?>" seamless id="input"></iframe>
    <?php endif; ?>

<h3>Misery, by day</h3>
<svg class="chart" id="chart"></svg>
<script>
var data = [];

$.getJSON( "output/scores.json", function( data ) {
    var items = [];
    $.each( data, function( key, val ) 
    {
        var obj = {"count": val, "date": key};
        window.data.push(obj);
    });


    // VISUALIZE THE MISERY
    // Dinger's tears.
    var tear = {
        src: 'http://extras.mnginteractive.com/live/media/site36/2015/0624/20150624_044312_dinger-tear70.gif',
        rand: function(floor, ceiling)
        {
            // Return a random number between floor and ceiling.
            return Math.floor(Math.random() * (ceiling - floor)) + floor;
        },
        add: function(i)
        {
            // Add a tear to Dinger's face.
            var x = this.rand(50,220);
            var y = this.rand(230,300);

            // Figure out how much to rotate the tear.
            // 115px's the center point. 
            var negative = '';
            var dist = 115 - x;
            if  ( dist < 0 ) 
            {
                negative = '-';
                dist = dist * -1;
            }
            var rotate = Math.floor(Math.sqrt(dist));
            $('#dinger').append('<img src="' + this.src + '" id="tear' + i + '" style="transform:rotate(' + negative + rotate + 'deg); position:absolute;top:' + y + 'px;left:' + x + 'px;">');
        },
        init: function()
        {
            // The number of tears is determined by the misery count of the last day.
            var keys = Object.keys(data);
            var key = keys.pop();
            var count = data[key];
            for ( i = 0; i < count; i ++ )
            {
                this.add(i);
            }
            $('#tears').text(count);
            if ( count == 1 ) $('#s').text('');
        }
    };
    tear.init();

// CHART THE MISERY
var dates = {
    start: new Date(2015, 5, 1),
    current: new Date(),
    delta: 0,
    get_delta: function()
    {
        this.current.setHours(0);
        this.current.setMinutes(0);
        this.current.setSeconds(0);
        this.current.setMilliseconds(0);

        var delta = this.current - this.start;

        this.delta = Math.round(delta / 1000 / 60 / 60/ 24) + 1;
        return Math.round(delta / 1000 / 60 / 60/ 24) + 1;
    },
    init: function()
    {
        this.get_delta();
    }
};
dates.init();
var $chart = $('#chart');
var mobile_threshold = 500;
var aspect = { width: 12, height: 6 };
var chart_width = dates.delta * 6;

var margin = { top: 20, right: 20, bottom: 30, left: 30 },
    width = $chart.width() - margin.left - margin.right,
    height = 200 - margin.top - margin.bottom;

var x = d3.scale.ordinal()
    //.rangeRoundBands([0, width], .1);
    .rangeRoundBands([1, chart_width], .1);
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
    var format_axis = d3.time.format("%b %-d");
    var format_ordinal = d3.time.format("%-j");
    d.date = format.parse(d.date);
    var date_orig = d.date;
    var ordinal = format_ordinal(d.date);
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

        });


// RECENT MISERY
        $.getJSON( "output/responses.json", function( data ) {
            var items = [];
            $.each( data, function( key, val ) 
            {
                var timestamp = val['Timestamp'];
                if ( val['Date'] !== '' ) timestamp = val['Date'];

                var text = val['Bad Thing'] + ": " + timestamp;
                if ( val['URL'] !== '' ) text = "<a href='" + val['URL'] + "' target='_parent'>" + text + "</a>";

                items.push( "<li id='" + key + "'>" + text + "</li>" );
            });

            items.reverse();
            $( "<ul/>", {
                html: items.join( "" )
            }).appendTo( "#recently" );
        });
</script>

    <h3>Recent Misery</h3>
    <div id="recently">
    </div>


    <footer>
    <p>Copyright &copy; 2015 <a href="http://www.denverpost.com/">The Denver Post</a></p>
    <p>
        <a href="http://www.denverpost.com/weather#denver">Denver Weather</a> 
        &bull; <a href="http://dptv.denverpost.com/">Denver TV News</a>
        &bull; <a href="http://www.denverpost.com/broncos">Denver Broncos</a>
    </p>
    </footer>
    <script>
        if ( iframe === 1 )
        {
            $('h1, h2, footer').remove()
        }
    </script>
</body
</html>
