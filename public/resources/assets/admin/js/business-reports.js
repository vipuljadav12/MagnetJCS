
$(function() {
	"use strict";
	var VectorMap = function() {
	};

	VectorMap.prototype.init = function() {

		$('#usa-01').vectorMap({
			map : 'us_aea_en',
			backgroundColor : 'transparent',			
			scaleColors : ['#45bbe0', '#45bbe0'],
			/*regionStyle : {
				initial : {
					fill : '#81878c'
				}
			},*/
			series: {
				markers: [{
					attribute: 'fill',
					scale: ['#eceeef', '#55595c'],
					normalizeFunction: 'polynomial',
					values: [200, 600, 1000, 1400],
					legend: {
					  //vertical: true
					}
				}],
				regions: [{
					values: gdpData,
					scale: ['#eceeef', '#55595c'],
					normalizeFunction: 'polynomial'
				}]
			},
			onRegionTipShow: function(e, el, code){
		  el.html(el.html()+' (Total Revenue - $'+gdpData[code]+')');
		}
		});
		$('#usa-02').vectorMap({
			map : 'us_aea_en',
			backgroundColor : 'transparent',			
			scaleColors : ['#45bbe0', '#45bbe0'],
			/*regionStyle : {
				initial : {
					fill : '#81878c'
				}
			},*/
			series: {
				markers: [{
					attribute: 'fill',
					scale: ['#eceeef', '#55595c'],
					normalizeFunction: 'polynomial',
					values: [200, 600, 1000, 1400],
					legend: {
					  //vertical: true
					}
				}],
				regions: [{
					values: gdpData,
					scale: ['#eceeef', '#55595c'],
					normalizeFunction: 'polynomial'
				}]
			},
			onRegionTipShow: function(e, el, code){
		  el.html(el.html()+' (Total Orders - '+gdpData[code]+')');
		}
		});
		$('#usa-03').vectorMap({
			map : 'us_aea_en',
			backgroundColor : 'transparent',			
			scaleColors : ['#45bbe0', '#45bbe0'],
			/*regionStyle : {
				initial : {
					fill : '#81878c'
				}
			},*/
			series: {
				markers: [{
					attribute: 'fill',
					scale: ['#eceeef', '#55595c'],
					normalizeFunction: 'polynomial',
					values: [200, 600, 1000, 1400],
					legend: {
					  //vertical: true
					}
				}],
				regions: [{
					values: gdpData,
					scale: ['#eceeef', '#55595c'],
					normalizeFunction: 'polynomial'
				}]
			},
			onRegionTipShow: function(e, el, code){
		  el.html(el.html()+' (Total Customers - '+gdpData[code]+')');
		}
		});
	},
	//init
	$.VectorMap = new VectorMap, $.VectorMap.Constructor = VectorMap
})
//initializing
$(function() {
	"use strict";
	$.VectorMap.init()
})
	
	
function custcolor(i){
	if(i == 'theme02') {
		var chartColors = {
			color1: 'rgba(57, 57, 170, 0.2)',
			color2: 'rgba(57, 57, 170, 1)',
			color3: 'rgba(57, 57, 170, 0.8)'
		}
	}
	else if(i == 'theme03') {
		var chartColors = {
			color1: 'rgba(52, 211, 235, 0.2)',
			color2: 'rgba(52, 211, 235, 1)',
			color3: 'rgba(52, 211, 235, 0.8)'
		}
	}
	else if(i == 'theme04') {
		var chartColors = {
			color1: 'rgba(85, 83, 206, 0.2)',
			color2: 'rgba(85, 83, 206, 1)',
			color3: 'rgba(85, 83, 206, 0.8)'
		}
	}
	else if(i == 'theme05') {
		var chartColors = {
			color1: 'rgba(208, 128, 33, 0.2)',
			color2: 'rgba(208, 128, 33, 1)',
			color3: 'rgba(208, 128, 33, 0.8)'
		}
	}
	else {
		var chartColors = {
			color1: 'rgba(238, 104, 72, 0.2)',
			color2: 'rgba(238, 104, 72, 1)',
			color3: 'rgba(238, 104, 72, 0.8)'
		}
	}
	
	Chart.defaults.global.defaultFontFamily = "Open Sans";
	
	var custconf = {
		type: 'bar',
		data: {		
			labels: ['2015', '2016', '2017', '2018', '2019'],
			datasets: [{
				label: 'Amount ($)',
				data: [1501, 1757, 2144, 1109, 1884],
				backgroundColor: '#D08021',
				borderColor: '#D08021',
				pointBackgroundColor: chartColors.color3,
				pointBorderColor: chartColors.color2,
				pointHoverBorderColor: chartColors.color2,
				pointHoverBackgroundColor: chartColors.color2,
				borderWidth: 2,
				yAxisID: 'amount',
			},{
				label: 'Order',
				data: [470, 370, 610, 598, 780],
				backgroundColor: '#28a745',
				borderColor: '#28a745',
				pointBackgroundColor: chartColors.color3,
				pointBorderColor: chartColors.color2,
				pointHoverBorderColor: chartColors.color2,
				pointHoverBackgroundColor: chartColors.color2,
				borderWidth: 2,
				yAxisID: 'order',
			}]
		},
		options: {
			legend: {
				labels: {
					fontSize: 16,
				}
			},
			responsive: true,
			tooltips: {
				mode: 'label',
				/*callbacks: {
                    label: function(tooltipItems, data) { 
                        return '$'+tooltipItems.yLabel;
                    }
                }*/
			},
			hover: {
				mode: 'nearest',
				intersect: false
			},
			elements: {
				point: {
					pointStyle: 'circle'
				}
			},
			scales: {
				xAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Days',
						fontSize: 16,
					},
					ticks: {
						fontSize: 13,
					}
				}],
				yAxes: [{
					id: 'amount',
					position: 'left',
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Amount',
						fontSize: 16,
					},
					ticks: {
						fontSize: 13,
						stepSize: 500,
						min: 0,
						//max: 3000
					}
				},{
					id: 'order',
					position: 'right',
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Orders',
						fontSize: 16,
					},
					ticks: {
						fontSize: 13,
						stepSize: 500,
						min: 0,
						//max: 30
					}
				}]
			}
		}
	};
	
	
	var custconf1 = {
		type: 'bar',
		data: {		
			labels: ['2018', '2019'],
			datasets: [{
				label: 'All Time number of Orders',
				data: [1109, 1884],
				backgroundColor: '#e26241',
				borderColor: '#e26241',
				pointBackgroundColor: chartColors.color3,
				pointBorderColor: chartColors.color2,
				pointHoverBorderColor: chartColors.color2,
				pointHoverBackgroundColor: chartColors.color2,
				borderWidth: 2,
				yAxisID: 'amount',
			}]
		},
		options: {
			legend: {
				labels: {
					fontSize: 16,
				}
			},
			responsive: true,
			tooltips: {
				mode: 'label',
				/*callbacks: {
                    label: function(tooltipItems, data) { 
                        return '$'+tooltipItems.yLabel;
                    }
                }*/
			},
			hover: {
				mode: 'nearest',
				intersect: false
			},
			elements: {
				point: {
					pointStyle: 'circle'
				}
			},
			scales: {
				xAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Days',
						fontSize: 16,
					},
					ticks: {
						fontSize: 13,
					}
				}],
				yAxes: [{
					id: 'amount',
					position: 'left',
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Amount',
						fontSize: 16,
					},
					ticks: {
						fontSize: 13,
						stepSize: 500,
						min: 0,
						//max: 3000
					}
				}]
			}
		}
	};
	
	
		var custconf2 = {
		type: 'bar',
		data: {		
			labels: ['2014', '2015', '2016', '2017', '2018', '2019'],
			datasets: [{
				label: 'All Time number of Orders',
				data: [480, 1089, 1204, 1874, 1675, 1790],
				backgroundColor: '#a34a28',
				borderColor: '#a34a28',
				pointBackgroundColor: chartColors.color3,
				pointBorderColor: chartColors.color2,
				pointHoverBorderColor: chartColors.color2,
				pointHoverBackgroundColor: chartColors.color2,
				borderWidth: 2,
				yAxisID: 'amount',
			}]
		},
		options: {
			legend: {
				labels: {
					fontSize: 16,
				}
			},
			responsive: true,
			tooltips: {
				mode: 'label',
				/*callbacks: {
                    label: function(tooltipItems, data) { 
                        return '$'+tooltipItems.yLabel;
                    }
                }*/
			},
			hover: {
				mode: 'nearest',
				intersect: false
			},
			elements: {
				point: {
					pointStyle: 'circle'
				}
			},
			scales: {
				xAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Days',
						fontSize: 16,
					},
					ticks: {
						fontSize: 13,
					}
				}],
				yAxes: [{
					id: 'amount',
					position: 'left',
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Amount',
						fontSize: 16,
					},
					ticks: {
						fontSize: 13,
						stepSize: 500,
						min: 0,
						//max: 3000
					}
				}]
			}
		}
	};
	
	
	var graph = document.getElementById('alltimerevenue');
	var myLineChart = new Chart(graph, custconf);
	
	var graph1 = document.getElementById('alltimecustomers');
	var myLineChart1 = new Chart(graph1, custconf1);
	
	var graph2 = document.getElementById('alltimeaveragerevenue');
	var myLineChart1 = new Chart(graph2, custconf2);

}
custcolor();	

