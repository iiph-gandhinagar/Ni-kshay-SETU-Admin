import AppListing from '../app-components/Listing/AppListing';

Vue.component('lb-sub-module-usage-listing', {
    mixins: [AppListing],
    data: function data() {
        return {
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