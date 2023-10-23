@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.cgc-intervention.actions.edit', ['name' => $cgcIntervention->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <cgc-intervention-form
                :action="'{{ $cgcIntervention->resource_url }}'"
                :data="{{ $cgcIntervention->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.cgc-intervention.actions.edit', ['name' => $cgcIntervention->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.cgc-intervention.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </cgc-intervention-form>

        </div>
    
</div>

@endsection