<div class="form-group row align-items-center" >
    <label for="material" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">Upload CSV</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div v-if="errors.has('')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('material') }}</div>
        @if(isset($messageNotification))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\MessageNotification::class)->getMediaCollection('material'),
                'media' => $messageNotification->getThumbs200ForCollection('material'),
                'Label' => "material"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\MessageNotification::class)->getMediaCollection('material'),
                'Label' => "material"
            ])
        @endif

    </div>
</div>
<div class="form-group row align-items-center" :class="{'has-danger': errors.has('message'), 'has-success': fields.message && fields.message.valid }">
    <label for="message" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.message-notification.columns.message') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-10'">
        {{-- <input type="text" v-model="form.message" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('message'), 'form-control-success': fields.message && fields.message.valid}" id="message" name="message" placeholder="{{ trans('admin.assessment-question.columns.message') }}"> --}}
        <select class="form-control" v-model="form.message" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('message'), 'form-control-success': fields.message && fields.message.valid}" id="message" name="message" placeholder="Message">
            <option value="">Select Options</option>                              
            {{-- <option value='રાષ્ટ્રીય ક્ષય પ્રોગ્રામ (NTEP) માટે હવે આપને મળી રેહશે "નિક્ષય સેતુ" એપ દ્વારા ડીજીટલ માર્ગદર્શન. આ એપ ગુગલ પ્લે સ્ટોર અથવા www.nikshay-setu.in પર ઉપલબ્ધ રહેશે'>રાષ્ટ્રીય ક્ષય પ્રોગ્રામ (NTEP) માટે હવે આપને મળી રેહશે "નિક્ષય સેતુ" એપ દ્વારા ડીજીટલ માર્ગદર્શન. આ એપ ગુગલ પ્લે સ્ટોર અથવા www.nikshay-setu.in પર ઉપલબ્ધ રહેશે</option> --}}
            <option value='Hey! Kindly find technical knowledge support app for National TB Program (NTEP). "Nikshay SETU". Search on Google Play Store or visit: www.nikshay-setu.in'>Hey! Kindly find technical knowledge support app for National TB Program (NTEP). "Nikshay SETU". Search on Google Play Store or visit: www.nikshay-setu.in</option>
            <option value='You are missing the new features and modules of the Nikshay Setu app on the NTEP Program. Download "Nikshay Setu" app from Google Play Store powered by Digiflux'>You are missing the new features and modules of the Nikshay Setu app on the NTEP Program. Download "Nikshay Setu" app from Google Play Store powered by Digiflux</option>
            <option value='Update Now! You are missing the new features and modules of the NTEP on the Nikshay Setu app. Update "Nikshay Setu" from Google Play Store powered by Digiflux'>Update Now! You are missing the new features and modules of the NTEP on the Nikshay Setu app. Update "Nikshay Setu" from Google Play Store powered by Digiflux</option>
        </select>
        <div v-if="errors.has('message')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('message') }}</div>
    </div>
</div>
