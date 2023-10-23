@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.user-app-version.actions.index'))

@section('body')

    <user-app-version-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/user-app-versions/overall-app-version') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.user-app-version.actions.index') }}
                        {{-- <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/user-app-versions/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.user-app-version.actions.create') }}</a> --}}
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="row justify-content-md-between col-md-12">
                                <!--col-md-12-->

                                <div class="col-md-3 form-group">

                                    <multiselect :searchable="true" v-model="form.current_plateform" id="current_plateform"
                                        name="current_plateform" placeholder="Select Current Plateform"
                                        :options="{{ $plateform }}"
                                        {{-- :custom-label="opt => {{ $state }}.find(x => x.id == opt).title" --}}
                                        open-direction="auto" :multiple="false"
                                        @input="filter('current_plateform', form.current_plateform);">
                                    </multiselect>
                                </div>
                                <div class="col-md-3 form-group">
                                    <multiselect :searchable="true" v-model="form.app_version" id="app_version"
                                        name="app_version" placeholder="Select Current Plateform"
                                        :options="{{ $app_version }}.map(type => type.app_version)"
                                        {{-- :custom-label="opt => {{ $state }}.find(x => x.id == opt).title" --}}
                                        open-direction="auto" :multiple="false"
                                        @input="filter('app_version', form.app_version);">
                                    </multiselect>

                                </div>
                                <div class="col-md-3 form-group"></div>
                                <div class="col-md-3 form-group"></div>
                            </div>
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

                                        <th is='sortable' :column="'id'">{{ trans('admin.user-app-version.columns.id') }}</th>
                                        <th is='sortable' :column="'user_id'">{{ trans('admin.user-app-version.columns.user_id') }}</th>
                                        <th is='sortable' :column="'app_version'">{{ trans('admin.user-app-version.columns.app_version') }}</th>
                                        <th is='sortable' :column="'current_plateform'">{{ trans('admin.user-app-version.columns.current_plateform') }}</th>
                                        <th is='sortable' :column="'has_ios'">{{ trans('admin.user-app-version.columns.has_ios') }}</th>
                                        <th is='sortable' :column="'has_android'">{{ trans('admin.user-app-version.columns.has_android') }}</th>
                                        <th is='sortable' :column="'has_web'">{{ trans('admin.user-app-version.columns.has_web') }}</th>
                                        <th is='sortable' :column="'created_at'">{{ trans('admin.user-app-version.columns.created_at') }}</th>

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="6">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/user-app-versions')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/user-app-versions/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">

                                        <td>@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</td>
                                        <td>@{{ item.user.name }}</td>
                                        <td>@{{ item.app_version }}</td>
                                        <td>@{{ item.current_plateform }}</td>
                                        <td>
                                            <span v-if="item.has_iphone" class="badge badge-success m-1 p-1" style="font-size: 0.8rem; color: white;"><i class="fa fa-check"></i></span>
                                            <span v-else class="badge badge-danger m-1 p-1" style="font-size: 0.8rem; color: white;"><i class="fa fa-times"></i></span>
                                        </td>
                                        <td>
                                            <span v-if="item.has_android" class="badge badge-success m-1 p-1" style="font-size: 0.8rem; color: white;"><i class="fa fa-check"></i></span>
                                            <span v-else class="badge badge-danger m-1 p-1" style="font-size: 0.8rem; color: white;"><i class="fa fa-times"></i></span>
                                        </td>
                                        <td>
                                            <span v-if="item.has_web" class="badge badge-success m-1 p-1" style="font-size: 0.8rem; color: white;"><i class="fa fa-check"></i></span>
                                            <span v-else class="badge badge-danger m-1 p-1" style="font-size: 0.8rem; color: white;"><i class="fa fa-times"></i></span>
                                        </td>
                                        <td>@{{ item.created_at | moment}}</td>
                                        <td></td>
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
                                {{-- <a class="btn btn-primary btn-spinner" href="{{ url('admin/user-app-versions/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.user-app-version.actions.create') }}</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </user-app-version-listing>

@endsection