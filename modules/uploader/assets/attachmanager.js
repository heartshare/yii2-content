/*
 Extend image upload plugin for work with pictures & thumbnails
 (c)Insolita
 */
if (typeof RedactorPlugins === 'undefined') var RedactorPlugins = {};

RedactorPlugins.attachmanager = {
    init: function () {
        this.opts.langs['ru'] = $.extend({
            attachmanager: 'Менеджер Вложений',
            configerror: 'Некорректная конфигурация плагина attachmanager отсутствует параметр ',
            hzerror: 'Неизвестная ошибка',
            deleteError: 'Ошибка, файл не удалён',
            download: 'Скачать',
            preview: 'Предварительный просмотр'
        }, this.opts.langs['ru']);
        this.opts.langs['en'] = $.extend({
            attachmanager: 'Attach Manager',
            hzerror: 'Unrecognized error',
            deleteError: 'Oops, can`t remove this file',
            configerror: 'Wrong configuration attachmanager not set variable ',
            download: 'Download',
            preview: 'Preview'
        }, this.opts.langs['en']);

        this.opts = $.extend({
            managerUrl: false,
            previewUrl: false,
            deleteUrl: false,
            imageListUrl: false,
            fileListUrl: false,
            imageUploadUrl: false,
            fileUploadUrl: false,
            awesomeIcon: 'fa-cloud-upload',
            bbinsertAllow: true,
            htmlinsertAllow: true,
            leftAlignStyle: false,
            rightAlignStyle: false,
            imageHtmlTemplate: '<img src="{imgurl}" alt="{alt}" class="{cssclass}" style="{align}">',
            imagePreviewTemplate: '<a href="{fullurl}" rel="imagelink" class="imagelink" title="{alt}"><img src="{imgurl}" alt="{alt}" class="{cssclass}" style="{align}"></a>',
            imageBBTemplate: '[[{"ATTACH_IMAGE":"{attach_id}","PREVIEW":"{preview}","CSS":"{cssclass}","ALIGN":"{align}","SIZE":"{size}","ALT":"{alt}"}]]',
            fileBBTemplate: '[[{"ATTACH_FILE":"{attach_id}"}]]',
            fileHTMLTemplate: '<a href="{fileurl}"  class="filelink">{dwnld}: {filetitle}({filesize})</a>',
            modalClosedCallback: $.proxy(this.destroyManager, this),
            pjaxTimeout: 10000
        }, this.opts);
        this.opts.curLang = this.opts.langs[this.opts.lang];
        this.buttonAdd('attachmanager', this.opts.curLang.attachmanager, this.showManager);
        this.buttonAwesome('attachmanager', this.opts.awesomeIcon);
        if (this.opts.bbinsertAllow) {
            this.buttonAdd('preview', this.opts.curLang.preview, this.showPreview);
            this.buttonAwesome('preview', 'fa-eye');
        }
    },
    showPreview: function () {
        this.sync();
        this.observeImages();
        this.observeLinks();

        var imgManage = $.proxy(this.initManager, this);
        if (!this.opts.previewUrl) {
            alert(this.opts.curLang.configerror + 'previewUrl');
        } else {
            var self = this;
            var curText = this.get();
            $.post(this.opts.previewUrl, {text: curText}, function (data) {
                self.modalInit(self.opts.curLang.preview, data, 700);
            });
        }
    },
    showManager: function () {
        this.selectionSave();
        var imgManage = $.proxy(this.initManager, this);
        if (!this.opts.managerUrl) {
            alert(this.opts.curLang.configerror + 'managerUrl');
        } else {
            var self = this;
            $.get(this.opts.managerUrl, function (data) {
                self.modalInit(self.opts.curLang.attachmanager, data, 700, imgManage);
            });
        }
    },
    destroyManager: function () {
        this.selectionRestore();
        if (this.opts.htmlinsertAllow) {
            $(document).off('click', '[data-redactor_inshtml_img]');
            $(document).off('click', '[data-redactor_inshtml_file]');
        }
        if (this.opts.bbinsertAllow) {
            $(document).off('click', '[data-redactor_insbb_img]');
            $(document).off('click', '[data-redactor_insbb_file]');
        }
        $(document).off('click', '[data-redactor_listedimg]');
        $(document).off('click', '[data-redactor_file_row]');
        $(document).off('click.pjax');
        $(document).off('click', '#redactor_attach_uplimage');
        $(document).off('click', '#redactor_attach_uplfile');
        if (this.opts.deleteUrl) {
            $(document).off('click', '[data-redactor_imgdeleter]');
            $(document).off('click', '[data-redactor_filedeleter]');
        }
    },
    initManager: function () {
        var self = this;
        $(document).on('click', '[data-redactor_listedimg]', function (e) {
            e.preventDefault();
            console.log(this.getCurrent);
            $(this).popover({html: true, placement: 'right', container: '#redactor-modal-image-manager'});
        });
        $(document).on('click', '[data-redactor_file_row]', function (e) {
            e.preventDefault();
            console.log(this.getCurrent);
            $(this).popover({html: true, placement: 'bottom', container: '#redactor-modal-image-manager'});
        });
        if (this.opts.deleteUrl) {
            $(document).on('click', '[data-redactor_imgdeleter]', function (e) {
                e.preventDefault();
                var id = $(this).data('redactor_imgdeleter');
                $('[data-redactor_listedimg]').popover('hide');
                $.post(self.opts.deleteUrl, {id: id}, function (data) {
                    if (data.state)
                        $.pjax.reload({container: '#redactor_imagepjax', timeout: self.opts.pjaxTimeout, url: self.opts.imageListUrl, push: false, replace: false, scrollTo: '#redactor_imagepjax'});
                    else
                        alert(self.opts.curLang.deleteError);
                });
            });
            $(document).on('click', '[data-redactor_filedeleter]', function (e) {
                e.preventDefault();
                var id = $(this).data('redactor_filedeleter');
                $('[data-redactor_file_row]').popover('hide');
                $.post(self.opts.deleteUrl, {id: id}, function (data) {
                    if (data.state)
                        $.pjax.reload({container: '#redactor_filepjax', timeout: self.opts.pjaxTimeout, url: self.opts.fileListUrl, push: false, replace: false, scrollTo: '#redactor_filepjax'});
                    else
                        alert(self.opts.curLang.deleteError);
                });
            });
        }
        if (this.opts.htmlinsertAllow) {
            $(document).on('click', '[data-redactor_inshtml_img]', $.proxy(function (e) {
                e.preventDefault();
                var targetid = $(e.target).data('redactor_inshtml_img');
                self.imageInsertHTML(e, targetid);
            }, self));
            $(document).on('click', '[data-redactor_inshtml_file]', $.proxy(function (e) {
                e.preventDefault();
                var targetid = $(e.target).data('redactor_inshtml_file');
                self.fileInsertHTML(e, targetid);
            }, self));
        }
        if (this.opts.bbinsertAllow) {
            $(document).on('click', '[data-redactor_insbb_img]', $.proxy(function (e) {
                e.preventDefault();
                var targetid = $(e.target).data('redactor_insbb_img');
                self.imageInsertBB(e, targetid);
            }, self));
            $(document).on('click', '[data-redactor_insbb_file]', $.proxy(function (e) {
                e.preventDefault();
                var targetid = $(e.target).data('redactor_insbb_file');
                self.fileInsertBB(e, targetid);
            }, self));
        }
        this.initAttachUploader();

    },
    initAttachUploader: function () {
        var img_input = $('#redactor_uplimage');
        var file_input = $('#redactor_uplfile');
        this.imguplform = $(img_input[0].form);
        this.fileuplform = $(file_input[0].form);

        this.imguplform_action = this.imguplform.attr('action');
        this.fileuplform_action = this.fileuplform.attr('action');
        this.attachOptions = {
            img_trigger: 'redactor_attach_uplimage',
            img_input: img_input,
            img_success: $.proxy(function (obj, json) {
                this.imageUploadSuccess(obj, json);
            }, this),
            img_error: $.proxy(function (obj, json) {
                this.imageUploadFail(obj, json);
            }, this),
            file_trigger: 'redactor_attach_uplfile',
            file_input: file_input,
            file_success: $.proxy(function (obj, json) {
                this.fileUploadSuccess(obj, json);
            }, this),
            file_error: $.proxy(function (obj, json) {
                this.fileUploadFail(obj, json);
            }, this)
        };

        $(document).on('click', '#' + this.attachOptions.img_trigger, $.proxy(function (e) {
            e.preventDefault();
            this.imguplform.submit(function (e) {
                return false;
            });
            this.attachSubmit('image');

        }, this));
        $(document).on('click', '#' + this.attachOptions.file_trigger, $.proxy(function (e) {
            e.preventDefault();
            this.fileuplform.submit(function (e) {
                return false;
            });
            this.attachSubmit('file');

        }, this));
    },
    attachReinit: function () {
        $(document).off('click', '#' + this.attachOptions.file_trigger);
        $(document).off('click', '#' + this.attachOptions.img_trigger);
        this.initAttachUploader()
    },
    attachSubmit: function (type) {
        this.showProgressBar();
        console.log('attachSubmit running');
        if (type == 'file') {
            this.attachForm(this.imguplform, type, this.attachFrame(type));
        } else {
            this.attachForm(this.fileuplform, type, this.attachFrame(type));
        }
        console.log('attachSubmit end');
    },
    attachFrame: function (type) {
        console.log('attachFrame running');
        this.attachtype = type;
        this.uplframeid = 'f' + Math.floor(Math.random() * 99999);
        var d = this.document.createElement('div');
        var iframe = '<iframe style="display:none" id="' + this.uplframeid + '" name="' + this.uplframeid + '"></iframe>';
        d.innerHTML = iframe;
        $(d).appendTo("body");
        $('#' + this.uplframeid).load($.proxy(this.attachLoaded, this));
        console.log(this.uplframeid);
        return this.uplframeid;
    },
    attachForm: function (f, type, name) {
        console.log('attachForm running for type=' + type);
        var formId = 'redactor_' + type + 'UploadForm' + this.uplframeid,
            fileId = 'redactor_' + type + 'UploadFile' + this.uplframeid;
        var formUrl = (type == 'file') ? this.fileuplform_action : this.imguplform_action;
        this.fakedform = $('<form  action="' + formUrl + '" method="POST" target="' + name + '" name="' + formId + '" id="' + formId + '" enctype="multipart/form-data" />');
        if (this.opts.uploadFields !== false && typeof this.opts.uploadFields === 'object') {
            $.each(this.opts.uploadFields, $.proxy(function (k, v) {
                if (v != null && v.toString().indexOf('#') === 0) v = $(v).val();
                var hidden = $('<input/>', {
                    'type': "hidden",
                    'name': k,
                    'value': v
                });
                $(this.fakedform).append(hidden);
            }, this));
        }

        var hidden = '';
        if (type == 'file') {
            this.fileuplform.find('input').each(function (el) {
                if ($(this).attr('id') !== 'undefined') {
                    hidden += '<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '">';
                }
            });
            $(this.fakedform).append(hidden);
        } else {
            this.imguplform.find('input').each(function (el) {
                if ($(this).attr('id') !== 'undefined') {
                    hidden += '<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '">';
                }
            });
            $(this.fakedform).append(hidden);
        }

        var oldElement = (type == 'file') ? this.attachOptions.file_input : this.attachOptions.img_input;
        var newElement = $(oldElement).clone();
        $(oldElement).attr('id', fileId).before(newElement).appendTo(this.fakedform);
        $(this.fakedform).css('position', 'absolute')
            .css('top', '-2000px')
            .css('left', '-2000px')
            .appendTo('body');
        this.fakedform.submit();
        console.log('fakedform submitted');
    },
    attachLoaded: function () {
        console.log('attachLoaded runned for type=' + this.attachtype);
        var i = $('#' + this.uplframeid)[0], d;
        if (i.contentDocument) d = i.contentDocument;
        else if (i.contentWindow) d = i.contentWindow.document;
        else d = window.frames[this.uplframeid].document;
        this.hideProgressBar();
        if (typeof d !== 'undefined') {
            console.log('typeof d !== undefined');
            var rawString = d.body.innerHTML;
            var jsonString = rawString.match(/\{(.|\n)*\}/)[0];
            jsonString = jsonString.replace(/^\[/, '');
            jsonString = jsonString.replace(/\]$/, '');
            console.log(rawString);
            console.log(jsonString);
            var json = $.parseJSON(jsonString);
            $(this.fakedform).remove();
            this.attachReinit();
            if (this.attachtype == 'file') {
                (typeof json.error == 'undefined') ? this.attachOptions.file_success(this, json) : this.attachOptions.file_error(this, json);
            } else {
                (typeof json.error == 'undefined') ? this.attachOptions.img_success(this, json) : this.attachOptions.img_error(this, json);
            }
        }
        else {
            alert('Upload failed!');
        }
    },
    imageUploadSuccess: function (obj, data) {
        console.log(this.opts.imageListUrl);
        $('#redactor_imgupload_error').html('');
        $('#redactor_imgupload_error').hide();
        $.pjax.reload({container: '#redactor_imagepjax', timeout: this.opts.pjaxTimeout, url: this.opts.imageListUrl, push: false, replace: false, scrollTo: '#redactor_imagepjax'});

    },
    imageUploadFail: function (obj, data) {
        if (data.error) {
            $('#redactor_imgupload_error').html('');
            $('#redactor_imgupload_error').append(data.error);
            $('#redactor_imgupload_error').show();
        } else {
            $('#redactor_imgupload_error').html(this.opts.curLang.hzerror);
            $('#redactor_imgupload_error').show();
        }
    },

    fileUploadSuccess: function (obj, data) {
        $('#redactor_fileupload_error').html('');
        $('#redactor_fileupload_error').hide();
        $.pjax.reload({container: '#redactor_filepjax', timeout: this.opts.pjaxTimeout, url: this.opts.fileListUrl, push: false, replace: false, scrollTo: '#redactor_filepjax'});

    },
    fileUploadFail: function (obj, data) {
        $('#redactor_fileupload_error').html('');
        if (data.error) {
            $('#redactor_fileupload_error').append(data.error);
            $('#redactor_fileupload_error').show();
        } else {
            $('#redactor_fileupload_error').html(this.opts.curLang.hzerror);
            $('#redactor_fileupload_error').show();
        }
    },
    imageInsertBB: function (e, targetid) {
        var size = $('#redactor_imgsize' + targetid).val().replace(/'|"|>|</g, '');
        var alt = $('#redactor_img_alt' + targetid).val().replace(/'|"|>|</g, '');
        var css = $('#redactor_img_css' + targetid).val().replace(/'|"|>|</g, '');
        var align = $('#redactor_imgalign' + targetid).val().replace(/'|"|>|</g, '');
        var ispreview = $('#redactor_ispreview' + targetid)[0].checked;
        console.log(this.opts.imageBBTemplate);
        var insbb = this.opts.imageBBTemplate.replace('{attach_id}', targetid)
            .replace('{preview}', ispreview)
            .replace('{align}', align)
            .replace('{cssclass}', css)
            .replace('{alt}', alt)
            .replace('{size}', size);
        console.log(insbb);
        this.bufferSet();
        this.execCommand('inserttext', insbb, true);
        this.modalClose();
    },
    fileInsertBB: function (e, targetid) {
        console.log(this.opts.fileBBTemplate);
        var insbb = this.opts.fileBBTemplate.replace('{attach_id}', targetid);
        this.selectionRestore();
        this.bufferSet();
        this.execCommand('inserttext', insbb, true);
        this.modalClose();
        this.observeImages();
        this.observeLinks();
    },
    imageInsertHTML: function (e, targetid) {
        var size = $('#redactor_imgsize' + targetid).val().replace(/'|"|>|</g, '');
        var alt = $('#redactor_img_alt' + targetid).val().replace(/'|"|>|</g, '');
        var css = $('#redactor_img_css' + targetid).val().replace(/'|"|>|</g, '');
        var align = $('#redactor_imgalign' + targetid).val().replace(/'|"|>|</g, '');
        if (align == 'right') align = this.opts.rightAlignStyle;
        else if (align == 'left') align = this.opts.leftAlignStyle;
        else align = '';

        var ispreview = $('#redactor_ispreview' + targetid)[0].checked;
        var imgurl = $('#redactor_images_data_' + targetid).data('url_' + size);
        var fullurl = $('#redactor_images_data_' + targetid).data('url_orig');

        (ispreview) ? console.log(this.opts.imagePreviewTemplate) : console.log(this.opts.imageHtmlTemplate);
        var imgcode = (ispreview) ? this.opts.imagePreviewTemplate : this.opts.imageHtmlTemplate;
        imgcode = imgcode.replace('{attach_id}', targetid)
            .replace('{align}', align)
            .replace('{cssclass}', css)
            .replace('{alt}', alt)
            .replace('{size}', size)
            .replace('{imgurl}', imgurl)
            .replace('{fullurl}', fullurl);
        console.log(imgcode);
        this.bufferSet();
        this.execCommand('inserthtml', imgcode, true);
        this.modalClose();
        this.observeImages();
        this.observeLinks();
    },
    fileInsertHTML: function (e, targetid) {
        var fileurl = $('#redactor_file_data_' + targetid).data('url_file');
        var filetitle = $('#redactor_file_title_' + targetid).html();
        var filesize = $('#redactor_file_size_' + targetid).html();
        var inshtml = this.opts.fileHtmlTemplate;
        console.log(this.opts.fileHtmlTemplate);
        this.selectionRestore();
        inshtml = inshtml.replace('{attach_id}', targetid)
            .replace('{fileurl}', fileurl)
            .replace('{filetitle}', filetitle)
            .replace('{filesize}', filesize)
            .replace('{dwnld}', this.opts.curLang.download);
        this.bufferSet();
        this.execCommand('inserthtml', inshtml, true);
        this.modalClose();
        this.observeImages();
        this.observeLinks();
    }
}
