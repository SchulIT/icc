import {
    ClassicEditor,
    Bold,
    Image,
    ImageUpload,
    Italic,
    Link,
    Paragraph,
    Strikethrough,
    Essentials,
    BlockQuote,
    List,
    Code,
    CodeBlock,
    Heading,
    Markdown,
    HorizontalLine,
    Emoji,
    Mention,
    Table,
    TableToolbar,
    SourceEditing,
    Plugin, ButtonView,
    IconLink
} from 'ckeditor5';

import {Modal} from "bootstrap";

import deTranslation from 'ckeditor5/translations/de.js';
import 'ckeditor5/ckeditor5.css';

class InsertInternalLink extends Plugin {
    init() {
        const editor = this.editor;

        editor.ui.componentFactory.add('internalLink', locale => {
            const button = new ButtonView(locale);
            const linkCommand = editor.commands.get('link');

            button.set({
                icon: IconLink
            })
            button.bind('isOn', 'isEnabled').to(linkCommand, 'value', 'isEnabled');
            button.bind('label').to(linkCommand, 'value');

            button.on('execute', () => {
                const modalUrl = editor.sourceElement.getAttribute('data-modal-url');
                const modalSelector = editor.sourceElement.getAttribute('data-modal');
                const $modal = document.querySelector(modalSelector);

                if($modal === null) {
                    console.error('No element found with selector: ' + modalSelector);
                    return;
                }

                const modal = new Modal(modalSelector);
                const $content = $modal.querySelector('.modal-content');
                const $iframe = document.createElement('iframe');
                $iframe.setAttribute('src', modalUrl);
                $iframe.setAttribute('height', '500');

                $content.innerHTML = '';
                $content.appendChild($iframe);

                $iframe.onload = function() {
                    for(const $a of $iframe.contentWindow.document.querySelectorAll('a[data-insert-link]')) {
                        $a.addEventListener('click', event => {
                            event.preventDefault();

                            const link = $a.getAttribute('data-insert-link');
                            const text = $a.getAttribute('data-insert-link-text');


                            editor.execute('link', link, {}, text);

                            modal.hide();
                            $content.innerHTML = '';
                        })
                    }
                }

                modal.show();
            });

            return button;
        });
    }
}

class ImageUploader {
    constructor(loader, editor) {
        this.loader = loader;
        this.editor = editor;
    }

    upload() {
        const url = this.editor.sourceElement.getAttribute('data-url');
        const csrfToken = this.editor.sourceElement.getAttribute('data-csrf-token');
        const csrfTokenParameter = this.editor.sourceElement.getAttribute('data-csrf-token-parameter');

        return this.loader.file
            .then(file => new Promise((resolve, reject) => {
                this.xhr = new XMLHttpRequest();
                this.xhr.open('POST', url, true);
                this.xhr.responseType = 'json';

                this.xhr.addEventListener('error', () => reject('Fehler beim Upload.'));
                this.xhr.addEventListener('abort', () => reject());
                this.xhr.addEventListener('load', () => {
                    const response = this.xhr.response;

                    if(!response || response.error) {
                        return reject(response && response.error ? response.error : 'Fehler beim Upload.');
                    }

                    resolve({
                        default: response.filename
                    });
                });

                if(this.xhr.upload) {
                    this.xhr.upload.addEventListener('progress', event => {
                        if(event.lengthComputable) {
                            this.loader.uploadTotal = event.total;
                            this.loader.uploaded = event.loaded;
                        }
                    });
                }

                const data = new FormData();
                data.append(csrfTokenParameter, csrfToken);
                data.append('file', file);

                this.xhr.send(data);
            }));
    }

    abort() {
        if(this.xhr) {
            this.xhr.abort();
        }
    }
}

function ImageUploaderPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        return new ImageUploader(loader, editor);
    }
}

for(let el of document.querySelectorAll('[data-editor=markdown]')) {
    let plugins = [
            Bold,
            Italic,
            Link,
            Paragraph,
            Strikethrough,
            Essentials,
            BlockQuote,
            List,
            Code,
            CodeBlock,
            Heading,
            Markdown,
            HorizontalLine,
            Emoji,
            Mention,
            Table,
            TableToolbar,
            SourceEditing
    ];

    if(el.getAttribute('data-upload') !== null) {
        if (el.getAttribute('data-upload') === "true" || el.getAttribute('data-upload') === 'data-upload') {
            plugins.push(Image);
            plugins.push(ImageUpload);
            plugins.push(ImageUploaderPlugin);
        }
    }

    if(el.getAttribute('data-modal-url') !== null) {
        if(el.getAttribute('data-modal') !== null) {
            plugins.push(InsertInternalLink);
        }
    }

    ClassicEditor.create(
        el,
        {
            licenseKey: 'GPL',
            plugins: plugins,
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'strikethrough', '|',
                'bulletedList', 'numberedList', '|',
                'internalLink', 'link', 'emoji', '|',
                'blockquote', 'insertTable', 'code', 'codeBlock', 'horizontalLine', '|',
                'sourceEditing', '|',
                'undo', 'redo',
            ],
            table: {
                defaultHeadings: { rows: 1 },
                contentToolbar: [ 'tableColumn', 'tableRow' ]
            },
            translations: [
                deTranslation
            ]
        }
    )
        .then(editor => {
            if(el.disabled) {
                editor.enableReadOnlyMode('editor');
            }
        })
        .catch(error => {
            console.error(error);
        });
}
