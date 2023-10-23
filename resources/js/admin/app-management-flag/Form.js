import AppForm from '../app-components/Form/AppForm';

Vue.component('app-management-flag-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                variable:  '' ,
                // value:  [] ,
                value:  this.getLocalizedFormDefaults() ,
                type:  '' ,
                
            },
            options: [],
        }
    },
    methods:{
        addFeature: function(newResTag) {
            console.log("this.form.value---->",this.form.value);
            if (newResTag.includes("|")) {
                alert('Please Add response without "|" ');
            } else {
                this.options.push(newResTag);
                this.form.value.push(newResTag);
            }
        },
    }

});