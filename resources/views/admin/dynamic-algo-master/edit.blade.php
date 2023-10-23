@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.dynamic-algo-master.actions.edit', ['name' => $dynamicAlgoMaster->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <dynamic-algo-master-form
                :action="'{{ $dynamicAlgoMaster->resource_url }}'"
                :data="{{ $dynamicAlgoMaster->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.dynamic-algo-master.actions.edit', ['name' => $dynamicAlgoMaster->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.dynamic-algo-master.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </dynamic-algo-master-form>

        </div>
    
</div>

@endsection