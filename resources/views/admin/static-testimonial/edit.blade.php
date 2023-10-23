@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.static-testimonial.actions.edit', ['name' => $staticTestimonial->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <static-testimonial-form
                :action="'{{ $staticTestimonial->resource_url }}'"
                :data="{{ $staticTestimonial->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.static-testimonial.actions.edit', ['name' => $staticTestimonial->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.static-testimonial.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </static-testimonial-form>

        </div>
    
</div>

@endsection