import AppListing from '../app-components/Listing/AppListing';

Vue.component('module-mapping-to-name-listing', {
    mixins: [AppListing],
    data: function data() {
        return {
            orderBy: {
                column: 'id',
                direction: 'desc'
            },    
        }
    },
});