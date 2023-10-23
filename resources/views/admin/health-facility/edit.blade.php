@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.health-facility.actions.edit', ['name' => $healthFacility->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <health-facility-form
                :action="'{{ $healthFacility->resource_url }}'"
                :data="{{ $healthFacility->toJson() }}"
                v-bind:state="{{  json_encode($state) }}"
                v-bind:district="{{  json_encode($district) }}"
                v-bind:block="{{  json_encode($taluka) }}"
                v-bind:health_facility="{{  json_encode($health_facility) }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.health-facility.actions.edit', ['name' => $healthFacility->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.health-facility.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </health-facility-form>

        </div>
    
</div>

@endsection