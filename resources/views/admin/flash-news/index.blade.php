@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.flash-news.actions.index'))

@section('body')

    <flash-news-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/flash-news') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.flash-news.actions.index') }}
                        <a class="btn btn-primary btn-sm pull-right m-b-0 ml-2" :href="'/admin/flash-news/export?from_date=' + form.from_date + '&to_date=' + form.to_date" role="button"><i class="fa fa-file-excel-o"></i>&nbsp; {{ trans('admin.flash-news.actions.export') }}</a>
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/flash-news/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.flash-news.actions.create') }}</a>
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="row justify-content-md-between col-md-12">
                                <div class="col-md-3 form-group">
                                    <datetime v-model="form.from_date" class="flatpickr" id="from_date" name="from_date" placeholder="Select From Date" @input="filter('from_date', form.from_date)"></datetime>{{-- v-validate="'date_format:yyyy-MM-dd h:mm:ss'"  --}}
                                </div>

                                <div class="col-md-3 form-group">
                                    <datetime v-model="form.to_date" class="flatpickr" value="form.to_date" id="to_date" name="to_date" placeholder="Select To date" @input="filter('to_date', form.to_date)"></datetime>{{-- v-validate="'date_format:yyyy-MM-dd h:mm:ss'"  --}}
                                </div>
                                <div class="col-md-3 form-group"></div>
                                <div class="col-md-3 form-group"></div>
                            <div>
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

                            <table class="table table-hover table-listing">
                                <thead>
                                    <tr>
                                        <th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>

                                        <th is='sortable' width="5%" data-title="id" :column="'id'" id="id1">{{ trans('admin.flash-news.columns.id') }}</th>
                                        <th is='sortable' width="25%" data-title="title" :column="'title'">{{ trans('admin.flash-news.columns.title') }}</th>
                                        {{-- <th is='sortable' :column="'description'">{{ trans('admin.flash-news.columns.description') }}</th> --}}
                                        <th is='sortable' width="10%" data-title="source" :column="'source'">{{ trans('admin.flash-news.columns.source') }}</th>
                                        <th is='sortable' width="20%" data-title="href" :column="'href'">{{ trans('admin.flash-news.columns.href') }}</th>
                                        {{-- <th is='sortable' :column="'author'">{{ trans('admin.flash-news.columns.author') }}</th> --}}
                                        {{-- <th is='sortable' width="10%" :column="'publish_date'">{{ trans('admin.flash-news.columns.publish_date') }}</th> --}}
                                        <th is='sortable' width="5%" data-title="order_index" :column="'order_index'">{{ trans('admin.flash-news.columns.order_index') }}</th>
                                        <th is='sortable' width="5%" data-title="active" :column="'active'">{{ trans('admin.flash-news.columns.active') }}</th>
                                        <th is='sortable' width="15%" data-title="created_at" :column="'created_at'">{{ trans('admin.flash-news.columns.created_at') }}</th>

                                        <th width="15%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="10">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/flash-news')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/flash-news/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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

                                        <td data-title="id">
                                            <span v-if="getId() == index + 1 " style="background-color: yellow">@{{  index + 1 }}</span>
                                            <span v-else>@{{  index + 1}}</span>
                                        </td>
                                        <td data-title="title">@{{ item.title }}</td>
                                        {{-- <td>@{{ item.description | stripHTML }}</td> --}}
                                        <td data-title="source"><div :title=stripHTML(item.source)><a :href=item.source>@{{ item.source | stringCount | stripHTML }}</a></div></td>
                                        <td data-title="href"><div :title=stripHTML(item.source)><a :href=item.href><a :href=item.href>@{{ item.href  | stringCount | stripHTML}}</a></div></td>
                                        {{-- <td>@{{ item.author}}</td> --}}
                                        {{-- <td>@{{ item.publish_date }}</td> --}}
                                        <td data-title="order_index">@{{ item.order_index }}</td>
                                        <td data-title="active" >
                                            <label class="switch switch-3d switch-danger">
                                                <input type="checkbox" class="switch-input" v-model="collection[index].active" @change="toggleSwitch(item.resource_url + '/activeflag', 'active', collection[index])">
                                                <span class="switch-slider"></span>
                                            </label>
                                        </td>
                                        <td data-title="created_at">@{{ item.created_at | moment }}</td>

                                        {{-- <td>@{{ item.active }}</td> --}}
                                        
                                        <td>
                                            <div class="row no-gutters">
                                                <div class="col-auto">
                                                    <a class="btn btn-sm btn-spinner btn-info" :href="item.resource_url + '/edit'" @click="idStore((pagination.state.current_page -1) * pagination.state.per_page + index+1)" title="{{ trans('brackets/admin-ui::admin.btn.edit') }}" role="button"><i class="fa fa-edit"></i></a>
                                                </div>
                                                <form class="col" @submit.prevent="deleteItem(item.resource_url)">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i class="fa fa-trash-o"></i></button>
                                                </form>
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
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/flash-news/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.flash-news.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </flash-news-listing>

@endsection
@section('bottom-scripts')
    @include('admin.script-element-pagination')
@endsection