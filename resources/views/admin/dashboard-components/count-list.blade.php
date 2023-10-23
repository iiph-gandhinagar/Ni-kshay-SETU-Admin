<div class="row">
    <div class="col-md-6 col-xl-4 mb-4">
        <div class="card  widget-content top-count-card subscriber-card-color">
            <div class="widget-content-left mt-1">
                <div class="text-white count-font">Total Subscribers</div>
                <div class="count-total text-white" style="cursor: pointer;margin-bottom: 20px;"
                    onclick="getSubscriberBYDate()">
                    <span class="count count-total" id="subscriber"></span>
                </div>
                <div class="text-white count-font">Subscribers Enrolled Today</div>
                <div class="count-total text-white" style="cursor: pointer;" onclick="getSubscriberByState()"
                    >{{-- onclick='window.location="admin/subscribers?from_date=" + (moment(new Date()).format("YYYY-MM-DD"));' --}}
                    <span class="count count-total" id="subscriberEnrollToday"></span>
                </div>
            </div>
            <div class="widget-content-right text-white">
                {{-- <i class="fa fa-thin fa-users fa-6x"></i> --}}
                <img src="../../fluent_people-team-28-regular.svg" alt="users" class="icon-position" />
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4 mb-4">
        <div class="card  widget-content top-count-card enquiry-card-color">
            <div class="widget-content-left mt-1">
                <div class="text-white count-font">Todays' Visitors</div>
                <div class="count-total text-white" style="cursor: pointer;margin-bottom: 20px;"
                    onclick='window.location="admin/subscriber-activities?date=" + (moment(new Date()).format("DD/MM/YYYY"));'>
                    <span class="count count-total" id="subscriberAppVistCount"></span>
                </div>
                <div class="text-white count-font">Enquiries Today</div>
                <div class="count-total text-white" style="cursor: pointer;"
                    onclick='window.location="admin/enquiries?date=" + (moment(new Date()).format("DD/MM/YYYY"));'>
                    <span class="count count-total" id="enquiry"></span>
                </div>
            </div>
            <div class="widget-content-right text-white">
                {{-- <i class="fa fa-solid fa fa-id-badge fa-6x"></i> --}}
                <img src="../../mdi_account-box-multiple-outline.svg" alt="accounts" class="icon-position" />
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4 mb-4">
        <div class="card  widget-content top-count-card assessemnt-card-color">
            <div class="widget-content-left mt-2">
                <div class="text-white count-font">Completed Assessments</div>
                <div class="count-total text-white" style="cursor: pointer;margin-bottom: 20px;"
                    onclick="getAssessmentByDate()">
                    <span class="count count-total" id="assessment"></span>
                </div>
                <div class="text-white count-font">Today's Completed Assessments</div>
                <div class="count-total text-white" style="cursor: pointer;"
                    onclick='window.location="admin/user-assessments?date=" + ( moment(new Date()).format("DD/MM/YYYY"));'>
                    <span class="count count-total" id="completeAssessmentCount"></span>
                </div>
            </div>
            <div class="widget-content-right text-white">
                {{-- <i class="fa fa-thin fa-clipboard-list fa-6x"></i> --}}
                <img src="../../fluent_clipboard-task-add-24-regular.svg" alt="assessment" class="icon-position" />
            </div>
        </div>
    </div>
</div>

