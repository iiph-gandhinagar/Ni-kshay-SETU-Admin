@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.static-module.actions.edit', ['name' => $staticModule->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <static-module-form
                :action="'{{ $staticModule->resource_url }}'"
                :data="{{ $staticModule->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.static-module.actions.edit', ['name' => $staticModule->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.static-module.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </static-module-form>

        </div>
    
</div>

@endsection