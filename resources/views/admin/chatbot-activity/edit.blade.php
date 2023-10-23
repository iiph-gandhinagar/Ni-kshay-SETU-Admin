@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.chatbot-activity.actions.edit', ['name' => $chatbotActivity->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <chatbot-activity-form
                :action="'{{ $chatbotActivity->resource_url }}'"
                :data="{{ $chatbotActivity->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.chatbot-activity.actions.edit', ['name' => $chatbotActivity->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.chatbot-activity.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </chatbot-activity-form>

        </div>
    
</div>

@endsection