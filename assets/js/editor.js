import EasyMDE from 'easymde';
import {Modal} from "bootstrap";

require('../../node_modules/inline-attachment/src/inline-attachment');
require('../../node_modules/inline-attachment/src/codemirror-4.inline-attachment');

/*
 * copied and modified from here: https://github.com/Ionaru/easy-markdown-editor/blob/master/src/js/easymde.js
 *
 * The MIT License (MIT)
 * Copyright (c) 2015 Sparksuite, Inc.
 * Copyright (c) 2017 Jeroen Akkerman.
 */
function insertLink(editor, url) {
    let cm = editor.codemirror;
    let stat = getState(cm);
    let options = editor.options;
    replaceSelection(cm, stat.link, options.insertTexts.link, url);
}

/*
 * copied from here: https://github.com/Ionaru/easy-markdown-editor/blob/master/src/js/easymde.js
 *
 * The MIT License (MIT)
 * Copyright (c) 2015 Sparksuite, Inc.
 * Copyright (c) 2017 Jeroen Akkerman.
 */
function getState(cm, pos) {
    pos = pos || cm.getCursor('start');
    let stat = cm.getTokenAt(pos);
    if (!stat.type) return {};

    let types = stat.type.split(' ');

    let ret = {},
        data, text;
    for (let i = 0; i < types.length; i++) {
        data = types[i];
        if (data === 'strong') {
            ret.bold = true;
        } else if (data === 'variable-2') {
            text = cm.getLine(pos.line);
            if (/^\s*\d+\.\s/.test(text)) {
                ret['ordered-list'] = true;
            } else {
                ret['unordered-list'] = true;
            }
        } else if (data === 'atom') {
            ret.quote = true;
        } else if (data === 'em') {
            ret.italic = true;
        } else if (data === 'quote') {
            ret.quote = true;
        } else if (data === 'strikethrough') {
            ret.strikethrough = true;
        } else if (data === 'comment') {
            ret.code = true;
        } else if (data === 'link') {
            ret.link = true;
        } else if (data === 'tag') {
            ret.image = true;
        } else if (data.match(/^header(-[1-6])?$/)) {
            ret[data.replace('header', 'heading')] = true;
        }
    }
    return ret;
}

/*
 * copied from here: https://github.com/Ionaru/easy-markdown-editor/blob/master/src/js/easymde.js
 *
 * The MIT License (MIT)
 * Copyright (c) 2015 Sparksuite, Inc.
 * Copyright (c) 2017 Jeroen Akkerman.
 */
function replaceSelection(cm, active, startEnd, url) {
    let text;
    let start = startEnd[0];
    let end = startEnd[1];
    let startPoint = {},
        endPoint = {};
    Object.assign(startPoint, cm.getCursor('start'));
    Object.assign(endPoint, cm.getCursor('end'));
    if (url) {
        start = start.replace('#url#', url);  // url is in start for upload-image
        end = end.replace('#url#', url);
    }
    if (active) {
        text = cm.getLine(startPoint.line);
        start = text.slice(0, startPoint.ch);
        end = text.slice(startPoint.ch);
        cm.replaceRange(start + end, {
            line: startPoint.line,
            ch: 0,
        });
    } else {
        text = cm.getSelection();
        cm.replaceSelection(start + text + end);

        startPoint.ch += start.length;
        if (startPoint !== endPoint) {
            endPoint.ch += start.length;
        }
    }
    cm.setSelection(startPoint, endPoint);
    cm.focus();
}

document.addEventListener('DOMContentLoaded', function(event) {
    document.querySelectorAll('[data-editor=markdown]').forEach(function(el) {
        if(el.classList.contains('md-textarea-hidden')) {
            /*
             * Somehow, when adding the markdown editor to an textarea,
             * this event is triggered again (with the hidden textarea)...
             */
            return;
        }

        if(el.getAttribute('data-preview') === null) {
            console.error('You must provide an URL which returns the markdown preview');
            return;
        }

        let previewUrl = el.getAttribute('data-preview');
        let modalSelector = el.getAttribute('data-modal');
        let linkUrl = el.getAttribute('data-modal-url');

        let options = {
            autoDownloadFontAwesome: false,
            autofocus: false,
            autosave: {
                enabled: false
            },
            element: el,
            placeholder: '',
            toolbar: [
                'bold', 'italic', 'heading', '|', 'unordered-list', 'ordered-list', '|', 'link',
                {
                    name: 'internal-link',
                    action: function(editor) {
                        if(modalSelector !== null && linkUrl !== null) {
                            let modal = new Modal(modalSelector);
                            modal.show();
                            let modalEl = document.querySelector(modalSelector);

                            let contentEl = modalEl.querySelector('.modal-content');
                            let iframe = document.createElement('iframe');
                            iframe.setAttribute('src', linkUrl);
                            iframe.setAttribute('height', '500');

                            contentEl.innerHTML = '';
                            contentEl.appendChild(iframe);

                            iframe.onload = function() {
                                iframe.contentWindow.document.querySelectorAll('a[data-insert-link]').forEach(function(el) {
                                    el.addEventListener('click', function(event) {
                                        event.preventDefault();

                                        let link = el.getAttribute('data-insert-link');
                                        insertLink(editor, link);

                                        modal.hide();
                                        contentEl.innerHTML = '';
                                    });
                                });
                            };
                        }
                    },
                    className: 'fas fa-external-link-alt',
                    title: 'Interner Verweise'
                },
                'image', '|', 'preview', 'side-by-side', 'fullscreen', '|', 'guide'
            ],
            previewRender: function(text, preview) {
                let request = new XMLHttpRequest();
                request.open('POST', previewUrl, true);
                request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

                request.onload = function() {
                    if(request.status >= 200 && request.status < 400) {
                        preview.innerHTML = request.responseText;
                    }
                };

                request.send(text);

                return 'Laden...';
            },
            spellChecker: false,
            status: false
        };

        let editor = new EasyMDE(options);

        if(el.getAttribute('data-upload') !== null) {
            if (el.getAttribute('data-upload') === "true" || el.getAttribute('data-upload') === 'data-upload') {
                let inlineAttachmentConfig = {
                    uploadUrl: el.getAttribute('data-url')
                };

                inlineAttachment.editors.codemirror4.attach(editor.codemirror, inlineAttachmentConfig);
            }
        }
    });
});
