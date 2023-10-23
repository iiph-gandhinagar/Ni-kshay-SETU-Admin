import AppListing from '../app-components/Listing/AppListing';

Vue.component('user-app-version-listing', {
    mixins: [AppListing],
    data: function data() {
        return {
            form: {
                current_plateform: '',
                app_version:'',
            },
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            },    
        }
    },
    filters: {
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
        }
    },
});