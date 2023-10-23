import AppListing from "../app-components/Listing/AppListing";

Vue.component("t-training-tag-listing", {
    mixins: [AppListing],
    props:["session_search"],
    data: function data() {
        return {
            orderBy: {
                column: 'updated_at',
                direction: 'desc'
            },  
            search: '',  
        }
    },
    filters: {
        moment: function(date) {
            return moment(date).format("Do MMMM YYYY, h:mm:ss a");
        }
    },
    methods: {
        copyTag: function copyTag(url) {
            console.log("url-->" + url);
            var _this7 = this;

            this.$modal.show("dialog", {
                title: "Warning!",
                text: "Do you really want to copy this Assessment?",
                buttons: [
                    { title: "No, cancel." },
                    {
                        title:
                            '<span class="btn-dialog btn-danger">Yes, Copy.<span>',
                        handler: function handler() {
                            _this7.$modal.hide("dialog");
                            axios.get(url).then(
                                function(response) {
                                    _this7.loadData();
                                    _this7.$notify({
                                        type: "success",
                                        title: "Success!",
                                        text: response.data.message
                                            ? response.data.message
                                            : "Training Tag successfully Copied."
                                    });
                                },
                                function(error) {
                                    _this7.$notify({
                                        type: "error",
                                        title: "Error!",
                                        text: error.response.data.message
                                            ? error.response.data.message
                                            : "An error has occured."
                                    });
                                }
                            );
                        }
                    }
                ]
            });
        },
        getSerchFilter(ajax){
            if(ajax && ajax == 1){
                this.session_search = this.search;
            }else{
                this.search = this.session_search;
            }
        },
        idStore(id){
            localStorage.setItem('id1',id);
        },
        getId(){
            return localStorage.getItem('id1');
        }
    },
    beforeMount() {
        this.getSerchFilter(0);
        if(window.location.href != localStorage.getItem('url')){
            localStorage.setItem('scrollpos', '')
            localStorage.setItem('id1', '')
        }
    }
});
