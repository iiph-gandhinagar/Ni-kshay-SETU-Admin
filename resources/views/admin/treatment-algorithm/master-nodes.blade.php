@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.treatment-algorithm.actions.index'))

@section('body')

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> {{ trans('admin.treatment-algorithm.actions.index') }}
                @can('admin.treatment-algorithm.create')
                    <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0 ml-2" href="{{ url('admin/treatment-algorithms?master=0&master_node_id=0') }}" role="button"><i class="fa fa-plus"></i>&nbsp; Add / Edit Master Nodes</a>
                @endcan
            </div>
            <div class="card-body row col-md-12" v-cloak>
                
                    @foreach ($data as $item)
                    <div class="col-md-3">&nbsp;</div>
                        <div class="card col-md-5" style="border: 2px solid #30AAB9;">
                            <div class="card-body row">
                                <div class="col-md-10">
                                    <h4 style="color: black;">{{$item->title}}</h4>
                                </div>
                                <div class="col-md-2">
                                    @if(isset($item) && isset($item->media[0]))
                                        <img width="54" height="54" src="/media/{{$item->media[0]->id}}/{{$item->media[0]->file_name}}"/>
                                    @endif
                                </div>
                            </div>
                            <div class="p-1">
                                <span><a target="_blank" href="/admin/treatment-algorithms/org-chart?master={{$item->id}}" class="btn btn-primary">Graph View<i class="p-1 fa fa-sitemap" style="font-size: 1rem;" title="Graph View"></i></a></span>
                                @if($item->node_type == 'Linking Node' || $item->node_type == 'Linking Node Without Options')
                                    <span><a href="/admin/treatment-algorithms?master={{$item->id}}&master_node_id={{$item->id}}" class="btn btn-primary">List View<i class="p-1 fa fa-list" style="font-size: 1rem;" title="List View"></i></a></span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">&nbsp;</div>
                    @endforeach
                
            </div>
        </div>
    </div>
</div>

@endsection