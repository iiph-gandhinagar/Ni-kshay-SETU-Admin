<div class="row display-flex  mb-2">
    <div class="col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
        <div class="card cus-border-top height-100 custom-card-border">
            <div class="widget-chart widget-chart2">
                <h4 class="heading mt-3">ASSESSMENT SUBMISSION</h4>
                <h6 class="line-chart assessment-month"></h6>
                <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10" id="assessment_chart">
                    <canvas id="assessment_submitted_graph" height="170px" style="margin-bottom: 10px"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-12">
        <div class="card cus-border-top height-100 custom-card-border">
            <div class="widget-chart widget-chart2">
                <div class="export-data mt-3">
                    <h4 class="heading col-xl-11 col-lg-11 col-md-11 col-sm-11 col-11">CADRE WISE SUBSCRIBERS</h4>
                    <span style="cursor: pointer"><a id="download-report" class="pull-right export-icon col-1 mr-4"
                            onclick="cadreModule()" role="button"><i class="fa fa-download"></i></a></span>
                </div>
                <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10 mt-2">
                    <div id="cadre-dou-chart"></div>
                    <canvas id="cadre_wise_doughnut_chart" style="margin-left: 4px; margin-bottom:10px"
                        class="d-flex justify-content-center align-items-center">{{-- height="170%" --}}
                    </canvas>
                </div>
                <div id="legend" class="legend-display"></div>
            </div>
        </div>
    </div>
</div>

