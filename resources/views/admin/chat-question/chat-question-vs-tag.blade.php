@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.chat-question.actions.index'))

@section('body')

<chat-question-listing :data="{{ $data->toJson() }}"
    :url="'{{ url('admin/chat-questions/chat-question-vs-tag') }}'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Chat Questions Vs Tags
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <form @submit.prevent="">
                                <div class="row justify-content-md-between">
                                    <div class="col col-lg-7 col-xl-5 form-group">
                                        <div class="input-group">
                                            <input class="form-control"
                                                placeholder="{{ trans('brackets/admin-ui::admin.placeholder.search') }}"
                                                v-model="search" @keyup.enter="filter('search', $event.target.value)" />
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary"
                                                    @click="filter('search', search)"><i class="fa fa-search"></i>&nbsp;
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

                            <table class="table table-hover table-listing">
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

                                        <th is='sortable' :column="'id'">
                                            {{ trans('admin.chat-question.columns.id') }}</th>
                                        <th is='sortable' :column="'question'">
                                            {{ trans('admin.chat-question.columns.question') }}</th>
                                        <th is='sortable' :column="'tag_count'">Tag Count</th>
                                        <th is='sortable' :column="'hit'">
                                            {{ trans('admin.chat-question.columns.hit') }}</th>
                                        <th is='sortable' :column="'activated'">
                                            {{ trans('admin.chat-question.columns.activated') }}</th>
                                        <th is='sortable' :column="'like_count'">{{ trans('admin.chat-question.columns.like_count') }}</th>
                                        <th is='sortable' :column="'dislike_count'">{{ trans('admin.chat-question.columns.dislike_count') }}</th>
                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="0"></td>
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

                                        <td>@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</td>
                                        <td>@{{ item . question }}</td>
                                        <td>
                                            <span v-if="item . tag_count > 0">
                                                <a :href="'/admin/chat-questions/chat-question-tag-list/'+item.id" title="Tag List">@{{ item.tag_count }}</a>
                                            </span>
                                            <span v-else>
                                                @{{ item.tag_count }}
                                            </span>
                                        </td>
                                        <td>@{{ item . hit }}</td>
                                        <td>@{{ item . activated }}</td>

                                        <td>@{{ item.like_count }}</td>
                                        <td>@{{ item.dislike_count }}</td>
                                        <td></td>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </chat-question-listing>

@endsection
