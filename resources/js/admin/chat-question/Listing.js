import AppListing from '../app-components/Listing/AppListing';

Vue.component('chat-question-listing', {
    mixins: [AppListing],
    props: ['all_cadres',"session_search"],
    filters: {
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
        }
    },
    data: function() {
        return {
            form: {
                all_cadres: this.all_cadres,
                select_category: '',
                select_keyword:'',
            },
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            }, 
            search: '',
        }
    }, 
    methods: {
        idStore(id){
            localStorage.setItem('id1',id);
        },
        getId(){
            return localStorage.getItem('id1');
        },
        getSerchFilter(ajax){
            if(ajax && ajax == 1){
                this.session_search = this.search;
            }else{
                this.search = this.session_search;
            }
        },
        getCadreNamesByIds: function onSuccess(item) {
            if(isNaN(item.cadre_id)){
                //array
                const splitted = item.cadre_id.split(',');
                // console.log('splitted',splitted);
                return this.all_cadres.filter(v=>splitted.includes(v.id.toString())).map(item=>item.title)
            }else{

                return this.all_cadres.filter(v=>v.id == item.cadre_id).map(item=>item.title)
            }
        },
        addTag: function addTag(url) {
            var _this7 = this;
    
            this.$modal.show('dialog', {
                title: 'Warning!',
                text: 'Do you really want to Add Question in Training Tag?',
                buttons: [{ title: 'No, cancel.' }, {
                    title: '<span class="btn-dialog btn-danger">Yes, Copy.<span>',
                    handler: function handler() {
                        _this7.$modal.hide('dialog');
                        axios.get(url).then(function (response) {
                            console.log("response--->",response);
                            _this7.loadData();
                            _this7.$notify({ type: 'success', title: 'Success!', text: response.data.message ? response.data.message : 'Question Added Successfully into Training Tag' });
                        }, function (error) {
                            console.log(error.response.status);
                            if(error.response.status == 400){
                                _this7.$notify({ type: 'error', title: 'Error!', text: error.response.data.message ? error.response.data.message : 'Question Length must be 50.' });
                            }else if(error.response.status == 409){
                                _this7.$notify({ type: 'error', title: 'Error!', text: error.response.data.message ? error.response.data.message : 'Tag must be unique' });
                            }
                            else{
                                _this7.$notify({ type: 'error', title: 'Error!', text: error.response.data.message ? error.response.data.message : 'An error has occured.' });
                            }
                           
                        });
                    }
                }]
            });
        },
    },
    beforeMount() {
        this.getSerchFilter(0);
        if(window.location.href != localStorage.getItem('url')){
            localStorage.setItem('scrollpos', '')
            localStorage.setItem('id1', '')
        }
    }
});
