@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.t-sub-module-master.actions.edit', ['name' => $tSubModuleMaster->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <t-sub-module-master-form
                :action="'{{ $tSubModuleMaster->resource_url }}'"
                :data="{{ $tSubModuleMaster->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.t-sub-module-master.actions.edit', ['name' => $tSubModuleMaster->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.t-sub-module-master.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </t-sub-module-master-form>

        </div>
    
</div>

@endsection