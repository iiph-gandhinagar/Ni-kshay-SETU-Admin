@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.t-training-tag.actions.index'))

@section('body')

    <chat-question-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/chat-questions/chat-question-tag-list/'.$questionId) }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.t-training-tag.actions.index') }} for Question : {{$question_title}}
                        <a class="btn btn-danger btn-spinner btn-sm pull-right m-b-0 mr-1" 
                            href="#"
                            onclick="window.history.go(-1)" 
                            role="button"><i class="fa fa-arrow-left"></i>&nbsp; Go Back</a>
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

                            <table class="table table-hover table-listing">
                                <thead>
                                    <tr>
                                        <th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>

                                        <th is='sortable' :column="'id'">{{ trans('admin.t-training-tag.columns.id') }}</th>
                                        <th is='sortable' :column="'tag'">{{ trans('admin.t-training-tag.columns.tag') }}</th>
                                        <th is='sortable' :column="'updated_at'">{{ trans('admin.t-training-tag.columns.updated_at') }}</th>
                                        <th is='sortable' :column="'like_count'">{{ trans('admin.t-training-tag.columns.like_count') }}</th>
                                        <th is='sortable' :column="'dislike_count'">{{ trans('admin.t-training-tag.columns.dislike_count') }}</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                        <td class="bulk-checkbox">
                                            <input class="form-check-input" :id="'enabled' + item.id" type="checkbox" v-model="bulkItems[item.id]" v-validate="''" :data-vv-name="'enabled' + item.id"  :name="'enabled' + item.id + '_fake_element'" @click="onBulkItemClicked(item.id)" :disabled="bulkCheckingAllLoader">
                                            <label class="form-check-label" :for="'enabled' + item.id">
                                            </label>
                                        </td>

                                        <td>@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</td>
                                        <td>@{{ item.tag }}</td>
                                        <td>@{{ item.updated_at | moment }}</td>
                                        <td>@{{ item.like_count }}</td>
                                        <td>@{{ item.dislike_count }}</td>
                                        
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

                            {{-- <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </chat-question-listing>

@endsection