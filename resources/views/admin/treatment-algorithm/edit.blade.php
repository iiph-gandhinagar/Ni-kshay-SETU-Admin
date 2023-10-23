@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.treatment-algorithm.actions.edit', ['name' => $treatmentAlgorithm->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <treatment-algorithm-form
                :action="'{{ $treatmentAlgorithm->resource_url }}'"
                :data="{{ $treatmentAlgorithm->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                v-bind:cadre="{{  json_encode($cadre) }}"
                v-bind:state="{{  json_encode($state) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.treatment-algorithm.actions.edit', ['name' => $treatmentAlgorithm->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.treatment-algorithm.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </treatment-algorithm-form>

        </div>
    
</div>

@endsection