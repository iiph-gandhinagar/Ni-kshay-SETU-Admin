@extends('brackets/admin-ui::admin.layout.default')

@section('title', 'Dashboard')


<link href="{{ asset('css/custom-dashboard.css') }}" rel="stylesheet">

@section('body')

 
<div class="row">
    <div class="col-md-6 col-xl-4 mb-4">
        <div class="card  widget-content top-count-card subscriber-card-color" onclick="display()">
            <div class="widget-content-left mt-1">
                
                <div class="count-total text-white" style="cursor: pointer;margin-bottom: 20px;"
                    onclick="getSubscriberBYDate()">
                    <span class="count count-total" ></span>
                </div>
                <div class="text-white count-font">Total Tags</div>
                <div class="count-total text-white" style="cursor: pointer;" 
                    >{{$tagcount}}
                    <span class="count count-total" ></span>
                </div>
            </div>
            <div class="widget-content-right text-white">
              <br>
                <i class="fa fa-list-ol" style="font-size:70px; margin-left: 30px;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4 mb-4">
        <div class="card  widget-content top-count-card enquiry-card-color" onclick="display_pattern()">
            <div class="widget-content-left mt-1">
                <div class="count-total text-white" style="cursor: pointer;margin-bottom: 20px;"
                    onclick='window.location="admin/subscriber-activities?date=" + (moment(new Date()).format("DD/MM/YYYY"));'>
                    <span class="count count-total" ></span>
                </div>
                <div class="text-white count-font">Total Patterns</div>
                <div class="count-total text-white" style="cursor: pointer;"
                    >{{$patterncount}}
                    <span class="count count-total" ></span>
                </div>
            </div>
            <div class="widget-content-right text-white">
                <br>
            <i class="fa fa-list" style="font-size:70px; margin-left: 30px;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4 mb-4">
        <div class="card  widget-content top-count-card assessemnt-card-color"  onclick="repeat()">
            <div class="widget-content-left mt-2">
                <div class="count-total text-white" style="cursor: pointer;margin-bottom: 20px;"
                   >
                    <span class="count count-total" ></span>
                </div>
                <div class="text-white count-font">Total Repeating Patterns</div>
                <div class="count-total text-white" style="cursor: pointer;"
                    >{{$varcount}}
                    <span class="count count-total" ></span>
                </div>
            </div>
            <div class="widget-content-right text-white">
                {{-- <i class="fa fa-thin fa-clipboard-list fa-6x"></i> --}}
                <img src="../../fluent_clipboard-task-add-24-regular.svg" alt="assessment" class="icon-position" />
            </div>
        </div>
    </div>
