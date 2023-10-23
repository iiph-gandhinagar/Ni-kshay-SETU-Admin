import AppListing from '../app-components/Listing/AppListing';

Vue.component('t-module-master-listing', {
    mixins: [AppListing],
    data: function data() {
        return {
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            },    
        }
    },
});