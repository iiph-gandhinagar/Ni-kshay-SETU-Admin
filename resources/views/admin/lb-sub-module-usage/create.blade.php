@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.lb-sub-module-usage.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">
        
        <lb-sub-module-usage-form
            :action="'{{ url('admin/lb-sub-module-usages') }}'"
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>
                
                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ trans('admin.lb-sub-module-usage.actions.create') }}
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