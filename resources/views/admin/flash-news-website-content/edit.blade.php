@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.flash-news-website-content.actions.edit', ['name' => $flashNewsWebsiteContent->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <flash-news-website-content-form
                :action="'{{ $flashNewsWebsiteContent->resource_url }}'"
                :data="{{ $flashNewsWebsiteContent->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.flash-news-website-content.actions.edit', ['name' => $flashNewsWebsiteContent->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.flash-news-website-content.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </flash-news-website-content-form>

        </div>
    
</div>

@endsection