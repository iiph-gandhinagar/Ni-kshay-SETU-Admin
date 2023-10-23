@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.tour-slide.actions.edit', ['name' => $tourSlide->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <tour-slide-form
                :action="'{{ $tourSlide->resource_url }}'"
                :data="{{ $tourSlide->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.tour-slide.actions.edit', ['name' => $tourSlide->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.tour-slide.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </tour-slide-form>

        </div>
    
</div>

@endsection