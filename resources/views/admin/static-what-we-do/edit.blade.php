@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.static-what-we-do.actions.edit', ['name' => $staticWhatWeDo->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <static-what-we-do-form
                :action="'{{ $staticWhatWeDo->resource_url }}'"
                :data="{{ $staticWhatWeDo->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.static-what-we-do.actions.edit', ['name' => $staticWhatWeDo->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.static-what-we-do.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </static-what-we-do-form>

        </div>
    
</div>

@endsection