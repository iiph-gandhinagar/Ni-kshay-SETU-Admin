@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.assessment.actions.index'))

@section('body')

    <assessment-listing :data="{{ $data->toJson() }}" :all_cadres="{{ json_encode($all_cadres) }}"
        :all_states="{{ json_encode($state) }}" :all_districts="{{ json_encode($districts) }}"
        :url="'{{ url('admin/assessments') }}'" :session_search="'{{ $search }}'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.assessment.actions.index') }}
                        @can('admin.assessment.create')
                            <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0"
                                href="{{ url('admin/assessments/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp;
                                {{ trans('admin.assessment.actions.create') }}</a>
                        @endcan
                    </div>
                    @if (isset($message) && $message != '')
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert" @click="clearSession()">×</button>
                            <strong>{{ $message ?? '' }}</strong>
                        </div>
                    @endif
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="col-md-3 form-group ">

                                <multiselect :searchable="true" v-model="form.select_cadre" {{-- id="cadre_id[]" name="cadre_id[]" --}}
                                    placeholder="Select Cadre" :options="{{ $cadre }}.map(type => type.id)"
                                    :custom-label="opt => {{ $cadre }}.find(x => x.id == opt).title"
                                    open-direction="auto" :multiple="true"
                                    @input="filter('cadre_id', form.select_cadre)">
                                </multiselect>
                            </div>

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
                                        <th width="5%" class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox"
                                                v-model="isClickedAll" v-validate="''" data-vv-name="enabled"
                                                name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>

                                        <th width="2%" is='sortable' data-title="id" :column="'id'"
                                            id="id1">
                                            {{ trans('admin.assessment.columns.id') }}</th>
                                        <th width="20%" is='sortable' data-title="assessment_title"
                                            :column="'assessment_title'">
                                            {{ trans('admin.assessment.columns.assessment_title') }}</th>
                                        <th width="5%" is='sortable' data-title="time_to_complete"
                                            :column="'time_to_complete'">
                                            {{ trans('admin.assessment.columns.time_to_complete') }}</th>
                                        <th width="5%" is='sortable' data-title="cadre_id" :column="'cadre_id'">
                                            {{ trans('admin.assessment.columns.cadre_id') }}</th>
                                        <th width="10%"
                                            v-if="{{ \Auth::user()->roles[0]['id'] }} != 3 && {{ \Auth::user()->roles[0]['id'] }} != 4"
                                            is='sortable' data-title="state_id" :column="'state_id'">
                                            {{ trans('admin.assessment.columns.state_id') }}</th>
                                        <th width="10%"
                                            v-if="{{ \Auth::user()->roles[0]['id'] }} == 3 || {{ \Auth::user()->roles[0]['id'] }} == 4"
                                            is='sortable' data-title="district_id" :column="'district_id'">
                                            {{ trans('admin.assessment.columns.district_id') }}</th>
                                        {{-- <th width="10%" is='sortable' :column="'assessment_json'">{{ trans('admin.assessment.columns.assessment_json') }}</th> --}}
                                        {{-- <th width="10%" is='sortable' :column="'district_id'">{{ trans('admin.assessment.columns.district_id') }}</th> --}}
                                        {{-- <th width="5%" is='sortable' :column="'cadre_type'">{{ trans('admin.assessment.columns.cadre_type') }}</th> --}}
                                        <th width="5%" is="sortable" data-title="Assessment"
                                            :column="'Assessment Questions'">Total Questions
                                        </th>
                                        <th width="8%" is='sortable' data-title="created_by" :column="'created_by'">
                                            {{ trans('admin.assessment.columns.created_by') }}</th>
                                        <th width="5%" is='sortable' data-title="activated" :column="'activated'">
                                            {{ trans('admin.assessment.columns.activated') }}</th>

                                        <th width="30%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="9">
                                            <span
                                                class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }}
                                                @{{ clickedBulkItemsCount }}. <a href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAll('/admin/assessments')"
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
                                                    @click="bulkDelete('/admin/assessments/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                        <td data-title="assessment_title">@{{ item.assessment_title }}</td>
                                        <td data-title="time_to_complete">@{{ item.time_to_complete }}</td>
                                        <td data-title="cadre_id">
                                            <span v-if="all_cadres.length === item.cadre_id.split(',').length"
                                                class="badge badge-danger m-1 p-1" style="font-size:0.8rem">All
                                                Cadres</span>
                                            <span v-else-if="item.cadre_id.length != 0">
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
                                        <td data-title="state_id"
                                            v-if="{{ \Auth::user()->roles[0]['id'] }} != 3 && {{ \Auth::user()->roles[0]['id'] }} != 4">
                                            <span v-if="all_states.length === item.state_id.split(',').length"
                                                class="badge badge-danger m-1 p-1" style="font-size:0.8rem">All
                                                States</span>
                                            <span v-else-if="item.state_id.split(',').length != 0">
                                                {{-- <span v-for="(state,j) in getStateNamesByIds(item)">
                                                    <span class="badge badge-info m-1 p-1" style="color:white">@{{ state }}</span>
                                                </span> --}}
                                                <span class="badge badge-warning m-1 p-1"
                                                    style="font-size:0.8rem;color:black">Selected States</span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                        <td data-title="district_id"
                                            v-if="{{ \Auth::user()->roles[0]['id'] }} == 3 || {{ \Auth::user()->roles[0]['id'] }} == 4">
                                            <span
                                                v-if="item.district_id == '' || item.district_id == null || item.district_id.length > 0 && all_districts.length === item.district_id.split(',').length"
                                                class="badge badge-danger m-1 p-1" style="font-size:0.8rem">All
                                                Districts</span>
                                            <span v-else-if="item.district_id.split(',').length != 0">
                                                <span class="badge badge-warning m-1 p-1"
                                                    style="font-size:0.8rem;color:black">Selected Districts</span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                        {{-- <td>@{{ item.assessment_json }}</td> --}}
                                        {{-- <td>@{{ item.district_id }}</td> --}}
                                        {{-- <td>@{{ item.cadre_type }}</td> --}}
                                        <td data-title="Assessment">@{{ item.assessment_questions.length }}</td>
                                        <td data-title="created_by">@{{ item.user_with_trashed.first_name }} @{{ item.user_with_trashed.last_name }}</td>
                                        <td data-title="activated">
                                            <div v-if="item.from_date <= format_date(item.from_date)">
                                                <label class="switch switch-3d switch-danger">
                                                    <input type="checkbox" disabled class="switch-input"
                                                        v-model="collection[index].activated"
                                                        @change="toggleSwitch(item.resource_url + '/activeflag', 'active', collection[index])">
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                            <div v-else>
                                                <label class="switch switch-3d switch-danger">
                                                    <input type="checkbox" class="switch-input"
                                                        v-model="collection[index].activated"
                                                        @change="toggleSwitch(item.resource_url + '/activeflag', 'active', collection[index])">
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="action_buttons">
                                            <div v-if="({{ \Auth::user()->roles[0]['id'] }} == 3 && item.user_with_trashed.roles[0].id == 3 || {{ \Auth::user()->roles[0]['id'] }} != 3) || {{ \Auth::user()->roles[0]['id'] }} == 10 && item.user_with_trashed.roles[0].id == 10 || {{ \Auth::user()->roles[0]['id'] }} != 10"
                                                class="row">

                                                @can('admin.assessment.assessment-question')
                                                    <div class="col-auto">
                                                        <a v-if="item.initial_invitation == 1 || item.activated == 0 || item.from_date <= format_date(item.from_date)"
                                                            class="btn btn-sm btn-dark disabled"
                                                            title="Send Initial Invitation" role="button"><i
                                                                class="fa fa-bell-o"></i></a>
                                                        <a v-else class="btn btn-sm btn-dark"
                                                            :href="item.resource_url + '/send-initial-invitation'"
                                                            title="Send Initial Invitation" role="button"><i
                                                                class="fa fa-bell-o"></i></a>
                                                    </div>
                                                @endcan

                                                @can('admin.assessment.assessment-question')
                                                    <div class="col-auto">
                                                        <a class="btn btn-sm btn-danger"
                                                            :href="item.resource_url + '/assessment-question'"
                                                            title="Assessment Question Report" role="button"><i
                                                                class="fa fa-floppy-o"></i></a>
                                                    </div>
                                                @endcan
                                                @can('admin.assessment.report')
                                                    <div class="col-auto">
                                                        <a class="btn btn-sm btn-success"
                                                            :href="item.resource_url + '/report'"
                                                            title="Assessment Result Report" role="button"><i
                                                                class="fa fa-download"></i></a>
                                                    </div>
                                                @endcan
                                                @can('admin.assessment.copy')
                                                    <form class="col" @click="copyItem(item.resource_url + '/copy')"
                                                        onsubmit="return false">
                                                        {{-- <a class="btn btn-sm btn-spinner btn-warning" :href="item.resource_url + '/copy'" title="{{ trans('brackets/admin-ui::admin.btn.copy') }}" role="button"><i class="fa fa-copy"></i></a>onclick="return confirm('Are you sure you want to Copy this Assessment?');" --}}
                                                        <button type="submit" class="btn btn-sm btn-spinner btn-warning"
                                                            title="Copy"><i class="fa fa-copy"></i></button>
                                                    </form>
                                                @endcan
                                                @can('admin.assessment.edit')
                                                    <div v-if="item.from_date <= format_date(item.from_date)">
                                                        <div v-if="item.activated == 1" class="col-auto">
                                                            <a class="btn btn-sm  btn-info disabled" aria-disabled="true"
                                                                title="{{ trans('brackets/admin-ui::admin.btn.edit') }}"
                                                                role="button"><i class="fa fa-edit"></i></a>
                                                        </div>
                                                        <div v-else class="col-auto">
                                                            <a class="btn btn-sm  btn-info"
                                                                :href="item.resource_url + '/edit'"
                                                                title="{{ trans('brackets/admin-ui::admin.btn.edit') }}"
                                                                role="button"><i class="fa fa-edit"></i></a>
                                                        </div>
                                                    </div>
                                                    <div v-else class="col-auto">
                                                        <div v-if="item.activated == 1" class="col-auto">
                                                            <a class="btn btn-sm  btn-info disabled" aria-disabled="true"
                                                                title="{{ trans('brackets/admin-ui::admin.btn.edit') }}"
                                                                role="button"><i class="fa fa-edit"></i></a>
                                                        </div>
                                                        <div v-else class="col-auto">
                                                            <a class="btn btn-sm btn-info" :href="item.resource_url + '/edit'"
                                                                @click="idStore((pagination.state.current_page -1) * pagination.state.per_page + index+1)"
                                                                title="{{ trans('brackets/admin-ui::admin.btn.edit') }}"
                                                                role="button"><i class="fa fa-edit"></i></a>
                                                        </div>
                                                    </div>
                                                @endcan
                                                @can('admin.assessment.delete')
                                                    <form class="col" @submit.prevent="deleteItem(item.resource_url)">
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
                                @can('admin.assessment.create')
                                    <a class="btn btn-primary btn-spinner" href="{{ url('admin/assessments/create') }}"
                                        role="button"><i class="fa fa-plus"></i>&nbsp;
                                        {{ trans('admin.assessment.actions.create') }}</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </assessment-listing>

@endsection
@section('bottom-scripts')
    @include('admin.script-element-pagination')
@endsection
