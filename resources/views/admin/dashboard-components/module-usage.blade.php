<div class="row display-flex mb-2">
    <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-12">
        <div class="card cus-border-top height-100 custom-card-border widget-chart widget-chart2">
            <div class="export-data mt-3">
                <h4 class="heading col-xl-11 col-lg-11 col-md-11 col-sm-11 col-11">MODULE USAGE</h4>
                <span style="cursor: pointer"><a class="pull-right export-icon col-1 mr-4" id="download-icon"
                        onclick="redirect()"><i class="fa fa-download"></i></a></span>
            </div>

            <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10 mb-3">
                <div id="dou-chart"></div>
                <canvas id="doughnut_chart"></canvas>
                <div id="module"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
        <div class="card cus-border-top height-100 custom-card-border widget-chart widget-chart2">
            <h4 class="heading mt-3">CHATBOT QUESTIONS HITS</h4>
            <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10 question-hits-scroll mb-2"
                id="chat-question">
            </div>
        </div>
    </div>
</div>
@push('graph-scripts')
    <script type="text/javascript">
        var module_data = [];
        var top_count = [];
        let barChartCanvas, top_10_modules;

        function updateModule(data) {

            if (!window.top_10_modules) {
                barChartCanvas = document.getElementById("doughnut_chart").getContext("2d");
            } else {
                module_data = [];
                top_count = []
                if (window.top_10_modules) window.top_10_modules.destroy();
            }
            $('#dou-chart').empty();
            $('#download-icon').removeAttr("style");

            if (data.length == 0) {
                $('#module').empty();
                $('#dou-chart').prepend(
                    '<img id="theImg" src="./Group 86.svg" style="display:flex;margin: auto;opacity:0.3"/>'
                );
                $('#download-icon').attr("style", "display: none !important");
            } else {
                for (var i = 0; i < data.length; i++) {
                    const a = data[i];
                    module_data.push(a.action);
                    top_count.push(a.TotalCount);
                }
                window.top_10_modules = new Chart(barChartCanvas, {
                    type: 'pie',
                    options: {
                        legend: {
                            display: false,
                            // position: 'bottom',
                            // cursor: "pointer",
                            // labels: {
                            //     usePointStyle: true,
                            // },
                        },
                        // cutoutPercentage: 70,
                        // aspectRatio: 2.4,
                    },
                    data: {
                        labels: module_data,
                        datasets: [{
                            data: top_count,
                            // backgroundColor: ['#86FFF8', '#45C3E5', '#FFAEDA', '#748CE0',
                            //     '#FDD992',
                            //     '#FFFCBA', '#89DD7E', '#FFA56C', '#0000001F'
                            // ],
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
                            shadowColor:  ['#7885CB', '#AED581', '#E57979', '#FFA56C', '#45C3E5',
                                '#ED7DFF', '#FDD992', '#C39BD3', '#76D7C4', '#FFAEDA'
                            ],
                            bevelWidth: 0,
                            bevelHighlightColor:  ['#7885CB', '#AED581', '#E57979', '#FFA56C', '#45C3E5',
                                '#ED7DFF', '#FDD992', '#C39BD3', '#76D7C4', '#FFAEDA'
                            ],
                            bevelShadowColor: ['#445ad1', '#8dd837', '#de3838', '#f6883a', '#4cc1e6',
                                '#e378ff', '#fdd482', '#b264d3', '#70dac590', '#ea7ac8'
                            ],
                        }]
                    },
                });

                var myModuleLegend = document.getElementById("module");
                // generate HTML legend
                console.log("myModuleLegend", myModuleLegend);
                myModuleLegend.innerHTML = window.top_10_modules.generateLegend();
                // bind onClick event to all LI-tags of the legend
                var moduleItems = myModuleLegend.getElementsByTagName('li');
                for (var i = 0; i < moduleItems.length; i += 1) {
                    moduleItems[i].addEventListener("click", legendCallback, false);
                }

                function legendCallback(moduleEvent) {
                    console.log("inside legendCallback");
                    moduleEvent = moduleEvent || window.moduleEvent;
                    console.log("moduleEvent", moduleEvent);

                    var targetModule = moduleEvent.targetModule || moduleEvent.srcElement;
                    while (targetModule.nodeName != 'LI') {
                        targetModule = targetModule.parentElement;
                    }
                    var parent = targetModule.parentElement;
                    var chartIdModule = parseInt(parent.classList[0].split("-")[0], 10);
                    var chartModule = Chart.instances[chartIdModule];
                    console.log("chartModule", chartModule);
                    var indexModule = Array.prototype.slice.call(parent.children).indexOf(targetModule);
                    var meta = chartModule.getDatasetMeta(0);
                    var itemModule = meta.data[indexModule];
                    console.log("itemModule", itemModule);
                    if (itemModule.hidden == null || itemModule.hidden == false) {
                        console.log("hiddenModule", itemModule.hidden);
                        itemModule.hidden = true;
                        targetModule.classList.add('line-through');
                    } else {
                        targetModule.classList.remove('line-through');
                        itemModule.hidden = null;
                    }
                    chartModule.update();
                }

            }
        }

        function updateChatQuestion(data) {
            // console.log("chat question-->", data);
            $('#chat-question').empty();
            if (data.length == 0) {
                $('#chat-question').prepend(
                    '<img id="theImg" src="./Group 86.svg" style="display:flex;margin: auto;opacity:0.3"/>'
                );
            } else {
                var max = 500
                for (var i = 0; i < data.length; i++) {
                    const a = data[i];
                    const question = JSON.parse(a.question);
                    var per = (a.hit * 100) / max;
                    if (per > 100) {
                        per = 100
                    }
                    $('#chat-question').append(` <div class="text-right-content mb-1" title="${question?.en}" style="display: inline-block;width: 500px;white-space: nowrap;overflow: hidden !important;
                    text-overflow: ellipsis;">${question?.en}
                            </div>`);

                    $('#chat-question').append(`
                    <div class="progress mb-3" style="background-color:#FFF">
                                <div class="progress-bar" style="width:${per}%;background-color:${ per>75? "#57BE4A":per>35?"#FFA56C" : "#E57979"}"
                                    role="progressbar" aria-valuenow="${a.hit}" aria-valuemin="0" aria-valuemax="100">${a.hit}</div><div class="d-flex justify-conten-around align-items-center ml-2"></div>
                                </div>
                            `);
                }

            }
        }

        function redirect() {

            const urlParams = new URLSearchParams(window.location.search);
            var date_ref = urlParams.get('date');
            console.log("date", date_ref);
            if (date_ref == '') {
                console.log("inside date null");
                window.location = `export-module-usage?state_id=${state}&district=${district}&block=${block}&date=0`;
            } else {
                console.log("inside date not null");
                window.location =
                    `export-module-usage?state_id=${state}&district=${district}&block=${block}&date=${date_ref}`;
            }
        }
    </script>
@endpush