</div>
           
            

    <t-training-tag-listing
        :data="{{ $viewtag->toJson() }}"
        :url="'{{ url('admin/t-training-tags') }}'"
        inline-template >

        <div class="row" id="hide">
            <div class="col">
                <div class="card">
                    
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            
                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div> 
                            <table class="table table-hover table-listing">
                                <thead>
                                    <tr>
                                        <th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>

                                        <th is='sortable' width="5%" :column="'id'">{{ trans('admin.t-training-tag.columns.id') }}</th>
                                        <th is='sortable' width="25%" :column="'tag'">{{ trans('admin.t-training-tag.columns.tag') }}</th>
                                        <th is='sortable' width="5%" :column="'is_fix_response'">{{ trans('admin.t-training-tag.columns.is_fix_response') }}</th>
                                        <th is='sortable' width="20%" :column="'updated_at'">{{ trans('admin.t-training-tag.columns.updated_at') }}</th>
                                        <th is='sortable' width="5%" :column="'like_count'">{{ trans('admin.t-training-tag.columns.like_count') }}</th>
                                        <th is='sortable' width="5%" :column="'dislike_count'">{{ trans('admin.t-training-tag.columns.dislike_count') }}</th>
                                        <th is='sortable' width="5%" :column="'pattern'">Pattern Count</th>
                                        <th is='sortable' width="5%" :column="'Question'">Question Count</th>

                                        <th width="20%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="8">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/t-training-tags')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/t-training-tags/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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

                                        <td>@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</td>
                                        
                                        <td>@{{ item.tag }}</td>
                                        <td>@{{ item.is_fix_response }}</td>
                                        <td>@{{ item.updated_at | moment }}</td>
                                        <td>@{{ item.like_count }}</td>
                                        <td>@{{ item.dislike_count }}</td>
                                        <td>@{{item.PatternsInTag}}</td>
                                        <td>@{{item.QuestionsInTag}}</td>
                                        
                                    </tr>
                                </tbody>
                            </table>


                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/t-training-tags/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.t-training-tag.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </t-training-tag-listing>

    <t-training-tag-listing
        :data="{{ $viewtag->toJson() }}"
        :url="'{{ url('admin/t-training-tags') }}'"
        inline-template >

        <div class="row" id="hide_pattern">
        <div class="col">
                <div class="card">
                    
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                        
                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div> 
                            <table class="table table-hover table-listing">
                                <thead>
                                    <tr>
                                        <th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>

                                        <th is='sortable' width="5%" :column="'id'">{{ trans('admin.t-training-tag.columns.id') }}</th>
                                        <th is='sortable' width="25%" :column="'tag'">{{ trans('admin.t-training-tag.columns.tag') }}</th>
                                        <th is='sortable' width="25%" :column="'pattern'">{{ trans('admin.t-training-tag.columns.pattern') }}</th>
                                        <th is='sortable' width="20%" :column="'updated_at'">{{ trans('admin.t-training-tag.columns.updated_at') }}</th>
                                        

                                        <th width="20%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="8">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/t-training-tags')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/t-training-tags/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" >
                                        <td class="bulk-checkbox">
                                            <input class="form-check-input" :id="'enabled' + item.id" type="checkbox" v-model="bulkItems[item.id]" v-validate="''" :data-vv-name="'enabled' + item.id"  :name="'enabled' + item.id + '_fake_element'" @click="onBulkItemClicked(item.id)" :disabled="bulkCheckingAllLoader">
                                            <label class="form-check-label" :for="'enabled' + item.id">
                                            </label>
                                        </td>

                                        <td>@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</td>
                                        <td>@{{ item.tag }}</td>
                                        <td>
                                            @{{item.pattern}} 
                                        </td>
                                        
                                        <td>@{{ item.updated_at | moment }}</td>
                                        
                                        
                                    </tr>
                                </tbody>
                            </table>


                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/t-training-tags/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.t-training-tag.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </t-training-tag-listing>

    <t-training-tag-listing
        :view="{{ $collection->toJson() }}"
        :url="'{{ url('admin/t-training-tags') }}'"
        inline-template >

        <div class="row" id="hide_pattern">
        <div class="col">
                <div class="card">
                    
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                        
                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div> 
                            <table class="table table-hover table-listing">
                                <thead>
                                    <tr>
                                        <th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>

                                        <th is='sortable' width="5%" :column="'id'">{{ trans('admin.t-training-tag.columns.id') }}</th>
                                        <th is='sortable' width="25%" :column="'tag'">{{ trans('admin.t-training-tag.columns.tag') }}</th>
                                        <th is='sortable' width="25%" :column="'pattern'">{{ trans('admin.t-training-tag.columns.pattern') }}</th>
                                        <th is='sortable' width="20%" :column="'updated_at'">{{ trans('admin.t-training-tag.columns.updated_at') }}</th>
                                        

                                        <th width="20%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="8">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/t-training-tags')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/t-training-tags/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" >
                                        <td class="bulk-checkbox">
                                            <input class="form-check-input" :id="'enabled' + item.id" type="checkbox" v-model="bulkItems[item.id]" v-validate="''" :data-vv-name="'enabled' + item.id"  :name="'enabled' + item.id + '_fake_element'" @click="onBulkItemClicked(item.id)" :disabled="bulkCheckingAllLoader">
                                            <label class="form-check-label" :for="'enabled' + item.id">
                                            </label>
                                        </td>

                                        <td>@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</td>
                                        <td>@{{ item.tag }}</td>
                                        <td>
                                            @{{item.pattern}} 
                                        </td>
                                        
                                        <td>@{{ item.value}}</td>
                                        
                                        
                                    </tr>
                                </tbody>
                            </table>


                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/t-training-tags/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.t-training-tag.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </t-training-tag-listing>


                            <script type="application/javascript" >
                                const targetDiv = document.getElementById("hide");
 targetDiv.style.display = "block";


 function display() {

    
    const div = document.getElementById("hide_pattern");
    const div2 = document.getElementById("hide_repeat");

 div.style.display = "none";
 div2.style.display = "none";
console.log(targetDiv);
 $targetDiv = document.getElementById("hide");
  if ($targetDiv.style.display === "none") {
    
    document.getElementById("hide").style.display = "block";
  } else {
    document.getElementById("hide").style.display = "none";
   

  }

};

const div = document.getElementById("hide_pattern");
 div.style.display = "none";
function display_pattern() {

    
    const targetDiv = document.getElementById("hide");
    const div2 = document.getElementById("hide_repeat");

 targetDiv.style.display = "none";
 div2.style.display = "none";
 $div = document.getElementById("hide_pattern");
  if ($div.style.display === "none") {
    
    document.getElementById("hide_pattern").style.display = "block";
    console.log('block');
  } else {
    document.getElementById("hide_pattern").style.display = "none";
   

  }

};

const div2 = document.getElementById("hide_repeat");
 div2.style.display = "none";
function repeat() {

    
    const targetDiv = document.getElementById("hide");
    const div = document.getElementById("hide_pattern");
 targetDiv.style.display = "none";
 div.style.display = "none";
 $divtag = document.getElementById("hide_repeat");
  if ($divtag.style.display === "none") {
    
    document.getElementById("hide_repeat").style.display = "block";
    console.log('block');
  } else {
    document.getElementById("hide_repeat").style.display = "none";
   

  }

};

</script>

    @endsection
  