@push('graph-scripts')
    <script type="text/javascript">
        var chartLabels = [];
        var subscriberCount = [];
        var cadre_data = [];
        var total_count_data = [];
        let assessment_submitted, lineChartCanvas;
        let doughnutChart, cadre_subscriber;


        function updateAssessment(data) {

            if (!window.assessment_submitted) {
                lineChartCanvas = document.getElementById("assessment_submitted_graph").getContext("2d");
            } else {
                subscriberCount = [];
                chartLabels = []
                if (window.assessment_submitted) window.assessment_submitted.destroy();
            }
            for (var i = 0; i < data.length; i++) {
                const a = data[i];
                chartLabels.push(a.date);
                subscriberCount.push(a.subscriber_count);

            }
            window.assessment_submitted = new Chart(lineChartCanvas, {
                type: 'line',

                options: {
                    legend: {
                        display: true,
                        position: 'top',
                        cursor: "pointer",
                        labels: {
                            usePointStyle: true,
                        },
                    },
                    tooltips: {
                        enabled: true,
                        intersect: true,
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                beginAtZero: true,
                                autoSkip: false,

                            },
                            maxBarThickness: 20,
                            scaleLabel: {
                                display: true,
                                labelString: 'Month'
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'No of subscriber',
                                ticks: {
                                    min: 6,
                                    max: 16
                                }
                            },
                        }]
                    },

                },
                data: {

                    labels: chartLabels,
                    datasets: [{
                        label: "Assessment Completed",
                        data: subscriberCount,
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "#30A9B8",
                        borderColor: "#30A9B8",
                        borderWidth: 2,
                        pointStyle: 'circle',
                        // hoverOffset: 15
                        shadowOffsetX: 10,
                        shadowOffsetY: 10,
                        shadowBlur: 5,
                        shadowColor: 'gray',
                        bevelWidth: 2,
                        bevelHighlightColor: 'gray',
                        bevelShadowColor: 'gray',
                    }]
                },
            });
        }
        // Chart.Legend.prototype.afterFit = function() {
        //     this.height = this.height + 20;
        // };

        function updateCadreWiseSubscriber(data) {
            // console.log("cadre wise subscriber-->",data);
            if (!window.cadre_subscriber) {
                // alert('1')
                doughnutChart = document.getElementById("cadre_wise_doughnut_chart").getContext("2d");
            } else {
                // alert('2')
                cadre_data = [];
                total_count_data = []
                if (window.cadre_subscriber) window.cadre_subscriber.destroy();
            }
            $('#cadre-dou-chart').empty();
            $('#download-report').removeAttr("style");

            if (data.length == 0) {
                $('#legend').empty();
                $('#cadre-dou-chart').prepend(
                    '<img id="theImg" src="./Group 86.svg" style="position: absolute;top: 0;bottom: 0;left: 0;right: 0;margin: auto;opacity:0.3"/>'
                );
                $('#download-report').attr("style", "display: none !important");
            } else {
                for (var i = 0; i < data.length; i++) {
                    const a = data[i];
                    cadre_data.push(a.CadreName);
                    total_count_data.push(a.TotalCadreCount);
                }

                window.cadre_subscriber = new Chart(doughnutChart, {
                    type: 'doughnut',
                    options: {
                        legend: {
                            display: false,
                            // position: 'bottom',
                            // cursor: "pointer",
                            // max:2,
                            // labels: {
                            //     usePointStyle: true,
                            //     padding: 20,
                            // },

                        },
                        cutoutPercentage: 70,
                        aspectRatio: 2.2,
                    },
                    data: {
                        labels: cadre_data,
                        datasets: [{
                            data: total_count_data,
                            backgroundColor: ['#7885CB', '#AED581', '#E57979', '#FFA56C', '#45C3E5',
                                '#ED7DFF', '#FDD992', '#C39BD3', '#76D7C4', '#FFAEDA'
                            ],
                            borderWidth: 1,
                            hoverBorderColor: ['#445ad1', '#8dd837', '#de3838', '#f6883a', '#4cc1e6',
                                '#e378ff', '#fdd482', '#b264d3', '#70dac590', '#ea7ac8'
                            ],
                            hoverBorderWidth: 5,
                            shadowOffsetX: 5,
                            shadowOffsetY: 5,
                            shadowBlur: 30,
                            shadowColor: ['#445ad1', '#8dd837', '#de3838', '#f6883a', '#4cc1e6',
                                '#e378ff', '#fdd482', '#b264d3', '#70dac590', '#ea7ac8'
                            ],
                            bevelWidth: 0,
                            bevelHighlightColor: ['#7885CB', '#AED581', '#E57979', '#FFA56C', '#45C3E5',
                                '#ED7DFF', '#FDD992', '#C39BD3', '#76D7C4', '#FFAEDA'
                            ],
                            bevelShadowColor: ['#445ad1', '#8dd837', '#de3838', '#f6883a', '#4cc1e6',
                                '#e378ff', '#fdd482', '#b264d3', '#70dac590', '#ea7ac8'
                            ],

                        }]
                    },
                });

                var myLegendContainer = document.getElementById("legend");
                // generate HTML legend
                myLegendContainer.innerHTML = window.cadre_subscriber.generateLegend();
                // bind onClick event to all LI-tags of the legend
                console.log("myLegendContainer inner html", myLegendContainer.innerHTML);
                var legendItems = myLegendContainer.getElementsByTagName('li');
                for (var i = 0; i < legendItems.length; i += 1) {
                    console.log("inside for,loop");
                    legendItems[i].addEventListener("click", legendClickCallback, false);
                }

                function legendClickCallback(event) {
                    event = event || window.event;
                    console.log("event", event);

                    var target = event.target || event.srcElement;
                    while (target.nodeName != 'LI') {
                        target = target.parentElement;
                    }
                    var parent = target.parentElement;
                    var chartId = parseInt(parent.classList[0].split("-")[0], 10);
                    console.log("chartId", chartId);
                    var chart = Chart.instances[chartId];
                    console.log("chart", chart);
                    var index = Array.prototype.slice.call(parent.children).indexOf(target);
                    var meta = chart.getDatasetMeta(0);
                    var item = meta.data[index];
                    console.log("itemModule assessment", item);
                    if (item.hidden == null || item.hidden == false) {
                        item.hidden = true;
                        target.classList.add('line-through');
                    } else {
                        target.classList.remove('line-through');
                        item.hidden = null;
                    }
                    chart.update();
                }
            }
        }


        function cadreModule() {
            const urlParams = new URLSearchParams(window.location.search);
            var date_ref = urlParams.get('date');
            console.log("date", date_ref);
            if (date_ref == '') {
                console.log("inside date null");
                window.location =
                    `export-cadre-wise-subscribers?state_id=${state}&district=${district}&block=${block}&date=0`;
            } else {
                console.log("inside date not null");
                window.location =
                    `export-cadre-wise-subscribers?state_id=${state}&district=${district}&block=${block}&date=${date_ref}`;
            }
        }
    </script>
@endpush
