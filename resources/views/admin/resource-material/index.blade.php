@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.resource-material.actions.index'))

@section('body')

    <resource-material-listing :data="{{ $data->toJson() }}" :all_cadres="{{ json_encode($all_cadres) }}"
        :all_states="{{ json_encode($state) }}"
        :url="'{{ url('admin/resource-materials?master=' . request()->query('master')) }}'"
        :session_search="'{{ $search }}'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.resource-material.actions.index') }}
                        @can('admin.resource-material.create')
                            <a v-if="{{ \Auth::user()->roles[0]['id'] }} != 10 && {{ \Auth::user()->roles[0]['id'] }} != 3"
                                class="btn btn-primary btn-spinner btn-sm pull-right m-b-0"
                                href="{{ url('admin/resource-materials/create') }}?master={{ app('request')->input('master') }}"
                                role="button"><i class="fa fa-plus"></i>&nbsp;
                                {{ trans('admin.resource-material.actions.create') }}</a>
                            <a v-else-if="({{ \Auth::user()->roles[0]['id'] }} == 3 && {{ app('request')->input('master') }} != 0) || ({{ \Auth::user()->roles[0]['id'] }} == 10 && {{ app('request')->input('master') }} != 0)"
                                class="btn btn-primary btn-spinner btn-sm pull-right m-b-0"
                                href="{{ url('admin/resource-materials/create') }}?master={{ app('request')->input('master') }}"
                                role="button"><i class="fa fa-plus"></i>&nbsp;
                                {{ trans('admin.resource-material.actions.create') }}</a>
                        @endcan
                        @if (app('request')->input('master') && app('request')->input('master') > 0)
                            <a class="btn btn-danger btn-spinner btn-sm pull-right m-b-0 mr-1" href="#"
                                onclick="window.history.go(-1)" role="button"><i class="fa fa-arrow-left"></i>&nbsp;
                                {{ trans('admin.resource-material.actions.back') }}</a>
                        @endif
                    </div>
                    @if (isset($message) && $message != '')
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert" @click="clearSession()">×</button>
                            <strong>{{ $message ?? '' }}</strong>
                        </div>
                    @endif
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <form @submit.prevent="">
                                <div class="row justify-content-md-between">
                                    <div class="col col-lg-7 col-xl-5 form-group">
                                        <div class="input-group">
                                            <input class="form-control"
                                                placeholder="{{ trans('brackets/admin-ui::admin.placeholder.search') }}"
                                                id="search_field" v-model="search"
                                                @keyup.enter="filter('search', $event.target.value)" />
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary"
                                                    @click="filter('search', search); getSerchFilter(1)"><i
                                                        class="fa fa-search"></i>&nbsp;
                                                    {{ trans('brackets/admin-ui::admin.btn.search') }}</button>
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
                                        <th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox"
                                                v-model="isClickedAll" v-validate="''" data-vv-name="enabled"
                                                name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>

                                        <th width="5%" is='sortable' data-title="id" :column="'id'">
                                            {{ trans('admin.resource-material.columns.id') }}</th>
                                        <th width="5%" is='sortable' data-title="type_of_materials"
                                            :column="'type_of_materials'">
                                            {{ trans('admin.resource-material.columns.type_of_materials') }}</th>
                                        <th width="25%" is='sortable'data-title="title" :column="'title'">
                                            {{ trans('admin.resource-material.columns.title') }}</th>
                                        <th width="10%" is='sortable'data-title="cadre" :column="'cadre'">
                                            {{ trans('admin.resource-material.columns.cadre') }}</th>
                                        <th width="10%" is='sortable'data-title="state" :column="'state'">
                                            {{ trans('admin.resource-material.columns.state') }}</th>
                                        <th width="5%" is='sortable' data-title="parent_id" :column="'parent_id'">
                                            {{ trans('admin.resource-material.columns.parent_id') }}</th>
                                        <th width="10%" is='sortable'data-title="created_at" :column="'created_at'">
                                            Created At</th>
                                        <th is='sortable' data-title="index" :column="'index'">
                                            {{ trans('admin.resource-material.columns.index') }}</th>
                                        <th width="10%" is='sortable' data-title="created_by" :column="'created_by'">
                                            {{ trans('admin.resource-material.columns.created_by') }}</th>

                                        <th width="10%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="13">
                                            <span
                                                class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }}
                                                @{{ clickedBulkItemsCount }}. <a href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAll('/admin/resource-materials')"
                                                    v-if="(clickedBulkItemsCount < pagination.state.total)"> <i
                                                        class="fa"
                                                        :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i>
                                                    {{ trans('brackets/admin-ui::admin.listing.check_all_items') }}
                                                    @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                    href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>
                                            </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3"
                                                    @click="bulkDelete('/admin/resource-materials/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id"
                                        :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                        <td class="bulk-checkbox">
                                            <input class="form-check-input" :id="'enabled' + item.id" type="checkbox"
                                                v-model="bulkItems[item.id]" v-validate="''"
                                                :data-vv-name="'enabled' + item.id"
                                                :name="'enabled' + item.id + '_fake_element'"
                                                @click="onBulkItemClicked(item.id)" :disabled="bulkCheckingAllLoader">
                                            <label class="form-check-label" :for="'enabled' + item.id">
                                            </label>
                                        </td>

                                        <td data-title="id">
                                            <span
                                                v-if="getId() ==(pagination.state.current_page -1) * pagination.state.per_page + index+1 "
                                                style="background-color: yellow">@{{ (pagination.state.current_page - 1) * pagination.state.per_page + index + 1 }}</span>
                                            <span v-else>@{{ (pagination.state.current_page - 1) * pagination.state.per_page + index + 1 }}</span>
                                        </td>
                                        <td data-title="type_of_materials">
                                            <span v-if="item.type_of_materials == 'folder' ">
                                                <i class="fa fa-folder-open-o" style="font-size: 1.9rem;"></i>
                                                {{-- <a :href="'/admin/resource-materials?master='+item.id" style="cursor: pointer;color:black;"><i class="fa fa-folder-open-o" style="font-size: 1.9rem;"></i></a> --}}
                                            </span>
                                            <span v-else-if="item.type_of_materials == 'pdfs' ">
                                                <i class="fa fa-file-pdf-o" style="font-size: 1.9rem;"></i>
                                            </span>
                                            <span v-else-if="item.type_of_materials == 'videos' ">
                                                <i class="fa fa-file-video-o" style="font-size: 1.9rem;"></i>
                                            </span>
                                            <span v-else-if="item.type_of_materials == 'ppt' ">
                                                <i class="fa fa-file-powerpoint-o" style="font-size: 1.9rem;"></i>
                                            </span>
                                            <span v-else-if="item.type_of_materials == 'document' ">
                                                <i class="fa fa-file-word-o" style="font-size: 1.9rem;"></i>
                                            </span>
                                            <span v-else-if="item.type_of_materials == 'images' ">
                                                <i class="fa fa-file-image-o" style="font-size: 1.9rem;"></i>
                                            </span>
                                            <span v-else-if="item.type_of_materials == 'pdf_office_orders' ">
                                                <i class="fa fa-file-pdf-o" style="font-size: 1.9rem;"></i>
                                            </span>
                                            <span v-else>
                                                @{{ item.type_of_materials }}
                                            </span>
                                        </td>
                                        <td data-title="title">
                                            <span v-if="item.type_of_materials == 'folder' ">
                                                <a
                                                    :href="'/admin/resource-materials?master=' + item.id">@{{ item.title }}</a>
                                            </span>
                                            <span v-else>
                                                <a
                                                    :href="'/media/' + item.media[0].id + '/' + item.media[0].file_name">@{{ item.title }}</a>
                                            </span>
                                        </td>
                                        {{-- <td>
                                            <span v-for="(cadre,i) in getCadreNamesByIds(item)">
                                                <span class="badge badge-warning m-1 p-1">@{{ cadre }}</span>
                                            </span>
                                        </td> --}}
                                        {{-- <td>
                                            <span v-if="all_cadres.length ===item.cadre.split(',').length" class="badge badge-warning m-1 p-1">ALL Cadre</span>
                                            <span v-else>
                                                <span v-for="(cadre,i) in getCadreNamesByIds(item)">
                                                    <span class="badge badge-warning m-1 p-1">@{{ cadre }}</span>
                                                </span>
                                            </span>
                                        </td> --}}
                                        <td data-title="cadre">
                                            <span v-if="all_cadres.length ===item.cadre.split(',').length"
                                                class="badge badge-danger m-1 p-1" style="font-size:0.8rem">All
                                                Cadres</span>
                                            <span v-else-if="item.cadre.length != 0">
                                                {{-- <span v-for="(cadre,i) in getCadreNamesByIds(item)">
                                                    <span class="badge badge-warning m-1 p-1">@{{ cadre }}</span>
                                                </span> --}}
                                                <span class="badge badge-warning m-1 p-1"
                                                    style="font-size:0.8rem;color:black">Selected Cadres</span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                        <td data-title="state">
                                            <span v-if="all_states.length ===item.state.split(',').length"
                                                class="badge badge-danger m-1 p-1" style="font-size:0.8rem">All
                                                States</span>
                                            <span v-else-if="item.state.length != 0">
                                                <span class="badge badge-warning m-1 p-1"
                                                    style="font-size:0.8rem;color:black">Selected States</span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                        {{-- <td>
                                            <span v-for="(state,j) in getStateNamesByIds(item)">
                                                <span class="badge badge-warning m-1 p-1">@{{ state }}</span>
                                            </span>
                                        </td> --}}
                                        <td data-title="parent_id">
                                            <span
                                                v-if="item.parent_master && item.parent_master != null">@{{ item.parent_master.title }}</span>
                                            <span v-else>
                                                Root
                                            </span>
                                        </td>
                                        <td data-title="created_at"> @{{ item.created_at | moment }}</td>
                                        <td data-title="index">@{{ item.index }}</td>
                                        <td data-title="created_by">@{{ item.user.first_name }} @{{ item.user.last_name }}</td>
                                        <td class="action_buttons">
                                            <div v-if="({{ \Auth::user()->roles[0]['id'] }} == 3 && item.user.roles[0].id == 3 || ({{ \Auth::user()->roles[0]['id'] }} == 10 && item.user.roles[0].id == 10) || {{ \Auth::user()->roles[0]['id'] }} != 3 && {{ \Auth::user()->roles[0]['id'] }} != 10)"
                                                class="row">
                                                <a class="btn btn-sm btn-dark"
                                                    :href="item.resource_url + '/send-initial-invitation'"
                                                    title="Send Initial Invitation" role="button"><i
                                                        class="fa fa-bell-o"></i></a>
                                                @can('admin.resource-material.edit')
                                                    <div class="col-auto">
                                                        <a class="btn btn-sm btn-spinner btn-info"
                                                            :href="item.resource_url + '/edit'"
                                                            @click="idStore((pagination.state.current_page -1) * pagination.state.per_page + index+1)"
                                                            title="{{ trans('brackets/admin-ui::admin.btn.edit') }}"
                                                            role="button"><i class="fa fa-edit"></i></a>
                                                    </div>
                                                @endcan
                                                @can('admin.resource-material.delete')
                                                    <form class="col-auto" @submit.prevent="deleteItem(item.resource_url)">
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i
                                                                class="fa fa-trash-o"></i></button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span
                                        class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                @can('admin.resource-material.create')
                                    <a class="btn btn-primary btn-spinner"
                                        href="{{ url('admin/resource-materials/create') }}?master={{ app('request')->input('master') }}"
                                        role="button"><i class="fa fa-plus"></i>&nbsp;
                                        {{ trans('admin.resource-material.actions.create') }}</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </resource-material-listing>

@endsection
@section('bottom-scripts')
    @include('admin.script-element-pagination')
@endsection
