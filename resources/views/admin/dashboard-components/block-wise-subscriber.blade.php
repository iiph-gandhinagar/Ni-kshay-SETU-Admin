<div class="row">
    <div class="col-md-5">
        <div class="main-card mb-3 card min-height" style="cursor: pointer;">
            <div class="widget-chart widget-chart2">
                <div class="widget-chat-wrapper-outer mb-3"></div>
                <h4 class="heading">BLOCK WISE SUBSCRIBERS</h4><br>
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-hover">
                        <tbody>
                            @foreach ($blockWiseSubscriber as $item)
                                <tr style="height:50px">
                                    <td>{{ $item->blockName }}</td>
                                    <td>{{ $item->TotalCount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('admin.dashboard-components.india-map')
</div>