@push('graph-scripts')
    <script type="text/javascript">
        function updateCountList(subscriber, enrollCount, enquiry, appVisitCount, assessment, completeAssessment) {
            $('#subscriber').empty();
            $('#subscriberAppVistCount').empty();
            $('#enquiry').empty();
            $('#assessment').empty();
            $('#completeAssessmentCount').empty();
            $('#subscriberEnrollToday').empty();

            $('#subscriber').text(subscriber);
            $('#subscriberAppVistCount').text(appVisitCount);
            $('#enquiry').text(enquiry);
            $('#assessment').text(assessment);
            $('#completeAssessmentCount').text(completeAssessment);
            $('#subscriberEnrollToday').text(enrollCount);

            $('.count').each(function() {
                $(this).prop('Counter', 0).animate({
                    Counter: $(this).text()
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function(now) {
                        $(this).text(Math.ceil(now));
                    }
                });
            });
        }

        function getSubscriberBYDate() {
            var params = new window.URLSearchParams(window.location.search);
            var state_search = params.get('state_id');
            arr = params.get('date').split(' ');
            var district_search =$('#district').val();
            var block_search =$('#block').val();
            if(params.get('date') != '' && state_search != "IN" && district_search != 0 && block_search != 0){
                window.location = "admin/subscribers?from_date=" + arr[0] + "&to_date=" + arr[2] + "&state_id=" + state_search + "&district_id=" + district_search +"&block_id=" + block_search;
            }else if(params.get('date') != '' && state_search != "IN" && district_search != 0){
                window.location = "admin/subscribers?from_date=" + arr[0] + "&to_date=" + arr[2] + "&state_id=" + state_search + "&district_id=" + district_search;
            }
            else if(params.get('date') != '' && state_search != "IN"){
                window.location = "admin/subscribers?from_date=" + arr[0] + "&to_date=" + arr[2] + "&state_id=" + state_search;
            }
            else if(params.get('date') != ''){
                window.location = "admin/subscribers?from_date=" + arr[0] + "&to_date=" + arr[2];
            }
            else if(state_search != "IN" && district_search != 0 && block_search != 0){
                window.location = "admin/subscribers?state_id=" + state_search + "&district_id=" + district_search + "&block_id=" + block_search;
            }
            else if(state_search != "IN" && district_search != 0){
                window.location = "admin/subscribers?state_id=" + state_search + "&district_id=" + district_search;
            }
            else if(state_search != "IN"){
                window.location = "admin/subscribers?state_id=" + state_search;
            }
            else{
                window.location = "admin/subscribers";
            }
           
        }

        function getAssessmentByDate() {
            var params = new window.URLSearchParams(window.location.search);
            arr = params.get('date').split(' ');
            var state_search = params.get('state_id');
            var district_search =$('#district').val();
            var block_search =$('#block').val();


            if(params.get('date') != '' && state_search != "IN" && district_search != 0 && block_search != 0){
                window.location = "admin/user-assessments?from_date=" + arr[0] + "&to_date=" + arr[2] + "&state=" + state_search + "&district=" + district_search +"&block_id=" + block_search;
            }else if(params.get('date') != '' && state_search != "IN" && district_search != 0){
                window.location = "admin/user-assessments?from_date=" + arr[0] + "&to_date=" + arr[2] + "&state=" + state_search + "&district=" + district_search;
            }
            else if(params.get('date') != '' && state_search != "IN"){
                window.location = "admin/user-assessments?from_date=" + arr[0] + "&to_date=" + arr[2] + "&state=" + state_search;
            }else if(params.get('date') != ''){
                window.location = "admin/user-assessments?from_date=" + arr[0] + "&to_date=" + arr[2];
            }
            else if(state_search != "IN" && district_search != 0 && block_search != 0){
                window.location = "admin/user-assessments?state=" + state_search + "&district=" + district_search + "&block_id=" + block_search;
            }
            else if(state_search != "IN" && district_search != 0){
                window.location = "admin/user-assessments?state=" + state_search + "&district=" + district_search;
            }
            else if(state_search != "IN"){
                window.location = "admin/user-assessments?state=" + state_search;
            }
            else{
                window.location = "admin/user-assessments";
            }
        }

        function getSubscriberByState(){
            var params = new window.URLSearchParams(window.location.search);
            var state_search = params.get('state_id');
            var district_search =$('#district').val();
            var block_search =$('#block').val();
            if(state_search != "IN" && district_search != 0 && block_search != 0){
                window.location = "admin/subscribers?state_id=" + state_search + "&district_id=" + district_search + "&block_id=" + block_search + "&from_date=" + moment(new Date()).format("YYYY-MM-DD");
            }
            else if(state_search != "IN" && district_search != 0){
                window.location = "admin/subscribers?state_id=" + state_search + "&district_id=" + district_search + "&from_date=" + moment(new Date()).format("YYYY-MM-DD");
            }
            else if(state_search != "IN"){
                window.location = "admin/subscribers?state_id=" + state_search + "&from_date=" + moment(new Date()).format("YYYY-MM-DD");
            }
            else{
                window.location = "admin/subscribers?from_date=" + (moment(new Date()).format("YYYY-MM-DD"));
            }
        }
    </script>
@endpush