// Load google charts
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

// Draw the chart and set the chart values
function drawChart() {
  var data = google.visualization.arrayToDataTable([
  ['Task', 'Hours per Day'],
  ['Lorem Ipsum 01', 8],
  ['Lorem Ipsum 02', 2],
  ['Lorem Ipsum 03', 2],
  ['Lorem Ipsum 04', 2],
  ['Lorem Ipsum 05', 2],
  ['Lorem Ipsum 06', 8]
]);

  // Optional; add a title and set the width and height of the chart
  var options = {'title':'Top 10 Category', 'width':'100%', 'height':400};

  // Display the chart inside the <div> element with id="piechart"
  var chart = new google.visualization.PieChart(document.getElementById('piechart'));
  chart.draw(data, options);
}
1
// Load google charts
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart1);

// Draw the chart and set the chart values
function drawChart1() {
  var data = google.visualization.arrayToDataTable([
  ['Task', 'Hours per Day'],
  ['Lorem Ipsum 01', 8],
  ['Lorem Ipsum 02', 2],
  ['Lorem Ipsum 03', 2],
  ['Lorem Ipsum 04', 2],
  ['Lorem Ipsum 05', 2],
  ['Lorem Ipsum 06', 8]
]);

  // Optional; add a title and set the width and height of the chart
  var options = {'title':'Top 10 Category', 'width':'100%', 'height':400};

  // Display the chart inside the <div> element with id="piechart"
  var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
  chart.draw(data, options);
}

// Load google charts
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart2);

// Draw the chart and set the chart values
function drawChart2() {
  var data = google.visualization.arrayToDataTable([
  ['Task', 'Hours per Day'],
  ['Lorem Ipsum 01', 8],
  ['Lorem Ipsum 02', 2],
  ['Lorem Ipsum 03', 2],
  ['Lorem Ipsum 04', 2],
  ['Lorem Ipsum 05', 2],
  ['Lorem Ipsum 06', 8]
]);

  // Optional; add a title and set the width and height of the chart
  var options = {'title':'Top 10 Category', 'width':'100%', 'height':400};

  // Display the chart inside the <div> element with id="piechart"
  var chart = new google.visualization.PieChart(document.getElementById('piechart3'));
  chart.draw(data, options);
}

