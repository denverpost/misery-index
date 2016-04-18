// MISERY JAVASCRIPT
// So we don't cut and paste the same js all over the place.


$.getJSON( fn['scores'], function( data ) {
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
            // The number of tears is determined by the misery count of the
            // last day we have a score for.
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
var misery_dates = {
    start: start_date,
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

misery_dates.init();
var $chart = $('#chart');
//var mobile_threshold = 500;
var aspect = { width: 12, height: 6 };
var chart_width = misery_dates.delta * 6;

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

    d.date = d.date.replace(season_year, '\'');

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
// This is the list of bad things that have happened.
$.getJSON( fn['recent'], function( data ) {
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
