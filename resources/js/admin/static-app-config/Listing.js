import AppListing from '../app-components/Listing/AppListing';

Vue.component('static-app-config-listing', {
    mixins: [AppListing],
    data: function data() {
        return {
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            },    
        }
    },
    filters:{
        stringCount(string){
            return (string.length) > 100 ? string.substring(0, 100) + "....." : string;
        },
        stripHTML: function (str) {
            // console.log("string --->",str);
            return str.replace(/<\/?[^>]+(>|$)/g, "");
        },
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
        }
    },
    methods:{
        stripHTML: function (str) {
            // console.log("string --->",str);
            return str.replace(/<\/?[^>]+(>|$)/g, "");
        }
    }
});