<div class="row display-flex mb-2">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card cus-border-top height-100 custom-card-border" style="cursor: pointer;">
            <h4 class="heading mt-3">CHATBOT KEYWORD HITS</h4>
            <div class="row col-md-12 m-0">
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="table-listing-design">
                        <table class="mb-0 table table-hover sortable" id="chat-keyword-data">
                            <thead id="keyword-hits-listing">
                                <tr style="border-bottom: 2.5px solid #30a9b880; border-top:1.5px solid #30a9b880;">
                                    <th width="15%">SR NO.</th>
                                    <th width="60%">KEYWORD</th>
                                    <th width="25%">HIT COUNT</th>
                                </tr>
                            </thead>
                            <tbody style="cursor: pointer;" onclick="window.location='admin/chat-keyword-hits';">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 ">
                    <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10 ">
                        <canvas id="keyword_hit" width="150px" height="110px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('graph-scripts')
    <script type="text/javascript">
        var keyword = [];
        var hitCount = [];
        var keywordName = [];
        let chatKeywordHit, chat_keyword_data;

        function updateChatKeyword(data) {
            // console.log("chat keyord--->", data);

            if (!window.chat_keyword_data) {
                chatKeywordHit = document.getElementById("keyword_hit").getContext("2d");
            } else {
                keyword = [];
                hitCount = [];
                keywordName = [];
                if (window.chat_keyword_data) window.chat_keyword_data.destroy();
            }
            $('#keyword-hits-listing').hide();
            $("#chat-keyword-data tbody").empty();
            // $('$keyword_hit').show();
            if (data.length == 0) {
                $('#chat-keyword-data tbody').prepend(
                    '<img id="theImg" src="./Group 86.svg" style="display:flex;margin: auto;opacity:0.3"/>'
                );
                // $('$keyword_hit').hide();
            } else {
                $('#keyword-hits-listing').show();
                for (var i = 0; i < data.length; i++) {
                    const a = data[i];
                    $("#chat-keyword-data tbody").append(`
                    <tr style="height:50px;border-bottom: 1.5px solid #30a9b880;">
                        <td>${i+1}</td>
                        <td>${JSON.parse(a.title)?.en}</td>
                        <td>${a.hit}</td>
                    </tr>
                `);
                    keyword.push(i + 1);
                    keywordName.push(JSON.parse(a.title)?.en);
                    hitCount.push(a.hit);
                }
            }
            // console.log("keyword array--->",keyword);
            window.chat_keyword_data = new Chart(chatKeywordHit, {
                type: 'bar',

                options: {
                    responsive: true,
                    cornerRadius: 19,
                    hover: {
                        animationDuration: 0
                    },
                    animation: {
                        duration: 1,
                        onComplete: function() {
                            var chartInstance = this.chart,
                                ctx = chartInstance.ctx;

                            ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart
                                .defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';

                            this.data.datasets.forEach(function(dataset, i) {
                                var meta = chartInstance.controller.getDatasetMeta(i);
                                meta.data.forEach(function(bar, index) {
                                    var data = dataset.data[index];
                                    ctx.fillText(data, bar._model.x, bar._model.y - 5);
                                });
                            });
                        }
                    },
                    borderRadius: 20,
                    scales: {
                        xAxes: [{
                            ticks: {
                                beginAtZero: true,
                                autoSkip: false,

                            },
                            gridLines: {
                                drawOnChartArea: false
                            },
                            maxBarThickness: 20,
                            scaleLabel: {
                                display: true,
                                labelString: 'Keywords Search Hits'
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                drawOnChartArea: false
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'No of visits'
                            },
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItem) {
                                return keywordName[tooltipItem[0].xLabel - 1];
                            }
                        }
                    }
                },
                data: {
                    labels: keyword,
                    datasets: [{
                        data: hitCount,
                        label: 'Keywords Search Hits',
                        // borderColor: '#30A9B8',
                        backgroundColor: '#30A9B8',
                        borderWidth: 1,
                        hoverBorderColor: '#57afc0',
                        hoverBorderWidth: 5,
                        shadowOffsetX: 10,
                        shadowOffsetY: 10,
                        shadowBlur: 5,
                        shadowColor: 'gray',
                        bevelWidth: 2,
                        bevelHighlightColor: '#30A9B8',
                        bevelShadowColor: '#30A9B8',
                    }]
                },
            });
        }
        Chart.Legend.prototype.afterFit = function() {
            this.height = this.height + 50;
        };

    </script>
@endpush
