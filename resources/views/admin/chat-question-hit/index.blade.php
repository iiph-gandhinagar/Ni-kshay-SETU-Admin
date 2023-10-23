@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.chat-question-hit.actions.index'))

@section('body')

    <chat-question-hit-listing :data="{{ $data->toJson() }}" :subscribers="{{ $subscriber }}"
        :url="'{{ url('admin/chat-question-hits') }}'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.chat-question-hit.actions.index') }}
                        @can('admin.chat-question-hit.export')
                            <a class="btn btn-primary btn-sm pull-right m-b-0 ml-2"
                                :href="'/admin/chat-question-hits/export?subscriber_id=' + form.select_subscriber + '&category=' + form.select_category"
                                role="button"><i class="fa fa-file-excel-o"></i>&nbsp;
                                {{ trans('admin.chat-question-hit.actions.export') }}
                            </a>
                        @endcan
                        {{-- <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/chat-question-hits/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.chat-question-hit.actions.create') }}</a> --}}
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="row col-md-12 justify-content-md-between">

                                <div class="col-md-4 form-group ">
                                    
                                    <multiselect :searchable="true" v-model="form.select_category" id="category"
                                        name="category" placeholder="Select Category"
                                        :options="{{ $category }}.map(type => type.category)"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('category', form.select_category)">
                                    </multiselect>
                                </div>

                                <div class="col-md-4 form-group">
                                    <multiselect :searchable="true" v-model="form.select_subscriber" id="subscriber_id" name="subscriber_id"
                                        placeholder="Select Subscriber"
                                        @search-change="asyncFind"
                                        :options="form.subscriber_data.map(type => type.id)"
                                        :custom-label="opt =>form.subscriber_data.find(x => x.id == opt).name"
                                        open-direction="auto" :multiple="false" @input="filter('subscriber_id', form.select_subscriber)">
                                    </multiselect>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
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
                            
                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span
                                        class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <table class="table table-hover">
                                <thead>
                                    <tr>

                                        <th is='sortable' data-title="id" :column="'id'">{{ trans('admin.chat-question-hit.columns.id') }}
                                        </th>
                                        <th is='sortable' data-title="question_id" :column="'question_id'">
                                            {{ trans('admin.chat-question-hit.columns.question_id') }}</th>
                                        <th is='sortable' data-title="subscriber_id" :column="'subscriber_id'">
                                            {{ trans('admin.chat-question-hit.columns.subscriber_id') }}</th>
                                        <th is='sortable'  data-title="category">Category</th>
                                        <th is='sortable' data-title="created_at" :column="'created_at'">Created At</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id"
                                        :class="bulkItems[item.id] ? 'bg-bulk' : ''">

                                        <td data-title="id" >@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</td>
                                        <td data-title="question_id" >@{{ item . questions_with_trashed ? item . questions_with_trashed . question : ''}}</td>
                                        <td data-title="subscriber_id" >@{{ item . user . name }}</td>
                                        <td data-title="category" >@{{ item . questions_with_trashed ? item . questions_with_trashed . category : '' }}</td>
                                        <td data-title="created_at" >@{{ (item . created_at) | moment }}</td>

                                        <td></td>
                                
                                    </tr>
                                </tbody>
                            </table>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                {{-- <a class="btn btn-primary btn-spinner" href="{{ url('admin/chat-question-hits/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.chat-question-hit.actions.create') }}</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </chat-question-hit-listing>

@endsection
