@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Dashboard')

<link href="{{ asset('css/custom-dashboard.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/fontawesome.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
<link href="{{ asset('css/daterangepicker.css') }}" rel="stylesheet">
<link href="{{ asset('css/ion.rangeSlider.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/bootstraptheme.css') }}" rel="stylesheet">

@section('body')
    <link href="{{ asset('css/dashboard-template.css') }}" rel="stylesheet">

    <div class="row display-flex">
        <a id="button"></a>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify" style="color:#30AAB9"></i> <a style="color:#30AAB9">Dashboard</a>
                </div>
                <div class="card-body p-0" v-cloak>
                    <div class="card-block">
                        @include('admin.dashboard-components.filter', [
                            'state_id' => $state_id,
                            'date' => $date,
                        ])

                        @include('admin.dashboard-components.count-list')

                        @include('admin.dashboard-components.state-wise-subscriber')

                        @include('admin.dashboard-components.module-usage', [
                            'state_id' => $state_id,
                        ])

                        @include('admin.dashboard-components.chat-keyword-hits', [
                            'state_id' => $state_id,
                        ])

                        @include('admin.dashboard-components.assessment-submit')

                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
@section('bottom-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="{{ asset('js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}" type="text/javascript"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-treemap@0.2.3"></script> --}}
    <script src="https://browser.sentry-cdn.com/5.6.3/bundle.min.js"
        integrity="sha384-/Cqa/8kaWn7emdqIBLk3AkFMAHBk0LObErtMhO+hr52CntkaurEnihPmqYj3uJho" crossorigin="anonymous">
    </script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="https://d3js.org/d3.v3.min.js"></script>
    <script src="http://d3js.org/topojson.v1.min.js"></script>
    <script src="{{ asset('js/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('js/lodash.min.js') }}"></script>
    <script src="{{ asset('js/g1.min.js') }}"></script>
    <script src="{{ asset('js/ion.rangeSlider.min.js') }}"></script>
    <script src="{{ asset('js/d3.min.js') }}"></script>
    <script src="{{ asset('js/d3-scale-chromatic.min.js') }}"></script>
    <script src="{{ asset('js/vega.min.js') }}"></script>
    <script src="{{ asset('js/topojson.min.js') }}"></script>
    <script src="https://use.fontawesome.com/15439642c4.js"></script>
    <script src="{{ asset('js/index.js') }}"></script>
    <script src="{{ asset('js/d3-color.min.js') }}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/d3-annotation/2.3.2/d3-annotation.js">
    </script>
    <script src="{{ asset('js/vega-tooltip.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mathjs/6.2.2/math.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/2.9.3/intro.min.js"></script>
    <script src="{{ asset('js/map.js') }}"></script>
    <script src="{{ asset('js/helper.js') }}"></script>
    <script src="{{ asset('js/matrix_modal.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>

    <script src="{{ asset('js/dashboard.js') }}" type="text/javascript"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-style@0.5.0"></script>
    <script src="{{ asset('js/sortable.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        var btn = $('#button');

        $(window).scroll(function() {
            if ($(window).scrollTop() > 300) {
                btn.addClass('show');
            } else {
                btn.removeClass('show');
            }
        });

        btn.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, '300');
        });
    </script>
@endsection
