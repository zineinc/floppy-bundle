$(function($){

    var EventBus = function(){
        this._subscribers = {};
    };

    $.extend(EventBus.prototype, {
        on: function(name, subscriber){
            if(!this._subscribers[name]) {
                this._subscribers[name] = [];
            }
            this._subscribers[name].push(subscriber);

            return this;
        },
        off: function(name, subscriber){
            if(this._subscribers[name]) {
                for(var key in this._subscribers[name]) {
                    if(this._subscribers[name][key] === subscriber) {
                        this._subscribers[name].splice(key, 1);
                    }
                }
            }

            return this;
        },
        trigger: function(name, arg){
            if(this._subscribers[name]) {
                for(var key in this._subscribers[name]) {
                    this._subscribers[name][key](arg);
                }
            }

            return this;
        }
    });

    var assertNotEmpty = function(options, name){
        if(!options[name]) {
            throw "options '"+name+"' is required and cannot be null";
        }

        return options[name];
    };

    /**
     * Required options:
     *
     * * eventBus
     * * box: dom element wrapped by jQuery
     *
     * @param options
     * @constructor
     */
    var UploadingFileView = function(options){
        this._init(options);
    };

    $.extend(UploadingFileView.prototype, {
        _init: function(options){
            this._eventBus = assertNotEmpty(options, "eventBus");
            this._box = assertNotEmpty(options, "box");
            this._progressBar = this._box.find(".js-bar");
            this._nameBox = this._box.find(".js-name");
            this._file = null;
            this._visible = true;

            this._setVisibility(false);

            var stopButton = this._box.find(".js-stop");
            var that = this;

            stopButton.on("click", function(){
                if(that._file) {
                    that._eventBus.trigger("uploading-file:delete", that._file);
                }
            });

            this.init(options);
        },
        init: function(options){},
        progressUpdate: function(progress){

            this._setVisibility(true);

            var done = (progress === 100) ? function(){$(this).addClass('full')} : function(){};

            this._progressBar.animate({ width: progress+'%' }, {
                duration: 'long',
                done: done
            });
        },
        _setVisibility: function(visible) {
            if(visible) {
                if(!this._visible) {
                    this._box.show();
                    this._visible = true;
                }
            } else {
                if(this._visible) {
                    this._box.hide();
                    this._visible = false;
                }
            }
        },
        clear: function(){
            this._progressBar.queue(function(){
                $(this).css("width", "0%").removeClass('full').dequeue();
            });
            this._file = null;

            this._setVisibility(false);
            this._update();
        },
        _update: function(){
            this._nameBox.text(this._file ? this._file.name() : "");
        },
        setFile: function(newFile){
            this._file = newFile;

            this._update();
            this._setVisibility(true);
        }
    });

    /**
     * Required options:
     *
     * * eventBus
     * * box
     * * previewBox
     * * errorBox
     * * attributesInput
     * * idInput
     * * uploadingFileBox
     *
     * All except eventBus should be dom elements wrapped by jQuery
     *
     * @param options
     * @constructor
     */
    var View = function(options){
        this._init(options);
    };

    $.extend(View.prototype, {
        _init: function(options){
            var that = this;
            this._eventBus = assertNotEmpty(options, "eventBus");
            this._box = assertNotEmpty(options, "box");
            this._previewBox = assertNotEmpty(options, "previewBox");
            this._errorBox = assertNotEmpty(options, "errorBox");
            this._attributesInput = assertNotEmpty(options, "attributesInput");
            this._idInput = assertNotEmpty(options, "idInput");
            this._uploadingFileBox = assertNotEmpty(options, "uploadingFileBox");

            this._uploadingFileView = new UploadingFileView({ eventBus: this._eventBus, box: this._uploadingFileBox });

            this._box.delegate(".js-delete", "click", function(){
                that._eventBus.trigger("file:delete");
            });

            this.init(options);
        },
        init: function(options){},
        clear: function(){
            this._previewBox.html("");
            this._errorBox.text("");
            this._uploadingFileView.clear();
        },
        progressUpdate: function(progress){
            this._uploadingFileView.progressUpdate(progress);
        },
        showError: function(error){
            this._errorBox.text(error);
            this._uploadingFileView.clear();
        },
        showPreview: function(response){
            this._uploadingFileView.clear();
            this._previewBox.html(response);
        },
        setAttributes: function(attributes){
            this._attributesInput.val(JSON.stringify(attributes));
            this._idInput.val(attributes.id);
        },
        clearAttributes: function(){
            this._attributesInput.val("");
            this._idInput.val("");
        },
        setFile: function(file){
            this._uploadingFileView.setFile(file);
        },
        clearUploadingFile: function(file){
            this._uploadingFileView.clear();
        }
    });

    /**
     * Required options:
     *
     * * eventBus
     * * uploader: object with start() and stop(File) methods
     * * view: dom element wrapped by jQuery
     * * errorMessages: associative array of error messages
     * * previewUrl:  url to retrieve file preview
     *
     * @param options
     * @constructor
     */
    var Controller = function(options){
        this._init(options);
    };

    $.extend(Controller.prototype, {
        _init: function(options) {
            this._eventBus = assertNotEmpty(options, "eventBus");
            this._uploader = assertNotEmpty(options, "uploader");
            this._view = assertNotEmpty(options, "view");
            this._errorMessages = assertNotEmpty(options, "errorMessages");
            this._previewUrl = assertNotEmpty(options, "previewUrl");
            var that = this;

            this._eventBus.on("file:delete",function () {
                that._view.clear();
                that._view.clearAttributes();
            }).on("uploading-file:delete", function (file) {
                that.onUploadingFileDelete(file)
            });
            this.init(options);
        },
        init: function(options){},
        onFileAdded: function(file){
            this._view.clear();
            this._view.setFile(file);
            this._uploader.start();
        },
        onProgress: function(progress){
            this._view.progressUpdate(progress);
        },
        onError: function(response){
            if(typeof(response) !== "object") {
                response = JSON.parse(response);
            }
            var error = this._errorMessages[response.message] ? this._errorMessages[response.message] : response.message;
            this._view.showError(error);
        },
        onSuccess: function(response){
            var parsedResponse = JSON.parse(response);

            this._view.setAttributes(parsedResponse.attributes);
            var that = this;

            $.get(this._previewUrl, { fileId: parsedResponse.attributes.id }, function(response){
                that._view.showPreview(response);
            });
        },
        onUploadingFileDelete: function(file){
            this._view.clearUploadingFile(file);
            this._uploader.stop(file);
        }
    });

    var File = function(id, name){
        this.id = function(){
            return id;
        };

        this.name = function(){
            return name;
        };
    };

    window.StorageFileFormType = {
        View: View,
        UploadingFileView: UploadingFileView,
        Controller: Controller,
        EventBus: EventBus,
        File: File
    };
}(window.jQuery));