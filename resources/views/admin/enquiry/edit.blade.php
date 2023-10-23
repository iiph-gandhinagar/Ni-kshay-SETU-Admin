@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.enquiry.actions.edit', ['name' => $enquiry->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <enquiry-form
                :action="'{{ $enquiry->resource_url }}'"
                :data="{{ $enquiry->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.enquiry.actions.edit', ['name' => $enquiry->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.enquiry.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </enquiry-form>

        </div>
    
</div>

@endsection