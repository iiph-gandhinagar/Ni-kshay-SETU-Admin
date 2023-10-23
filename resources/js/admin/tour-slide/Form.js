import AppForm from '../app-components/Form/AppForm';
//import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import ClassicEditor from '../../admin/ckeditor';
import UploadAdapter from "../uploadadapter";

Vue.component('tour-slide-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                tour_id:  '' ,
                title:  this.getLocalizedFormDefaults() ,
                description:  this.getLocalizedFormDefaults() ,
                type:  '' ,
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
            mediaCollections: ["tour_video", "tour_image"]
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