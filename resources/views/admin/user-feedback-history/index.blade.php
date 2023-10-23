@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.user-feedback-history.actions.index'))

@section('body')

    <user-feedback-history-listing
        :data="{{ $data->toJson() }}"
        :subscribers="{{ json_encode($subscriber) }}"
        :rating="{{ json_encode($rating) }}"
        :url="'{{ url('admin/user-feedback-histories') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.user-feedback-history.actions.index') }}
                        <a class="btn btn-primary btn-sm pull-right m-b-0 ml-2" :href="'/admin/user-feedback-histories/export?rating_id=' + form.select_rating + '&subscriber_id='+form.select_subscriber" role="button"><i class="fa fa-file-excel-o"></i>&nbsp; {{ trans('admin.user-feedback-history.actions.export') }}</a>
                        {{-- <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/user-feedback-histories/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.user-feedback-history.actions.create') }}</a> --}}
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="row justify-content-md-between col-md-12">

                                <div class="col-md-3 form-group">

                                    <multiselect :searchable="true" v-model="form.select_rating" id="rating_id" name="rating_id"
                                        placeholder="Select Rating"
                                        :options="{{ $rating }}.map(type => type.ratings)"
                                        {{-- :custom-label="opt => {{ $rating }}.find(x => x.ratings == opt).ratings" --}}
                                        open-direction="auto" :multiple="false" @input="filter('rating_id', form.select_rating)">
                                    </multiselect>
                                </div>
                                <div class="col-md-3 form-group">
                                    <multiselect :searchable="true" v-model="form.select_subscriber" id="subscriber_id" name="subscriber_id"
                                        placeholder="Select Subscriber"
                                        @search-change="asyncFind"
                                        :options="form.subscriber_data.map(type => type.id)"
                                        :custom-label="opt =>form.subscriber_data.find(x => x.id == opt).name"
                                        open-direction="auto" :multiple="false" @input="filter('subscriber_id', form.select_subscriber);">
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

                            <table class="table table-hover ">
                                <thead>
                                    <tr>
                                        <th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>

                                        <th is='sortable' width="5%" data-title="id"  :column="'id'">{{ trans('admin.user-feedback-history.columns.id') }}</th>
                                        <th is='sortable' width="15%" data-title="subscriber_id"  :column="'subscriber_id'">{{ trans('admin.user-feedback-history.columns.subscriber_id') }}</th>
                                        <th is='sortable' width="20%" data-title="feedback_id"  :column="'feedback_id'">{{ trans('admin.user-feedback-history.columns.feedback_id') }}</th>
                                        <th is='sortable' width="15%" data-title="ratings"  :column="'ratings'">{{ trans('admin.user-feedback-history.columns.ratings') }}</th>
                                        <th is='sortable' width="5%" data-title="skip"  :column="'skip'">{{ trans('admin.user-feedback-history.columns.skip') }}</th>
                                        <th is='sortable' width="5%" data-title="review"  :column="'review'">{{ trans('admin.user-feedback-history.columns.review') }}</th>
                                        <th is='sortable' width="20%" data-title="created_at"  :column="'created_at'">{{ trans('admin.user-feedback-history.columns.created_at') }}</th>

                                        <th width="15%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="7">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/user-feedback-histories')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/user-feedback-histories/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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

                                        <td data-title="id" >@{{ item.id }}</td>
                                        <td data-title="subscriber_id" >@{{ item.user.name }}</td>
                                        <td data-title="feedback_id" >@{{ item.feedback_question.feedback_question }}</td>
                                        <td data-title="ratings" >@{{ item.ratings }}</td>
                                        <td data-title="skip" >@{{ item.skip }}</td>
                                        <td data-title="review" >@{{ item.review }}</td>
                                        <td data-title="created_at" >@{{ item.created_at | moment }}</td>
                                        <td></td>
                                        {{-- <td>
                                            <div class="row no-gutters">
                                                <div class="col-auto">
                                                    <a class="btn btn-sm btn-spinner btn-info" :href="item.resource_url + '/edit'" title="{{ trans('brackets/admin-ui::admin.btn.edit') }}" role="button"><i class="fa fa-edit"></i></a>
                                                </div>
                                                <form class="col" @submit.prevent="deleteItem(item.resource_url)">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i class="fa fa-trash-o"></i></button>
                                                </form>
                                            </div>
                                        </td> --}}
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
                                {{-- <a class="btn btn-primary btn-spinner" href="{{ url('admin/user-feedback-histories/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.user-feedback-history.actions.create') }}</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </user-feedback-history-listing>

@endsection