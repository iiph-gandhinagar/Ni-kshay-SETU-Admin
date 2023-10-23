@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.flash-similar-app.actions.edit', ['name' => $flashSimilarApp->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <flash-similar-app-form
                :action="'{{ $flashSimilarApp->resource_url }}'"
                :data="{{ $flashSimilarApp->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.flash-similar-app.actions.edit', ['name' => $flashSimilarApp->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.flash-similar-app.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </flash-similar-app-form>

        </div>
    
</div>

@endsection