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
        var section = '';
        if ( document.location.hash === '#iframe' ) iframe = 1;
        if ( document.location.hash === '#section' ) 
        {
            iframe = 1;
            section = 1;
        }
    </script>
    <script src="js/d3.v3.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/misery.css" />
    <h2>Rockies Misery Index</h2>
    <p id="intro">How miserable are the Colorado Rockies today? We rate how miserable recent events have been and turn that into today's Misery Index, measured in Dinger's tears. <em>Illustration by Severiano Galv&aacute;n</em></p>
    <div id="dinger">
        <img src="http://extras.mnginteractive.com/live/media/site36/2015/0624/20150624_043103_sad_dinger.gif" alt="Sad Dinger" width="300" height="284" />
    </div>
    <p>Dinger's crying <span id="tears">0</span> tear<span id="s">s</span> today.</p>

    <?php if ( array_key_exists('FORM_URL', $_ENV) ): ?>
    <iframe src="<?php echo $_ENV['FORM_URL']; ?>" seamless id="input"></iframe>
    <?php endif; ?>

    <section id="chart">
        <h3>Misery, by day</h3>
        <svg class="chart" id="chart"></svg>
    </section>
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
        dinger_top: $('#dinger').position()['top'],
        src: 'http://extras.mnginteractive.com/live/media/site36/2015/0624/20150624_044312_dinger-tear70.gif',
        rand: function(floor, ceiling)
        {
            // Return a random number between floor and ceiling.
            return Math.floor(Math.random() * (ceiling - floor)) + floor;
        },
        add: function(i)
        {
            // Add a tear to Dinger's face.
            var x = this.rand(50,200);
            var y = this.rand(160+this.dinger_top,260+this.dinger_top);

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
            $('#dinger').prepend('<img src="' + this.src + '" id="tear' + i + '" style="transform:rotate(' + negative + rotate + 'deg); position:absolute;top:' + y + 'px;left:' + x + 'px;">');
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
//var mobile_threshold = 500;
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

    d.date = d.date.replace('2015', '\'');

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

    <section id="recent">
        <h3>Recent Misery</h3>
        <div id="recently">
        </div>
    </section>


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
            $('h1, h2, #intro, footer').remove();
        }
        if ( section === 1 )
        {
            $('section').remove();
        }
    </script>
<!-- Google Tag Manager Data Layer -->
  <script>
    var is_mobile = function() {
      var check = false;
      (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
      if ( check == true ) return 'YES';
      return 'NO';
    };
    if ( iframe !== 1 )
    {
    analyticsEvent = function() {};
    analyticsSocial = function() {};
    analyticsVPV = function() {};
    analyticsClearVPV = function() {};
    analyticsForm = function() {};
    window.dataLayer = window.dataLayer || [];
    dataLayer.push({
        'ga_ua':'UA-61435456-7',
        'quantcast':'p-4ctCQwtnNBNs2',
        'quantcast label': 'Denver',
        'comscore':'6035443',
        'errorType':'',
        'Publisher Domain':'denverpost.com',
        'Publisher Product':'extras.denverpost.com',
        'Dateline':'',
        'Publish Hour of Day':'',
        'Create Hour of Day':'',
        'Update Hour of Day':'',
        'Behind Paywall':'NO',
        'Mobile Presentation':is_mobile(),
        'kv':'sports',
        'Release Version':'',
        'Digital Publisher':'',
        'Platform':'custom',
        'Section':'Sports',
        'Taxonomy1':'Sports',
        'Taxonomy2':'Baseball',
        'Taxonomy3':'Professional-baseball',
        'Taxonomy4':'MLB-baseball',
        'Taxonomy5':'',
        'Content Source':'',
        'Canonical URL': 'http://extras.denverpost.com' + document.location.pathname,
        'Slug':'',
        'Content ID':'',
        'Page Type':'data',
        'Publisher State':'CO',
        'Byline':'',
        'Content Title':document.title,
        'URL':document.location.href,
        'Page Title':document.title,
        'User ID':''
    });
    }
  </script>
  <!-- End Google Tag Manager Data Layer -->
<!-- Google Tag Manager --><noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-TLFP4R" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript><script>
if ( iframe !== 1 )
{
(function(w,d,s,l,i) {
   w[l]=w[l]||[];
   w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});
   var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
   j.async=true;
   j.src='//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})
(window,document,'script','dataLayer','GTM-TLFP4R');
}
</script><!-- End Google Tag Manager -->
</body
</html>
