import AppListing from '../app-components/Listing/AppListing';

Vue.component('assessment-question-listing', {
    mixins: [AppListing],
    props:["session_search"],
    data: function() {
        return {
            form: {
                select_assessment: '',
                select_category:'',
            },
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            }, 
            search: '',
        }
    }, 
    methods:{
        getSerchFilter(ajax){
            if(ajax && ajax == 1){
                this.session_search = this.search;
            }else{
                this.search = this.session_search;
            }
        },
        copy: function copy(url) {
            // var _lodash = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
            var _this5 = this;

            var itemsToDelete = (0, _.keys)((0, _.pickBy)(this.bulkItems));
            var self = this;
            console.log("inside copy function --->");
            this.$modal.show('dialog', {
                title: 'Warning!',
                text: 'Do you really want to Copy ' + this.clickedBulkItemsCount + ' selected items ?',
                buttons: [{ title: 'No, cancel.' }, {
                    title: '<span class="btn-dialog btn-danger">Yes, Copy.<span>',
                    handler: function handler() {
                        _this5.$modal.hide('dialog');
                        window.location = `/admin/assessments/create?ids=${itemsToDelete}`;
                    }
                }]
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
        this.getSerchFilter(0);
        if(window.location.href != localStorage.getItem('url')){
            localStorage.setItem('scrollpos', '')
            localStorage.setItem('id1', '')
        }
    }
});