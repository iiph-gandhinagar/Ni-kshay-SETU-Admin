@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.lb-sub-module-usage.actions.edit', ['name' => $lbSubModuleUsage->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <lb-sub-module-usage-form
                :action="'{{ $lbSubModuleUsage->resource_url }}'"
                :data="{{ $lbSubModuleUsage->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.lb-sub-module-usage.actions.edit', ['name' => $lbSubModuleUsage->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.lb-sub-module-usage.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </lb-sub-module-usage-form>

        </div>
    
</div>

@endsection