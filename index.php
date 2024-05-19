<?php 
    if(isset($_GET["data"])){
        $f = fopen("data.csv","a");
        $date = new \DateTime();
        $date_string = $date->format(DATE_W3C);
        fwrite($f, $date_string.",".(int)$_GET["data"]."\n");
        fclose($f);
        echo $date_string.",".(int)$_GET["data"];
        exit();
    }
    else if(isset($_GET["clear"])){
        $f = fopen("data.csv","w");
        fwrite($f, "Time, Value\n");
        fclose($f);
    }
?>
<html>
	<head>
		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/modules/data.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>
		<script src="https://code.highcharts.com/modules/export-data.js"></script>
		<script src="https://code.highcharts.com/modules/accessibility.js"></script>
		<style>
		    .highcharts-figure,
            .highcharts-data-table table {
                min-width: 360px;
                max-width: 800px;
                margin: 1em auto;
            }
            
            .highcharts-data-table table {
                font-family: Verdana, sans-serif;
                border-collapse: collapse;
                border: 1px solid #ebebeb;
                margin: 10px auto;
                text-align: center;
                width: 100%;
                max-width: 500px;
            }
            
            .highcharts-data-table caption {
                padding: 1em 0;
                font-size: 1.2em;
                color: #555;
            }
            
            .highcharts-data-table th {
                font-weight: 600;
                padding: 0.5em;
            }
            
            .highcharts-data-table td,
            .highcharts-data-table th,
            .highcharts-data-table caption {
                padding: 0.5em;
            }
            
            .highcharts-data-table thead tr,
            .highcharts-data-table tr:nth-child(even) {
                background: #f8f8f8;
            }
            
            .highcharts-data-table tr:hover {
                background: #f1f7ff;
            }
            
            .ld-form {
                max-width: 800px;
                margin: 1em auto;
            }
            
            .ld-row {
                margin: 0.3em 0;
            }
            
            .ld-row input {
                line-height: 1.5em;
                padding: 0.375rem 0.75rem;
                border: 1px solid rgba(128, 128, 128, 0.3);
                border-radius: 0.25rem;
                color: #495057;
            }
            
            .ld-label {
                width: 200px;
                display: inline-block;
            }
            
            .ld-url-input {
                width: 400px;
                max-width: calc(100% - 2rem);
            }
            
            .ld-time-input {
                width: 40px;
            }
        </style>
    </head>
	<body>
		<figure class="highcharts-figure">
			<div id="container"></div>
			<p class="highcharts-description">
		Datasets formatted in CSV or JSON can be fetched remotely using the
				<code>data</code> module. This chart shows how a chart can also be
		configured to poll against the remote source.
			</p>
		</figure>

		<div class="ld-form">
			<div class="ld-row">
				<label class="ld-label">
			Enable Polling
				</label>
				<input type="checkbox" checked="checked" id="enablePolling"/>
			</div>
			<div class="ld-row">
				<label class="ld-label">
			Polling Time (Seconds)
				</label>
				<input class="ld-time-input" type="number" value="2" id="pollingTime"/>
			</div>
			<div class="ld-row">
				<label class="ld-label">
			CSV URL
				</label>
				<input class="ld-url-input" type="text" id="fetchURL"/>
			</div>
		</div>
		<script>
		    const defaultData = 'https://albin.si/analog/data.csv';
            const urlInput = document.getElementById('fetchURL');
            const pollingCheckbox = document.getElementById('enablePolling');
            const pollingInput = document.getElementById('pollingTime');
            
            function createChart() {
                Highcharts.chart('container', {
                    chart: {
                        type: 'areaspline'
                    },
                    title: {
                        text: 'Live Data'
                    },
                    accessibility: {
                        announceNewData: {
                            enabled: true,
                            minAnnounceInterval: 15000,
                            announcementFormatter: function (
                                allSeries,
                                newSeries,
                                newPoint) {
                                if (newPoint) {
                                    return 'New point added. Value: ' + newPoint.y;
                                }
                                return false;
                            }
                        }
                    },
                    plotOptions: {
                        areaspline: {
                            color: '#32CD32',
                            fillColor: {
                                linearGradient: { x1: 0, x2: 0, y1: 0, y2: 1 },
                                stops: [
                                    [0, '#32CD32'],
                                    [1, '#32CD3200']
                                ]
                            },
                            threshold: null,
                            marker: {
                                lineWidth: 1,
                                lineColor: null,
                                fillColor: 'white'
                            }
                        }
                    },
                    data: {
                        csvURL: urlInput.value,
                        enablePolling: pollingCheckbox.checked === true,
                        dataRefreshRate: parseInt(pollingInput.value, 10)
                    }
                });
            
                if (pollingInput.value < 1 || !pollingInput.value) {
                    pollingInput.value = 1;
                }
            }
            
            urlInput.value = defaultData;
            
            // We recreate instead of using chart update to make sure the loaded CSV
            // and such is completely gone.
            pollingCheckbox.onchange = urlInput.onchange =
               pollingInput.onchange = createChart;
            
            // Create the chart
            createChart();
        </script>
	</body>
</html>
