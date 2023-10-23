import AppListing from '../app-components/Listing/AppListing';

Vue.component('flash-news-listing', {
    mixins: [AppListing],
    filters: {
        stringCount(string){
            if(string != null && string != ""){
                return (string.length) > 30 ? string.substring(0, 30) + "....." : string;
            }else{
                return string;
            }
        },
        stripHTML: function (str) {
            // console.log("string --->",str);
            if(str != "" && str != null){
                return str.replace(/<\/?[^>]+(>|$)/g, "");
            }
        },
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
        }
    },
    data: function data() {
        return {
            orderBy: {
                column: 'id',
                direction: 'desc'
            }, 
            form:{
                from_date: "",
                to_date:"",
            }   
        }
    },
    methods:{
        stripHTML: function (str) {
            // console.log("string --->",str);
            return str.replace(/<\/?[^>]+(>|$)/g, "");
        },
        idStore(id){
            localStorage.setItem('id1',id);
        },
        getId(){
            return localStorage.getItem('id1');
        }
    },
    beforeMount() {
        if(window.location.href != localStorage.getItem('url')){
            localStorage.setItem('scrollpos', '')
            localStorage.setItem('id1', '')
        }
    }
});