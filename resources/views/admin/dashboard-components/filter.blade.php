<div class="row display-flex mb-2">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card height-100 filter-border">
            <div class="widget-chat-wrapper-outer row m-0 p-3 filter-border" style="padding-bottom: 0 !important">
                {{-- <div class="widget-chart-wrapper widget-chart-wrapper-lg opacity-10"> --}}
                <div class="col-12 row p-0 m-0 d-flex justify-content-center align-items-center">
                    <div class="col-11 row ">
                        <div class="col-md-6 col-lg-3 col-sm-6 pb-3">
                            @if (isset($state_id) && $state_id != 0)
                                <select class="form-control" id="state" name="state" placeholder="All States"
                                    onchange="getState()">
                                    {{-- <option value="0">Select State</option> --}}
                                    @foreach ($state as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $state_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->title }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select class="form-control" id="state" name="state" placeholder="All States"
                                    onchange="getState()">
                                    <option value="0">All States</option>
                                    @foreach ($state as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request()->state_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->title }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="col-md-6 col-lg-3 col-sm-6 pb-3">
                            <select class="form-control" id="district" name="district" placeholder="select District"
                                onchange="getDistrict()">
                            </select>
                        </div>

                        <div class="col-md-6 col-lg-3 col-sm-6 pb-3">
                            <select class="form-control" id="block" name="block" placeholder="select Block"
                                onchange="getBlock()">

                            </select>
                        </div>

                        <div class="col-md-6 col-lg-3 col-sm-6 pb-3">
                            <input id='date' type="text" autocomplete="off" class="daterangepicker-class form-control"
                                name="date" value="{{ $date }}" placeholder="Select date" />
                        </div>

                    </div>
                    <div class="col-sm-1 col-12 p-0 m-0 pb-3 d-flex justify-content-center align-items-center">
                        <a id="btn-loader" class="btn btn-icon-split "
                            style="font-size: 20px;color: white;background-color:#30AAB9;">
                            <span id="spinner"></span>
                        </a>
                    </div>
                </div>

                {{-- </div> --}}
            </div>
        </div>
    </div>
