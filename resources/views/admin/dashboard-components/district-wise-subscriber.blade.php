<div class="row">
    <div class="col-md-5">
        <div class="main-card mb-3 card min-height" style="cursor: pointer;">
            <div class="widget-chat-wrapper-outer mb-3"></div>
            <h4 class="heading">District WISE SUBSCRIBERS</h4><br>
            <div class="table-responsive">
                <table class="align-middle mb-0 table table-hover" id="districts-data">
                    <tbody>
                        {{-- @foreach ($districtWiseSubscriber as $item)
                            <tr style="height:50px">
                                <td>{{ $item->DistrictName }}</td>
                                <td>{{ $item->TotalCount }}</td>
                            </tr>
                        @endforeach --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('admin.dashboard-components.india-map')
</div>

@push('graph-scripts')
    <script type="text/javascript">
        
    </script>
@endpush