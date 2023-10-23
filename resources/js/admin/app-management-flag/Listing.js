import AppListing from '../app-components/Listing/AppListing';

Vue.component('app-management-flag-listing', {
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