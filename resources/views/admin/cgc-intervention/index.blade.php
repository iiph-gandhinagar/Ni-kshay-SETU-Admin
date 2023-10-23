@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.cgc-intervention.actions.index'))

@section('body')

    <cgc-intervention-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/cgc-interventions') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.cgc-intervention.actions.index') }}
                        @can('admin.cgc-intervention.create')
                            <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/cgc-interventions/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.cgc-intervention.actions.create') }}</a>
                        @endcan
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <form @submit.prevent="">
                                <div class="row justify-content-md-between">
                                    <div class="col col-lg-7 col-xl-5 form-group">
                                        <div class="input-group">
                                            <input class="form-control" placeholder="{{ trans('brackets/admin-ui::admin.placeholder.search') }}" v-model="search" @keyup.enter="filter('search', $event.target.value)" />
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary" @click="filter('search', search)"><i class="fa fa-search"></i>&nbsp; {{ trans('brackets/admin-ui::admin.btn.search') }}</button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-auto form-group ">
                                        <select class="form-control" v-model="pagination.state.per_page">
                                            
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-hover ">
                                <thead>
                                    <tr>
                                        <th width="5%" class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>

                                        <th width="5%" is='sortable'  data-title="id" :column="'id'">{{ trans('admin.cgc-intervention.columns.id') }}</th>
                                        <th width="15%" is='sortable' data-title="chapter_title"  :column="'chapter_title'">{{ trans('admin.cgc-intervention.columns.chapter_title') }}</th>
                                        <th width="15%" is='sortable' data-title="video_title"  :column="'video_title'">{{ trans('admin.cgc-intervention.columns.video_title') }}</th>
                                        <th width="35%" is='sortable' data-title="description"  :column="'description'">{{ trans('admin.cgc-intervention.columns.description') }}</th>
                                        <th width="15%" is='sortable' data-title="reference_title"  :column="'reference_title'">{{ trans('admin.cgc-intervention.columns.reference_title') }}</th>

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td wdith="10%" class="bg-bulk-info d-table-cell text-center" colspan="6">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/cgc-interventions')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/cgc-interventions/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                        <td class="bulk-checkbox">
                                            <input class="form-check-input" :id="'enabled' + item.id" type="checkbox" v-model="bulkItems[item.id]" v-validate="''" :data-vv-name="'enabled' + item.id"  :name="'enabled' + item.id + '_fake_element'" @click="onBulkItemClicked(item.id)" :disabled="bulkCheckingAllLoader">
                                            <label class="form-check-label" :for="'enabled' + item.id">
                                            </label>
                                        </td>

                                        <td data-title="id" >@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</td>
                                        <td data-title="chapter_title" >@{{ item.chapter_title }}</td>
                                        <td data-title="video_title" >@{{ item.video_title }}</td>
                                        <td data-title="description" >@{{ item.description }}</td>
                                        <td data-title="reference_title" >@{{ item.reference_title }}</td>
                                        
                                        <td>
                                            <div class="row no-gutters">
                                                @can('admin.cgc-intervention.delete')
                                                    <div class="col-auto">
                                                        <a class="btn btn-sm btn-spinner btn-info" :href="item.resource_url + '/edit'" title="{{ trans('brackets/admin-ui::admin.btn.edit') }}" role="button"><i class="fa fa-edit"></i></a>
                                                    </div>
                                                @endcan
                                                @can('admin.cgc-intervention.delete')
                                                    <form class="col" @submit.prevent="deleteItem(item.resource_url)">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i class="fa fa-trash-o"></i></button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                @can('admin.cgc-intervention.create')
                                    <a class="btn btn-primary btn-spinner" href="{{ url('admin/cgc-interventions/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.cgc-intervention.actions.create') }}</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </cgc-intervention-listing>

@endsection