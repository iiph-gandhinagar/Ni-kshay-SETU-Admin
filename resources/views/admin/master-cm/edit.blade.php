@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.master-cm.actions.edit', ['name' => $masterCm->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <master-cm-form
                :action="'{{ $masterCm->resource_url }}'"
                :data="{{ $masterCm->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.master-cm.actions.edit', ['name' => $masterCm->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.master-cm.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </master-cm-form>

        </div>
    
</div>

@endsection