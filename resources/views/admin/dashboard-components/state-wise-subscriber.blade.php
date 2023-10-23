<div class="row display-flex mb-2">
    <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-12">
        <div class="card cus-border-top height-100 custom-card-border" style="cursor: pointer;">
            {{-- <div class=" border-4 mb-3"></div> --}}
            <h4 class="heading header mt-3"></h4>
            <div class="table-listing-design scrollbar">
                <table class="table table-hover sortable" id="states-data">
                    <thead id="subscriber-listing">
                        <tr style="border-bottom: 2.5px solid #30a9b880; border-top:1.5px solid #30a9b880;">
                            <th width="50%" class="header_title">STATE</th>
                            <th width="50%" style="text-align: center">TOTAL COUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('admin.dashboard-components.india-map')
</div>

@push('graph-scripts')
    <script type="text/javascript">
        function updateStateDistData(heading, data,title) {
            $('.header').text(heading);
            $('.header_title').text(title);
            $("#states-data tbody").empty();
            $('#subscriber-listing').hide();
            if (data.length == 0) {
                $('#states-data tbody').prepend('<img id="theImg" src="./Group 86.svg" style="display:flex;margin: auto;opacity:0.3"/>');
            } else {
            $('#subscriber-listing').show();

                for (var i = 0; i < data.length; i++) {
                    const a = data[i];
                    
                    $("#states-data tbody").append(`
                    <tr style='height:50px;border-bottom: 1.5px solid #30a9b880;'>
                        <td>${a.title}</td>
                        <td style="text-align: center;">${a.TotalCount}</td>
                    </tr>
            `);
                }

            }
        }
        
    </script>
@endpush
