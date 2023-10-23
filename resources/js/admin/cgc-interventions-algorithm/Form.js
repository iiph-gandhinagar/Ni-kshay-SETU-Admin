import AppForm from '../app-components/Form/AppForm';
import { editiorConfig } from "../utils";
//import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import ClassicEditor from '../../admin/ckeditor';
import UploadAdapter from "../uploadadapter";

Vue.component('cgc-interventions-algorithm-form', {
    mixins: [AppForm],
    props: ["cadre", "state"],
    data: function() {
        let uri = window.location.search.substring(1);
        let params = new URLSearchParams(uri);
        return {
            form: {
                title:  this.getLocalizedFormDefaults() ,
                node_type:  '' ,
                is_expandable:  false ,
                has_options:  false ,
                parent_id:  params.get("master"),
                description:  this.getLocalizedFormDefaults() ,
                time_spent:  '' ,
                index:  '' ,
                redirect_algo_type:  '' ,
                redirect_node_id:  '' ,
                header:  this.getLocalizedFormDefaults(),
                sub_header:  this.getLocalizedFormDefaults(),
                activated:  false ,
                master_node_id:  params.get("master_node_id"),
                state_id:  '' ,
                cadre_id:  '' ,
                all_states: this.state,
                all_cadres: this.cadre,
                editor: null
            },
            editor: ClassicEditor,
            editorConfig: {
                // The configuration of the editor.
                // toolbar: [
                //     "bold",
                //     "italic",
                //     "underline",
                //     "strikethrough",
                //     "subscript",
                //     "superscript",
                //     "|",
                //     "headings",
                //     "|",
                //     "undo",
                //     "redo"
                // ],
                // plugins: [
                //     EssentialsPlugin,
                // ],
                heading: {
                    options: [
                        {
                            model: "paragraph",
                            title: "Paragraph",
                            class: "ck-heading_paragraph"
                        },
                        {
                            model: "heading1",
                            view: "h1",
                            title: "Heading 1",
                            class: "ck-heading_heading1"
                        },
                        {
                            model: "heading2",
                            view: "h2",
                            title: "Heading 2",
                            class: "ck-heading_heading2"
                        },
                        {
                            model: "heading3",
                            view: "h3",
                            title: "Heading 3",
                            class: "ck-heading_heading3"
                        },
                        {
                            model: "heading4",
                            view: "h4",
                            title: "Heading 4",
                            class: "ck-heading_heading4"
                        }
                    ]
                },
                extraPlugins: [this.uploader],
                language: "en"
            },
            mediaCollections: ["node_icon"],
            mediaWysiwygConfig: editiorConfig
        }
    },
    methods: {
        uploader(editor) {
            console.log("inside uploader function");
            editor.plugins.get(
                "FileRepository"
            ).createUploadAdapter = loader => {
                return new UploadAdapter(loader);
            };
        },
        selectAll: function onSuccess() {
            if (this.form.cadre_id.length == this.cadre.length)
                this.form.cadre_id = [];
            else this.form.cadre_id = this.cadre.map(v => v.id);
        },
        
        selectAllStates: function onSuccess() {
            if (this.form.state_id.length == this.form.all_states.length) {
                this.form.state_id = [];
            } else {
                this.form.state_id = this.form.all_states.map(v => v.id);
            }
        },
    },
    mounted() {
        setTimeout(() => {
            const newForm = Object.assign({}, this.form);
            let before = newForm.description.en || '';
            let before_hi = newForm.description.hi || '';
            let before_gu = newForm.description.gu || '';
            let before_mr = newForm.description.mr || '';
            let before_ta = newForm.description.ta || '';
            let before_pa = newForm.description.pa || '';
            let before_te = newForm.description.te || '';
            let before_kn = newForm.description.kn || '';
            this.form.description.en = newForm.description.en + "..";
            this.form.description.hi = newForm.description.hi + "..";
            this.form.description.gu = newForm.description.gu + "..";
            this.form.description.mr = newForm.description.mr + "..";
            this.form.description.ta = newForm.description.ta + "..";
            this.form.description.pa = newForm.description.pa + "..";
            this.form.description.te = newForm.description.te + "..";
            this.form.description.kn = newForm.description.kn + "..";
            setTimeout(() => {
                this.form.description.en = before;
                this.form.description.hi = before_hi;
                this.form.description.gu = before_gu;
                this.form.description.mr = before_mr;
                this.form.description.ta = before_ta;
                this.form.description.pa = before_pa;
                this.form.description.te = before_te;
                this.form.description.kn = before_kn;
            }, 100);
        }, 100);
    }

});