@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.static-enquiry.actions.edit', ['name' => $staticEnquiry->email]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <static-enquiry-form
                :action="'{{ $staticEnquiry->resource_url }}'"
                :data="{{ $staticEnquiry->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.static-enquiry.actions.edit', ['name' => $staticEnquiry->email]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.static-enquiry.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </static-enquiry-form>

        </div>
    
</div>

@endsection