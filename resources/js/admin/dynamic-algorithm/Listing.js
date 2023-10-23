import AppListing from '../app-components/Listing/AppListing';

Vue.component('dynamic-algorithm-listing', {
    mixins: [AppListing],
    methods:{
        clearSession() {
            console.log("inside clear session");
            axios.get("/admin/dynamic-algorithms").then(result => {
                console.log("session closed");
            });
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