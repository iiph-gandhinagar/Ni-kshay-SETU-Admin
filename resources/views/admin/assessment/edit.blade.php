@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.assessment.actions.edit', ['name' => $assessment->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <assessment-form
                :action="'{{ $assessment->resource_url }}'"
                :data="{{ $assessment->toJsonAllLocales() }}"
                v-bind:cadre="{{  json_encode($cadre) }}"
                v-bind:district="{{  json_encode($district) }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.assessment.actions.edit', ['name' => $assessment->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.assessment.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </assessment-form>

        </div>
    
</div>

@endsection