@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.resource-material.actions.edit', ['name' => $resourceMaterial->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <resource-material-form
                :action="'{{ $resourceMaterial->resource_url }}'"
                :data="{{ $resourceMaterial->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-bind:cadre="{{  json_encode($cadre) }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.resource-material.actions.edit', ['name' => $resourceMaterial->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.resource-material.components.form-elements', ['mode' => 'edit'])
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </resource-material-form>

        </div>
    
</div>

@endsection