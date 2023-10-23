@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.lb-subscriber-ranking-history.actions.edit', ['name' => $lbSubscriberRankingHistory->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <lb-subscriber-ranking-history-form
                :action="'{{ $lbSubscriberRankingHistory->resource_url }}'"
                :data="{{ $lbSubscriberRankingHistory->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.lb-subscriber-ranking-history.actions.edit', ['name' => $lbSubscriberRankingHistory->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.lb-subscriber-ranking-history.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </lb-subscriber-ranking-history-form>

        </div>
    
</div>

@endsection