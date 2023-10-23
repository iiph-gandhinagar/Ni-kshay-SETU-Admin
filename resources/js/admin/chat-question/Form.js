import AppForm from '../app-components/Form/AppForm';
// import { editiorConfig } from '../utils';
import UploadAdapter from "../uploadadapter";
import ClassicEditor from '../../admin/ckeditor';

Vue.component('chat-question-form', {
    mixins: [AppForm],
    props: ["keywords","cadre"],
    data: function() {
        return {
            form: {
                question:  this.getLocalizedFormDefaults() ,
                answer:  this.getLocalizedFormDefaults() ,
                hit:  '' ,
                keyword_id: '',
                cadre_id: '',
                all_keywords: this.keywords,
                all_cadres: this.cadre,
                category:  '' ,
                activated:  false ,
                like_count:  '' ,
                dislike_count:  '' ,
                editor: null
            },
            editor: ClassicEditor,
            editorConfig: {
                
                // plugins: [
                    // HighlightEditing,
                    // Highlight,
                    // SourceEditing,
                    // RemoveFormatEditing
                    
                // ],
                // toolbar:{
                //     items: [
                //         'heading',
                //         '|',
                //         'bold',
                //         'italic',
                //         'link',
                //         'bulletedList',
                //         'numberedList',
                //         'highlight:yellowMarker', 'highlight:greenMarker', 'highlight:pinkMarker',
                //         'highlight:greenPen', 'highlight:redPen', 'removeHighlight',
                //         '|',
                //         'outdent',
                //         'indent',
                //         '|',
                //         'uploadImage',
                //         'blockQuote',
                //         'insertTable',
                //         'mediaEmbed',
                //         'undo',
                //         'redo',
                //         '|',
                //         'source',
                //         'removeFormat',
                //         'alignment',
                //         'SourceEditing'
                //     ],
                // },
                // alignment: {
                //     options: ['left', 'center', 'right']
                // },
                // heading: {
                //     options: [
                //         {
                //             model: "paragraph",
                //             title: "Paragraph",
                //             class: "ck-heading_paragraph"
                //         },
                //         {
                //             model: "heading1",
                //             view: "h1",
                //             title: "Heading 1",
                //             class: "ck-heading_heading1"
                //         },
                //         {
                //             model: "heading2",
                //             view: "h2",
                //             title: "Heading 2",
                //             class: "ck-heading_heading2"
                //         },
                //         {
                //             model: "heading3",
                //             view: "h3",
                //             title: "Heading 3",
                //             class: "ck-heading_heading3"
                //         },
                //         {
                //             model: "heading4",
                //             view: "h4",
                //             title: "Heading 4",
                //             class: "ck-heading_heading4"
                //         },
                //         { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                //         { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                //     ]
                // },
                // highlight: {
                //     options: [
                //         {
                //             model: 'greenMarker',
                //             class: 'marker-green',
                //             title: 'Green marker',
                //             color: 'rgb(25, 156, 25)',
                //             type: 'marker'
                //         },
                //         {
                //             model: 'yellowMarker',
                //             class: 'marker-yellow',
                //             title: 'Yellow marker',
                //             color: '#cac407',
                //             type: 'marker'
                //         },
                //         {
                //             model: 'redPen',
                //             class: 'pen-red',
                //             title: 'Red pen',
                //             color: 'hsl(343, 82%, 58%)',
                //             type: 'pen'
                //         }
                //     ]
                // },
                extraPlugins: [this.uploader],//
                language: "en",
            },
            // mediaWysiwygConfig: editiorConfig
        };
    },
    methods: {
        selectAll: function onClick() {
            if (this.form.cadre_id.length == this.cadre.length)
                this.form.cadre_id = [];
            else this.form.cadre_id = this.cadre.map(v => v.id);
        },
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
            let before = newForm.answer.en || '';
            let before_hi = newForm.answer.hi || '';
            let before_gu = newForm.answer.gu || '';
            let before_mr = newForm.answer.mr || '';
            let before_ta = newForm.answer.ta || '';
            let before_pa = newForm.answer.pa || '';
            let before_te = newForm.answer.te || '';
            let before_kn = newForm.answer.kn || '';
            this.form.answer.en = newForm.answer.en + "..";
            this.form.answer.hi = newForm.answer.hi + "..";
            this.form.answer.gu = newForm.answer.gu + "..";
            this.form.answer.mr = newForm.answer.mr + "..";
            this.form.answer.ta = newForm.answer.ta + "..";
            this.form.answer.pa = newForm.answer.pa + "..";
            this.form.answer.te = newForm.answer.te + "..";
            this.form.answer.kn = newForm.answer.kn + "..";
            setTimeout(() => {
                this.form.answer.en = before;
                this.form.answer.hi = before_hi;
                this.form.answer.gu = before_gu;
                this.form.answer.mr = before_mr;
                this.form.answer.ta = before_ta;
                this.form.answer.pa = before_pa;
                this.form.answer.te = before_te;
                this.form.answer.kn = before_kn;
            }, 100);
        }, 100);
    }
});