import AppForm from '../app-components/Form/AppForm';

Vue.component('static-release-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                active:  false ,
                bugs_fix:  this.getLocalizedFormDefaults() ,
                date:  '' ,
                features:  this.getLocalizedFormDefaults() ,
                order_index:  '' ,
                
            },
            options: [],
            bug_options: []
        }
    },
    methods:{
        addTag: function(newTag) {
            console.log(this.form.features);
            let tag_repeated = -1;
            if(this.form.features.length > 0 && this.form.features != ''){
                tag_repeated = this.form.features.findIndex(item => $.trim(newTag).toLowerCase() === item.toLowerCase());
            }
           
            console.log(tag_repeated);
            if (newTag.includes("|")) {
                alert('Please Add features without "|" ');
            } else if(tag_repeated != -1){
                alert(newTag + ' features is Repated!!');
            }else {
                this.options.push($.trim(newTag));
                this.form.features.push($.trim(newTag));
            }
        },
        addBug: function(newTag) {
            console.log(this.form.bugs_fix);
            let tag_repeated = -1;
            if(this.form.bugs_fix.length > 0 && this.form.bugs_fix != ''){
                tag_repeated = this.form.bugs_fix.findIndex(item => $.trim(newTag).toLowerCase() === item.toLowerCase());
            }
           
            console.log(tag_repeated);
            if (newTag.includes("|")) {
                alert('Please Add bugs_fix without "|" ');
            } else if(tag_repeated != -1){
                alert(newTag + ' bugs_fix is Repated!!');
            }else {
                this.bug_options.push($.trim(newTag));
                this.form.bugs_fix.push($.trim(newTag));
            }
        }
    }

});