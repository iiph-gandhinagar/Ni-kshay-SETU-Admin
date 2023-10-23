import AppListing from '../app-components/Listing/AppListing';

Vue.component('key-feature-listing', {
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
        stripHTML: function (str) {
            // console.log("string --->",str);
            return str.replace(/<\/?[^>]+(>|$)/g, "");
        },
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
        }
    },
});