</div>
@push('graph-scripts')
    <script type="text/javascript">
        let district = document.getElementById("district").value;
        let block = document.getElementById("block").value;
        var state = document.getElementById("state").value;
        //change map name dynamic with state
        var state_val = $('#state option:selected').text();
        if (state_val == "All States") {
            $('#map-title').text(`INDIA MAP`);
            $('.map_subscriber-count').empty();
            $('.map-subscriber-count').text('National Subscribers:');
        } else {
            $('#map-title').text(`${state_val} MAP`);
            $('.map_subscriber-count').empty();
            $('.map-subscriber-count').text('State Subscribers:');
        }

        jQuery.when(
            jQuery.getJSON(
                `get-district-data?state_id=${state}`
            )
        ).done(function(json) {
            $('#district').append(` <option value="0">Select District</option>`);
            for (var i = 0; i < json.length; i++) {
                const a = json[i];
                $('#district').append(`<option value="${a.id}">${a.title}</option>`);
            }
        });


        function getDistrict() {
            state = document.getElementById("state").value;
            district = document.getElementById("district").value;
            $('#block').empty();
            jQuery.when(
                jQuery.getJSON(
                    `get-block-data?state_id=${state}&district=${district}`
                )
            ).done(function(json) {
                $('#block').append(` <option value="0">Select Block</option>`);
                for (var i = 0; i < json.length; i++) {
                    const a = json[i];
                    $('#block').append(`<option value="${a.id}">${a.title}</option>`);
                }
            });
        }

        function getBlock() {
            state = document.getElementById("state").value;
            district = document.getElementById("district").value;
            block = document.getElementById("block").value;
        }

        function getState() {
            state = document.getElementById("state").value;
            $('#block').empty();

            $('#district').empty();
            jQuery.when(
                jQuery.getJSON(
                    `get-district-data?state_id=${state}`
                )
            ).done(function(json) {
                $('#district').append(` <option value="0">Select District</option>`);
                for (var i = 0; i < json.length; i++) {
                    const a = json[i];
                    $('#district').append(`<option value="${a.id}">${a.title}</option>`);
                }
            });
        }

        $('.daterangepicker-class').daterangepicker({
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD',
            },
            // startDate: '2018-01-01',
            // endDate: moment()
            maxDate: new Date,
            autoUpdateInput: false,
        });

        //Setting date range picker date to blank
        $('.daterangepicker-class').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('.daterangepicker-class').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // $(window).on("load", filter());
        window.onload = function() {
            //window.location = window.location.href.split("?")[0];
            $('#btn-loader').html(`<span class="icon text-white-50"><i class="fa fa-arrow-right"></i>`);
            filter(); // 4th
        };

        //button loader
        $('#btn-loader').click(function(e) {
            e.preventDefault();
            $this = $(this);
            $this.prop('disabled', true).html(
                '<span class="spinner"><i class="fa fa-spinner" aria-hidden="true"></i></span>');
            filter(() => {
                $this.prop('disabled', false)
                    .html('<span class="icon text-white-50"><i class="fa fa-arrow-right"></i></span>');
                var state_val = $('#state option:selected').text();
                var district_val = $('#district option:selected').text();
                var block_val = $('#block option:selected').text();
                if (state_val == "All States") {
                    $('#map-title').text(`INDIA MAP`);
                    $('.map_subscriber-count').empty();
                    $('.map-subscriber-count').text('National Subscribers:');
                } else if (state_val != "All States" && district_val == 'Select District') {
                    $('#map-title').text(`${state_val} MAP`);
                    $('.map_subscriber-count').empty();
                    $('.map-subscriber-count').text('State Subscribers:');
                } else if (state_val != "All States" && district_val != 'Select District' && block_val ==
                    'Select Block') {
                    $('#map-title').text(`${state_val} MAP`);
                    $('.map_subscriber-count').empty();
                    $('.map-subscriber-count').text('District Subscribers:');
                } else {
                    $('#map-title').text(`${state_val} MAP`);
                    $('.map_subscriber-count').empty();
                    $('.map-subscriber-count').text('Block Subscribers:');
                }
            })

        });

        //filter data 
        function filter(callback) {
            var state = document.getElementById("state").value;
            var district = document.getElementById("district").value;
            var block = document.getElementById("block").value;

            var user_state = '<?php print_r($state_id); ?>';
            if (user_state) {
                $('#back-btn').hide();
            } else if (state > 0 ) {
                console.log("inside state not IN");
                $('#back-btn').html(
                    `<a href="./admin"><i class="fa fa-arrow-left back-icon" aria-hidden="true"></i></a>`
                );
            }
            // console.log("in filter state", state);

            var date = $("#date").val();
            if (date && date != '') {
                $('.assessment-month').text(`Summary (${date}) `);
            } else {
                $('.assessment-month').text("Summary (Last 12 Months) ");
            }
            let url = g1.url.parse(location.href);
            url.update({
                date: date
            })
            history.pushState({}, '', url.toString())
            // console.log('state',state);
            if (state != 0 && district == 0 && block == 0) {
                getState();
                let heading = 'DISTRICT WISE SUBSCRIBERS';
                jQuery.when(
                    jQuery.getJSON(
                        `get-district-block-data?date=${date}&state_id=${state}&district=${district}&block=${block}`
                    )
                ).done(function(json) {
                    // console.log("dist json", json['districtWiseSubscriber']);
                    updateStateDistData(heading, json['districtWiseSubscriber'],"DISTRICT");
                    callFunctions(json);
                    callback();
                });
                redrawMap(state, '1')

            } else if (state != 0 && district != 0 && block == 0) {
                // console.log("inside block health");
                let heading = 'BLOCK WISE SUBSCRIBERS';
                jQuery.when(
                    jQuery.getJSON(
                        `get-block-health-data?date=${date}&state_id=${state}&district=${district}&block=${block}`)
                ).done(function(json) {
                    // console.log("block json", json['blockWiseSubscriber']);
                    updateStateDistData(heading, json['blockWiseSubscriber'],"BLOCK");
                    callFunctions(json);

                    callback();
                });
                redrawMap(state, '1')
            } else if (state != 0 && district != 0 && block != 0) {
                // console.log("inside block health");
                let heading = 'HEALTH FACILITY WISE SUBSCRIBERS';
                jQuery.when(
                    jQuery.getJSON(
                        `get-health-data?date=${date}&state_id=${state}&district=${district}&block=${block}`)
                ).done(function(json) {
                    // console.log("block json", json);
                    updateStateDistData(heading, json['subscriberList'],"HEALTH FACILITY");
                    callFunctions(json);

                    callback();
                });
                redrawMap(state, '1')
            } else {
                //india level data
                let heading = 'STATE WISE SUBSCRIBERS';
                jQuery.when(
                    jQuery.getJSON(
                        `get-dashboard-data?date=${date}&state_id=${state}&district=${district}&block=${block}`)
                ).done(function(json) {
                    // console.log("state json", json['stateWiseSubscriber']);
                    updateStateDistData(heading, json['stateWiseSubscriber'],"STATE");
                    callFunctions(json);

                    callback();
                });
                redrawMap('IN', '0')
            }
        }

        function callFunctions(json) {
            updateModule(json['top10Modules']);
            updateChatQuestion(json['questionHit']);
            updateChatKeyword(json['keywordHit']);
            updateAssessment(json['asessmentGraph']);
            updateCadreWiseSubscriber(json['cadreWiseSubscriber']);
            updateMapBoxCount(json['state_level_subscriber']);
            updateCountList(json['subscriber'], json['subscriberEnrollToday'], json['enquiry'], json[
                'subscriberAppVistCount'], json['assessment'], json['completeAssessmentCount']);

        }

        //redraw map
        function redrawMap(stateId, districtView) {
            // getState();
            //call map related APIs for state
            let url = g1.url.parse(location.href);
            url.update({
                state_id: stateId,
                district_view: districtView
            })
            history.pushState({}, '', url.toString())
            // console.log("redraw map",stateId);
            redraw()
        }
    </script>
@endpush
