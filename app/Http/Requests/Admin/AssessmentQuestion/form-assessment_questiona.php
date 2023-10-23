// <div class="form-group row align-items-center" :class="{'has-danger': errors.has('question'), 'has-success': fields.question && fields.question.valid }">
    //     <label for="question" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-question.columns.question') }}</label>
    //         <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
    //         <input type="text" v-model="form.question" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('question'), 'form-control-success': fields.question && fields.question.valid}" id="question" name="question" placeholder="{{ trans('admin.assessment-question.columns.question') }}">
    //         <div v-if="errors.has('question')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('question') }}</div>
    //     </div>
    // </div>
    
    // <div class="form-group row align-items-center" :class="{'has-danger': errors.has('option1'), 'has-success': fields.option1 && fields.option1.valid }">
    //     <label for="option1" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-question.columns.option1') }}</label>
    //         <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
    //         <input type="text" v-model="form.option1" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option1'), 'form-control-success': fields.option1 && fields.option1.valid}" id="option1" name="option1" placeholder="{{ trans('admin.assessment-question.columns.option1') }}">
    //         <div v-if="errors.has('option1')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('option1') }}</div>
    //     </div>
    // </div>
    
    // <div class="form-group row align-items-center" :class="{'has-danger': errors.has('option2'), 'has-success': fields.option2 && fields.option2.valid }">
    //     <label for="option2" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-question.columns.option2') }}</label>
    //         <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
    //         <input type="text" v-model="form.option2" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option2'), 'form-control-success': fields.option2 && fields.option2.valid}" id="option2" name="option2" placeholder="{{ trans('admin.assessment-question.columns.option2') }}">
    //         <div v-if="errors.has('option2')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('option2') }}</div>
    //     </div>
    // </div>
    
    // <div class="form-group row align-items-center" :class="{'has-danger': errors.has('option3'), 'has-success': fields.option3 && fields.option3.valid }">
    //     <label for="option3" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-question.columns.option3') }}</label>
    //         <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
    //         <input type="text" v-model="form.option3" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option3'), 'form-control-success': fields.option3 && fields.option3.valid}" id="option3" name="option3" placeholder="{{ trans('admin.assessment-question.columns.option3') }}">
    //         <div v-if="errors.has('option3')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('option3') }}</div>
    //     </div>
    // </div>
    
    // <div class="form-group row align-items-center" :class="{'has-danger': errors.has('option4'), 'has-success': fields.option4 && fields.option4.valid }">
    //     <label for="option4" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-question.columns.option4') }}</label>
    //         <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
    //         <input type="text" v-model="form.option4" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option4'), 'form-control-success': fields.option4 && fields.option4.valid}" id="option4" name="option4" placeholder="{{ trans('admin.assessment-question.columns.option4') }}">
    //         <div v-if="errors.has('option4')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('option4') }}</div>
    //     </div>
    // </div>