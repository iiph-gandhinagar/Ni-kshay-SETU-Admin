@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.differential-care-algorithm.actions.edit', ['name' => $differentialCareAlgorithm->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <differential-care-algorithm-form
                :action="'{{ $differentialCareAlgorithm->resource_url }}'"
                :data="{{ $differentialCareAlgorithm->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                v-bind:cadre="{{  json_encode($cadre) }}"
                v-bind:state="{{  json_encode($state) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.differential-care-algorithm.actions.edit', ['name' => $differentialCareAlgorithm->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.differential-care-algorithm.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </differential-care-algorithm-form>

        </div>
    
</div>

@endsection