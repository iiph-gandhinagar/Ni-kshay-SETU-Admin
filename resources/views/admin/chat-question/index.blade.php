@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.chat-question.actions.index'))

@section('body')

    <chat-question-listing :data="{{ $data->toJson() }}" :all_cadres="{{ json_encode($all_cadres) }}"
        :session_search="'{{ $search }}'" :url="'{{ url('admin/chat-questions') }}'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.chat-question.actions.index') }}
                        @can('admin.chat-question.create')
                            <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0 ml-2"
                                href="{{ url('admin/chat-questions/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp;
                                {{ trans('admin.chat-question.actions.create') }}</a>
                        @endcan
                        @can('admin.chat-question.export')
                            <a class="btn btn-primary btn-sm pull-right m-b-0 ml-2"
                                href="{{ url('admin/chat-questions/export') }}" role="button"><i
                                    class="fa fa-file-excel-o"></i>&nbsp; {{ trans('admin.chat-question.actions.export') }}</a>
                        @endcan
                        {{-- <a class="btn btn-primary btn-sm pull-right m-b-0 ml-2"
                            href="{{ url('admin/chat-questions/export-marathi') }}" role="button"><i
                                class="fa fa-file-excel-o"></i>&nbsp; Export Marathi</a> --}}
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="row justify-content-md-between col-md-12">
                                <div class="col-md-3 form-group ">
                                    <select class="form-control" id="category" name="category"
                                        v-model="form.select_category" @change="filter('category', form.select_category)">
                                        <option value="">Select Category</option>
                                        @foreach ($category as $item)
                                            <option value="{{ $item->category }}">{{ $item->category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 form-group ">
                                    <multiselect :searchable="true" v-model="form.select_keyword" id="keyword"
                                        name="keyword" placeholder="Select keyword"
                                        :options="{{ $keyword }}.map(type => type.id)"
                                        :custom-label="opt => {{ $keyword }}.find(x => x.id == opt).title"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('keyword', form.select_keyword)">
                                    </multiselect>
                                    {{-- <select class="form-control" id="keyword" name="keyword"
                                        v-model="form.select_keyword"
                                        @change="filter('keyword', form.select_keyword)">
                                        <option value="">Select Keyword</option>
                                        @foreach ($keyword as $item)
                                            <option value="{{ $item->id }}">{{ $item->title }}</option>
                                        @endforeach
                                    </select> --}}
                                </div>
                                <div class="col-md-3"></div>
                                <div class="col-md-3"></div>
                            </div>
                            <form @submit.prevent="">
                                <div class="row justify-content-md-between">
                                    <div class="col col-lg-7 col-xl-5 form-group">
                                        <div class="input-group">
                                            <input class="form-control" id="search_field"
                                                placeholder="{{ trans('brackets/admin-ui::admin.placeholder.search') }}"
                                                v-model="search" @keyup.enter="filter('search', $event.target.value)" />
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

                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span
                                        class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <table class="table table-hover ">
                                <thead>
                                    <tr>
                                        <th width="2%" class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox"
                                                v-model="isClickedAll" v-validate="''" data-vv-name="enabled"
                                                name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>
                                        <th width="5%" :column="'sr_no'">Sr No.</th>
                                        <th width="2%" data-title="id" is='sortable' :column="'id'">
                                            {{ trans('admin.chat-question.columns.id') }}</th>
                                        <th width="15%"data-title="question" is='sortable' :column="'question'">
                                            {{ trans('admin.chat-question.columns.question') }}</th>
                                        <th width="15%"data-title="keyword_id" is='sortable' :column="'keyword_id'">
                                            {{ trans('admin.chat-question.columns.keyword_id') }}</th>
                                        <th width="5%" data-title="cadre_id" is='sortable' :column="'cadre_id'">
                                            {{ trans('admin.chat-question.columns.cadre_id') }}</th>
                                        <th width="5%" data-title="hit" is='sortable' :column="'hit'">
                                            {{ trans('admin.chat-question.columns.hit') }}</th>
                                        <th width="13%"data-title="category" is='sortable' :column="'category'">
                                            {{ trans('admin.chat-question.columns.category') }}</th>
                                        <th width="5%" data-title="activated" is='sortable' :column="'activated'">
                                            {{ trans('admin.chat-question.columns.activated') }}</th>
                                        <th width="5%" data-title="like_count" is='sortable' :column="'like_count'">
                                            {{ trans('admin.chat-question.columns.like_count') }}</th>
                                        <th width="5%" data-title="dislike_count" is='sortable'
                                            :column="'dislike_count'">
                                            {{ trans('admin.chat-question.columns.dislike_count') }}</th>
                                        <th width="5%" data-title="created_at" is='sortable' :column="'created_at'">
                                            {{ trans('admin.chat-question.columns.created_at') }}</th>
                                        <th width="20%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-ulk-info d-table-cell text-center" colspan="0">
                                            <span
                                                class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }}
                                                @{{ clickedBulkItemsCount }}. <a href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAll('/admin/chat-questions')"
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
                                                    @click="bulkDelete('/admin/chat-questions/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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

                                        <td>
                                            <span
                                                v-if="getId() ==(pagination.state.current_page -1) * pagination.state.per_page + index+1 "
                                                style="background-color: yellow">@{{ (pagination.state.current_page - 1) * pagination.state.per_page + index + 1 }}</span>
                                            <span v-else>@{{ (pagination.state.current_page - 1) * pagination.state.per_page + index + 1 }}</span>
                                        </td>
                                        <td data-title="id">@{{ item.id }}</td>
                                        <td data-title="question">@{{ item.question }}</td>
                                        <td data-title="keyword_id">
                                            <span v-for="(kw, i) in item.question_keywords"
                                                class="badge badge-warning m-1"
                                                style="font-size: 0.8rem;">@{{ kw.keywords && kw.keywords.title ? kw.keywords.title : null }} </span>
                                        </td>
                                        {{-- <td>
                                            <span v-for="(cadre,i) in getCadreNamesByIds(item)">
                                                <span class="badge badge-warning m-1 p-1">@{{ cadre }}</span>
                                            </span>
                                        </td> --}}
                                        {{-- <td>
                                            <span v-if="all_cadres.length ===item.cadre_id.split(',').length"
                                                class="badge badge-warning m-1 p-1">ALL Cadre</span>
                                            <span v-else>
                                                <span v-for="(cadre,i) in getCadreNamesByIds(item)">
                                                    <span class="badge badge-warning m-1 p-1">@{{ cadre }}</span>
                                                </span>
                                            </span>
                                        </td> --}}
                                        <td data-title="cadre_id">
                                            <span v-if="all_cadres.length ===item.cadre_id.split(',').length"
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
                                        <td data-title="hit">@{{ item.hit }}</td>
                                        <td data-title="category">@{{ item.category }}</td>
                                        <td data-title="activated">@{{ item.activated }}</td>
                                        {{-- <td>
                                            <label class="switch switch-3d switch-success">
                                                <input type="checkbox" class="switch-input" v-model="collection[index].activated" @change="toggleSwitch(item.resource_url, 'activated', collection[index])">
                                                <span class="switch-slider"></span>
                                            </label>
                                        </td> --}}

                                        <td data-title="like_count">@{{ item.like_count }}</td>
                                        <td data-title="dislike_count">@{{ item.dislike_count }}</td>
                                        <td data-title="created_at">@{{ item.created_at | moment }}</td>
                                        <td class="action_buttons">
                                            <div class="row">
                                                @can('admin.chat-question.edit')
                                                    <div class="col-auto">
                                                        <a class="btn btn-sm btn-spinner btn-info"
                                                            :href="item.resource_url + '/edit'"
                                                            @click="idStore((pagination.state.current_page -1) * pagination.state.per_page + index+1)"
                                                            title="{{ trans('brackets/admin-ui::admin.btn.edit') }}"
                                                            role="button"><i class="fa fa-edit"></i></a>
                                                    </div>
                                                @endcan
                                                @can('admin.chat-question.delete')
                                                    <form class="col" @submit.prevent="deleteItem(item.resource_url)">
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i
                                                                class="fa fa-trash-o"></i></button>
                                                    </form>
                                                @endcan
                                                @can('admin.chat-question.addTag')
                                                    <form class="col" @click="addTag(item.resource_url + '/tag')"
                                                        onsubmit="return false">
                                                        {{-- <a class="btn btn-sm btn-spinner btn-warning" :href="item.resource_url + '/copy'" title="{{ trans('brackets/admin-ui::admin.btn.copy') }}" role="button"><i class="fa fa-copy"></i></a>onclick="return confirm('Are you sure you want to Copy this Assessment?');" --}}
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            title="Add Quesion To Training Tag"><i
                                                                class="fa fa-tag"></i></button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                @can('admin.chat-question.create')
                                    <a class="btn btn-primary btn-spinner" href="{{ url('admin/chat-questions/create') }}"
                                        role="button"><i class="fa fa-plus"></i>&nbsp;
                                        {{ trans('admin.chat-question.actions.create') }}</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </chat-question-listing>

@endsection
@section('bottom-scripts')
    @include('admin.script-element-pagination')
@endsection
