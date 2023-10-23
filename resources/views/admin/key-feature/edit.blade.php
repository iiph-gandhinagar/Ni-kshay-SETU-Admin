@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.key-feature.actions.edit', ['name' => $keyFeature->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <key-feature-form
                :action="'{{ $keyFeature->resource_url }}'"
                :data="{{ $keyFeature->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.key-feature.actions.edit', ['name' => $keyFeature->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.key-feature.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </key-feature-form>

        </div>
    
</div>

@endsection