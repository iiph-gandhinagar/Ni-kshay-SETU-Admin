import AppForm from "../app-components/Form/AppForm";
// import ClassicEditor from "../ckeditor";
import UploadAdapter from "../uploadadapter";
import ClassicEditor from '../../admin/ckeditor';
// import Highlight from "@ckeditor/ckeditor5-highlight/src/highlight";
// import EssentialsPlugin from '@ckeditor/ckeditor5-essentials/src/essentials';
// import DecoupledEditor from '@ckeditor/ckeditor5-build-decoupled-document';

Vue.component("static-blog-form", {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                active: false,
                author: "",
                description: this.getLocalizedFormDefaults(),
                // description: '',
                keywords: "",
                order_index: "",
                short_description: this.getLocalizedFormDefaults(),
                slug: "",
                source: "",
                title: this.getLocalizedFormDefaults(),
                keywords: [],
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
            options: [],
            mediaCollections: [
                "blog_thumb_image1",
                "blog_thumb_image2",
                "blog_thumb_image3"
            ]
        };
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

        addTag: function(newTag) {
            console.log(this.form.keywords);
            let tag_repeated = -1;
            if (this.form.keywords.length > 0 && this.form.keywords != "") {
                tag_repeated = this.form.keywords.findIndex(
                    item => $.trim(newTag).toLowerCase() === item.toLowerCase()
                );
            }

            console.log(tag_repeated);
            if (newTag.includes("|")) {
                alert('Please Add keywords without "|" ');
            } else if (tag_repeated != -1) {
                alert(newTag + " keywords is Repated!!");
            } else {
                this.options.push($.trim(newTag));
                this.form.keywords.push($.trim(newTag));
            }
        },
        getTitle() {
            // this.editorData = this.form.description.en;
            let title = this.form.title;
            this.form.slug = title.en.replaceAll(" ", "-");
        }
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
