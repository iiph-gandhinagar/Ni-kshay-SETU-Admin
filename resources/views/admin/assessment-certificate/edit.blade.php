@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.assessment-certificate.actions.edit', ['name' => $assessmentCertificate->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <assessment-certificate-form
                :action="'{{ $assessmentCertificate->resource_url }}'"
                :data="{{ $assessmentCertificate->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.assessment-certificate.actions.edit', ['name' => $assessmentCertificate->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.assessment-certificate.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </assessment-certificate-form>

        </div>
    
</div>

@endsection