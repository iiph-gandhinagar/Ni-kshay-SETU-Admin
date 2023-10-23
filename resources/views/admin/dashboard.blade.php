@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Dashboard')




@section('body')

    <link href="{{ asset('css/dashboard-template.css') }}" rel="stylesheet">


    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify" style="color:#30AAB9"></i> <a style="color:#30AAB9">Dashboard</a>
                </div>
                <br>
                <div class="card-body" v-cloak>
                    <div class="card-block">
                        <div class="row">
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content" style="background: #458aa5;cursor: pointer;"
                                    onclick='window.location="admin/subscribers";'>
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Total Subscribers</div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span>{{ $subscriber }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content" style="background: #458aa5;cursor: pointer;"
                                    onclick='window.location="admin/user-assessments?date=" + ( moment(new Date()).format("DD/MM/YYYY"));'>
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Today's Completed Assessments</div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white">
                                                <span>{{ $completeAssessmentCount }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content" style="background: #458aa5;cursor: pointer;"
                                    onclick='window.location="admin/subscribers?from_date=" + (moment(new Date()).format("YYYY-MM-DD"));'>
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Subscribers Enrolled Today</div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white">
                                                <span>{{ $subscriberEnrollToday }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-danger" style="cursor: pointer;">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Enquiries Today</div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span>{{ $enquiry }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-danger" style="cursor: pointer;"
                                    onclick='window.location="admin/subscriber-activities?date=" + (moment(new Date()).format("DD/MM/YYYY"));'>
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Todays' Visitors</div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white">
                                                <span>{{ $subscriberAppVistCount }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-danger" style="cursor: pointer;"
                                    onclick='window.location="admin/user-assessments";'>
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Total Completed Assessments</div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span>{{ $assessment }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 main-card p-0">
                                    <div class="card-header dashboard-card">State Wise Subscribers
                                        <div class="btn-actions-pane-right">
                                        </div>
                                    </div>
                                    <div class="card mb-3 widget-chart widget-chart2 text-left w-110">
                                        <div class="widget-chat-wrapper-outer">
                                            <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10 ">
                                                <?php
                                                $state = [];
                                                $count = [];
                                                ?>

                                                @foreach ($stateWiseSubscriber as $md)
                                                    <div style="display:none;">
                                                        {{ array_push($state, $md->StateName) }}
                                                        {{ array_push($count, $md->TotalCount) }}
                                                    </div>
                                                @endforeach
                                                <canvas id="doughnut_chart">
                                                </canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 main-card">
                                    <div class="card-header dashboard-card">Modules Usage
                                        <div class="btn-actions-pane-right">
                                        </div>
                                    </div>
                                    <div class="card mb-3 widget-chart widget-chart2 text-left w-110">
                                        <div class="widget-chat-wrapper-outer mb-3">
                                            <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10">
                                                <?php
                                                $modules_data = [];
                                                $count_data = [];
                                                ?>

                                                @foreach ($top10Modules as $md)
                                                    <div style="display:none;">
                                                        {{ array_push($modules_data, $md['action']) }}
                                                        {{ array_push($count_data, $md['TotalCount']) }}
                                                    </div>
                                                @endforeach
                                                <canvas id="pie_chart">
                                                </canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 main-card p-0">
                                    <div class="card-header dashboard-card">Module Visits
                                        <div class="btn-actions-pane-right">
                                        </div>
                                    </div>
                                    <div class="card mb-3 widget-chart widget-chart2 text-left w-100">
                                        <div class="widget-chat-wrapper-outer">
                                            <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10 m-0">
                                                <?php
                                                $modules = [];
                                                $totalCount = [];
                                                ?>

                                                @foreach ($top10Modules as $md)
                                                    <div style="display:none;">
                                                        {{ array_push($modules, $md['action']) }}
                                                        {{ array_push($totalCount, $md['TotalCount']) }}
                                                    </div>
                                                @endforeach
                                                <canvas id="top_10_modules"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 main-card mb-3">
                                    <div class="card-header dashboard-card">Module Visits(Today)
                                        <div class="btn-actions-pane-right">
                                        </div>
                                    </div>
                                    <div class="card mb-3 widget-chart widget-chart2 text-left w-100">
                                        <div class="widget-chat-wrapper-outer mb-3">
                                            <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10">
                                                <?php
                                                $modulesTotal = [];
                                                $total10Count = [];
                                                ?>

                                                @foreach ($top10ModulesToday as $md)
                                                    <div style="display:none;">
                                                        {{ array_push($modulesTotal, $md['action']) }}
                                                        {{ array_push($total10Count, $md['TotalCount']) }}
                                                    </div>
                                                @endforeach
                                                <canvas id="top_10_modules_today"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 p-0">
                                    <div class="main-card mb-3 card" style="cursor: pointer;"
                                        onclick="window.location='admin/chat-keyword-hits';">
                                        <div class="card-header dashboard-card">Keyword Hit Count
                                            <div class="btn-actions-pane-right">
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="align-middle mb-0 table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="20%">Id</th>
                                                        <th width="50%">Keyword</th>
                                                        <th width="30%">Hit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($keywordHit as $item)
                                                        <tr style="height:50px">
                                                            <td class="text-center text-muted">{{ $loop->iteration }}
                                                            </td>
                                                            <td>{{ $item->title }}</td>
                                                            <td>{{ $item->hit }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 main-card mb-3">
                                    <div class="card-header dashboard-card">Keyword Hits
                                        <div class="btn-actions-pane-right">
                                        </div>
                                    </div>
                                    <div class="card mb-3 widget-chart widget-chart2 text-left w-200">
                                        <div class="widget-chat-wrapper-outer">
                                            <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10 m-1">
                                                <canvas id="treemap_chart" height="530px">
                                                </canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 p-0">
                                    <div class="main-card mb-3 card" style="cursor: pointer;"
                                        onclick="window.location='admin/chat-question-hits';">
                                        <div class="card-header dashboard-card">Questions Hit Count
                                            <div class="btn-actions-pane-right">
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="align-middle mb-0 table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%">Id</th>
                                                        <th width="80%">Question</th>
                                                        <th width="15%">Hit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($questionHit as $item)
                                                        <tr style="height:50px">
                                                            <td class="text-center text-muted">{{ $loop->iteration }}
                                                            </td>
                                                            <td>{{ $item->question }}</td>
                                                            <td>{{ $item->hit }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 main-card mb-2">
                                    <div class="card-header dashboard-card">Question Category Hits
                                        <div class="btn-actions-pane-right">
                                        </div>
                                    </div>
                                    <div class="card mb-2 widget-chart widget-chart2 text-left w-200">
                                        <div class="widget-chat-wrapper-outer">
                                            <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10 m-2">
                                                <canvas id="question_tree_map_chart" height="530px">
                                                </canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 p-0">
                                    <div class="main-card mb-3 card" style="cursor: pointer;"
                                        onclick="window.location='admin/user-assessments';">
                                        <div class="card-header dashboard-card">Assessments Submitted Today
                                            <div class="btn-actions-pane-right">
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="align-middle mb-0 table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%">Id</th>
                                                        <th width="25%">Assessment</th>
                                                        <th width="30%">User</th>
                                                        <th width="5%">Total</th>
                                                        <th width="5%">Obtained</th>
                                                        <th width="30%">Submit Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($assessmentGiven as $item)
                                                        <tr style="height:50px">
                                                            <td class="text-center text-muted">{{ $loop->iteration }}
                                                            </td>
                                                            <td title="{{ $item->assessment_with_trashed->assessment_title }}"
                                                                style="display: inline-block;
                                                                            width: 120px;
                                                                            white-space: nowrap;
                                                                            overflow: hidden !important;
                                                                            text-overflow: ellipsis;">
                                                                {{ $item->assessment_with_trashed->assessment_title }}
                                                            </td>
                                                            <td title="{{ $item->user->name }}">
                                                                {{ $item->user->name }}
                                                            </td>
                                                            <td>{{ $item->total_marks }}</td>
                                                            <td>{{ $item->obtained_marks }}</td>
                                                            <td>{{ DateTimeUtils::getDateInddmmmYYYYWithTime($item->created_at) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 main-card mb-2">
                                    <div class="card-header dashboard-card">Total Assessment Submitted
                                        <div class="btn-actions-pane-right">
                                        </div>
                                    </div>
                                    <div class="card mb-3 widget-chart widget-chart2 text-left w-100">
                                        <div class="widget-chat-wrapper-outer">
                                            <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10 m-0 p-4">
                                                <?php
                                                $date = [];
                                                $subscriberCount = [];
                                                ?>

                                                @foreach ($asessmentGraph as $graph)
                                                    <div style="display:none;">
                                                        {{ array_push($date, $graph->date) }}
                                                        {{ array_push($subscriberCount, $graph->subscriber_count) }}
                                                    </div>
                                                @endforeach
                                                <canvas id="assessment_submitted_graph" height="200px"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 p-0">
                                    <div class="main-card mb-3 card" style="cursor: pointer;"
                                        onclick="window.location='admin/subscriber-activities';">
                                        <div class="card-header dashboard-card">Subscribers Activity
                                            <div class="btn-actions-pane-right">
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="align-middle mb-0 table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%">Id</th>
                                                        <th width="25%">User</th>
                                                        <th width="20%">Action</th>
                                                        {{-- <th width="10%">Ip Address</th> --}}
                                                        <th width="10%">Plateform</th>
                                                        <th width="20%">Action Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($subscrierActivity as $item)
                                                        <tr style="height:50px">
                                                            <td class="text-center text-muted">{{ $loop->iteration }}
                                                            </td>
                                                            <td>{{ $item->user->name }}</td>
                                                            <td title="{{ $item->action }}" style="display: inline-block;
                                                                            width: 120px;
                                                                            white-space: nowrap;
                                                                            overflow: hidden !important;
                                                                            text-overflow: ellipsis;">
                                                                {{ $item->action }}
                                                            </td>
                                                            {{-- <td>{{ $item->ip_address }}</td> --}}
                                                            <td>{{ $item->plateform }}</td>
                                                            <td>{{ DateTimeUtils::getDateInddmmmYYYYWithTime($item->created_at) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
    @section('bottom-scripts')

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="{{ asset('js/dashboard.js') }}" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.0.2/chart.js" integrity="sha512-n8DscwKN6+Yjr7rI6mL+m9nS4uCEgIrKRFcP0EOkIvzOLUyQgOjWK15hRfoCJQZe0s6XrARyXjpvGFo1w9N3xg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-treemap@0.2.3"></script>
        <script type="text/javascript">
            //overall graph for module
            var modules = <?php print_r(json_encode($modules)); ?>;
            var totalCount = <?php print_r(json_encode($totalCount)); ?>;
            console.log("total Count", totalCount);
            console.log("total modules", modules);
            var barChartCanvas = document.getElementById("top_10_modules").getContext("2d");
            top_10_modules = new Chart(barChartCanvas, {
                type: 'bar',

                options: {
                    scales: {
                        xAxes: [{
                            ticks: {
                                beginAtZero: true,
                                autoSkip: false,

                            },
                            maxBarThickness: 20,
                            scaleLabel: {
                                display: true,
                                labelString: 'Module'
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'No of visits'
                            },
                        }]
                    },
                    // legend: {
                    //     display: false,
                    // }
                },
                data: {
                    labels: modules,
                    datasets: [{
                        data: totalCount,
                        label: 'Module Name',
                        backgroundColor: ['#3ca55c', '#3ca55c', '#3ca55c', '#3ca55c', '#3ca55c', '#3ca55c',
                            '#3ca55c', '#3ca55c', '#3ca55c', '#3ca55c'
                        ],
                        borderWidth: 1,
                    }]
                },
            });


            //today graph for module
            var modulesTotal = <?php print_r(json_encode($modulesTotal)); ?>;
            var total10Count = <?php print_r(json_encode($total10Count)); ?>;
            console.log("total Count", total10Count);
            console.log("total modules", modulesTotal);
            var barChartCanvas = document.getElementById("top_10_modules_today").getContext("2d");
            top_10_modules = new Chart(barChartCanvas, {
                type: 'bar',

                options: {
                    scales: {
                        xAxes: [{
                            ticks: {
                                beginAtZero: true,
                                autoSkip: false,

                            },
                            maxBarThickness: 20,
                            scaleLabel: {
                                display: true,
                                labelString: 'Module'
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'No of visits'
                            },
                        }]
                    },
                    // legend: {
                    //     display: false,
                    // }
                },
                data: {
                    labels: modulesTotal,
                    datasets: [{
                        data: total10Count,
                        label: 'Module Name',
                        backgroundColor: ['#3ca55c', '#3ca55c', '#3ca55c', '#3ca55c', '#3ca55c', '#3ca55c',
                            '#3ca55c', '#3ca55c', '#3ca55c', '#3ca55c'
                        ],
                        borderWidth: 1,
                        fillOpacity: .9,
                    }]
                },
            });

            var state_data = <?php print_r(json_encode($state)); ?>;
            var count_data = <?php print_r(json_encode($count)); ?>;
            console.log("total Count", state_data);
            console.log("total modules", count_data);
            var barChartCanvas = document.getElementById("doughnut_chart").getContext("2d");
            top_10_modules = new Chart(barChartCanvas, {
                type: 'doughnut',

                data: {
                    labels: state_data,
                    datasets: [{
                        data: count_data,
                        backgroundColor: ['#3f6ad8', '#fd7e14', '#006400', '#ffc107', '#d92550', '#794c8a',
                            '#e83e8c', '#28a745', '#0771B2', '#8911F9', '#cd8300', '#17a7f5', '#008080'
                        ],
                        borderWidth: 1
                    }]
                },
            });

            var module_data = <?php print_r(json_encode($modules_data)); ?>;
            var top_count = <?php print_r(json_encode($count_data)); ?>;
            console.log("total Count", module_data);
            console.log("total modules", top_count);
            var barChartCanvas = document.getElementById("pie_chart").getContext("2d");
            top_10_modules = new Chart(barChartCanvas, {
                type: 'pie',
                options: {
                    legend: {
                        display: true,
                        position: 'left',
                    },
                },
                data: {
                    labels: module_data,
                    datasets: [{
                        data: top_count,
                        backgroundColor: ['#3f6ad8', '#16aaff', '#fd7e14', '#006400', '#28a745', '#20c997',
                            '#ffc107', '#d92550', '#794c8a', '#e83e8c'
                        ],
                        borderWidth: 1
                    }]
                },
            });

            var topTags = <?php print_r(json_encode($keywordHit)); ?>;
            console.log(topTags);
            var ctx = document.getElementById("treemap_chart").getContext("2d");;
            var chart = window.chart = new Chart(ctx, {
                type: "treemap",
                data: {
                    datasets: [{
                        label: "Hits",
                        tree: topTags,
                        key: "hit",
                        groups: ['title'],
                        spacing: 0.5,
                        borderWidth: 2.0,
                        fontColor: "black",
                        backgroundColor: ['#468de3', '#16aaff', '#fd7e14', '#29cc47', '#28a745', '#20c997',
                            '#ffc107', '#d92550', '#794c8a', '#e83e8c'
                        ],
                        showScale: true
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    tooltips: {
                        enabled: true
                    }
                }
            });

            var topQuestions = <?php print_r(json_encode($questionCategoryCount)); ?>;
            console.log(topQuestions);
            var ctx = document.getElementById("question_tree_map_chart").getContext("2d");;
            var chart = window.chart = new Chart(ctx, {
                type: "treemap",
                data: {
                    datasets: [{
                        label: "Hits",
                        tree: topQuestions,
                        key: "hit",
                        groups: ['category'],
                        spacing: 0.5,
                        borderWidth: 1.5,
                        fontColor: "black",
                        backgroundColor: ['#3f6ad8', '#16aaff', '#fd7e14', '#006400', '#28a745', '#20c997',
                            '#ffc107', '#d92550', '#794c8a', '#e83e8c'
                        ],
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    tooltips: {
                        enabled: true
                    }
                }
            });

            var date = <?php print_r(json_encode($date)); ?>;
            var subscriberCount = <?php print_r(json_encode($subscriberCount)); ?>;
            console.log("date", date);
            console.log("total subscriber", subscriberCount);
            var lineChartCanvas = document.getElementById("assessment_submitted_graph").getContext("2d");
            assessment_submitted = new Chart(lineChartCanvas, {
                type: 'line',

                options: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        cursor: "pointer",
                        labels: {
                            usePointStyle: true,
                        },
                    },
                    tooltips: {
                        enabled: true,
                        intersect:true,
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
                                labelString: 'date'
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

                    labels: date,
                    datasets: [{
                        label: "Assessments",
                        data: subscriberCount,
                        fill: false,
                        lineTension: 0,
                        backgroundColor: "rgba(0,0,255)",
                        borderColor: "rgba(0,0,255)",
                        borderWidth: 2,
                        pointStyle:'circle',
                    }]
                },
            });
        </script>
    @endsection
