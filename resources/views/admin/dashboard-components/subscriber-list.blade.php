<div class="row">
    <div class="col-md-5">
        <div class="main-card mb-3 card min-height" style="cursor: pointer;">
            <div class="widget-chat-wrapper-outer mb-3"></div>
            <h4 class="heading">Health Facility WISE SUBSCRIBERS</h4><br>
            <div class="table-responsive">
                <table class="align-middle mb-0 table table-hover">
                    <tbody>
                        @foreach ($subscriberList as $item)
                            <tr style="height:50px">
                                <td>{{ $item->HealthFacilityName }}</td>
                                <td>{{ $item->TotalCount }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('admin.dashboard-components.india-map')
</div>