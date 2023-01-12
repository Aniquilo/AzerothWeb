<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}          
?>

<!-- Secondary navigation -->
<nav id="secondary">
	<ul>
		<li class="current"><a href="#maintab">Dashboard</a></li>
	</ul>
</nav>
          
<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">
        <div class="column left half" style="padding: 0 40px 40px 40px; box-sizing: border-box;">
            <h2>Visitors</h2>

            <div style="position: relative; background: rgba(255,255,255,0.2); padding: 20px 10px 10px 10px;">
                <canvas id="visitors" style="position: relative;"></canvas>
            </div>
        </div>
        <div class="column left half" style="padding: 0 40px 40px 40px; box-sizing: border-box;">
            <h2>Page Views</h2>

            <div style="position: relative; background: rgba(255,255,255,0.2); padding: 20px 10px 10px 10px;">
                <canvas id="pageviews" style="position: relative;"></canvas>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script>
var ctx = document.getElementById('visitors').getContext('2d');
var visitorsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            <?php foreach ($visitors as $day) { echo "'".$day['text']."', "; } ?>
        ],
        datasets: [{
            label: ' Visitors',
            data: [
                <?php foreach ($visitors as $day) { echo $day['visitors'].", "; } ?>
            ],
            backgroundColor: 'rgba(0, 0, 0, 0.2)',
            borderColor: 'rgba(0, 0, 0, 1)',
            borderWidth: 1
        }]
    },
    options: {
        legend: false,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});

var ctx2 = document.getElementById('pageviews').getContext('2d');
var pageviewsChart = new Chart(ctx2, {
    type: 'line',
    data: {
        labels: [
            <?php foreach ($visitors as $day) { echo "'".$day['text']."', "; } ?>
        ],
        datasets: [{
            label: ' Page Views',
            data: [
                <?php foreach ($visitors as $day) { echo $day['pageviews'].", "; } ?>
            ],
            backgroundColor: 'rgba(0, 0, 0, 0.2)',
            borderColor: 'rgba(0, 0, 0, 1)',
            borderWidth: 1
        }]
    },
    options: {
        legend: false,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
</script>