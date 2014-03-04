$(function($){

    var EventBus = function(){

        var subscribers = {};

        this.on = function(name, subscriber){
            if(!subscribers[name]) {
                subscribers[name] = [];
            }
            subscribers[name].push(subscriber);
        };

        this.off = function(name, subscriber){
            if(subscribers[name]) {
                for(var key in subscribers[name]) {
                    if(subscribers[name][key] === subscriber) {
                        subscribers[name].splice(key, 1);
                    }
                }
            }
        }

        this.trigger = function(name, arg){
            if(subscribers[name]) {
                for(var key in subscribers[name]) {
                    subscribers[name][key](arg);
                }
            }
        };
    };

    var View = function(eventBus, box, previewBox, errorBox, attributesInput, idInput, progressBar){
        box.delegate(".delete", "click", function(){
            eventBus.trigger("delete");
        });

        this.clear = function(){
            previewBox.html("");
            errorBox.text("");
        };

        this.progressUpdate = function(progress){
            var done = (progress === 100) ? function(){$(this).addClass('full')} : function(){};

            progressBar.animate({ width: progress+'%' }, {
                duration: 'long',
                done: done
            });
        };

        this.showError = function(error){
            progressBar.css({width: '0%'}).removeClass('full');
            errorBox.text(error);
        };

        this.showPreview = function(response){
            previewBox.html(response);
        };

        this.setAttributes = function(attributes){
            attributesInput.val(JSON.stringify(attributes));
            idInput.val(attributes.id);
        };

        this.clearAttributes = function(){
            attributesInput.val("");
            idInput.val("");
        };
    };

    var Controller = function(eventBus, startUploaderCallback, view, errorMessages, previewUrl){

        eventBus.on("delete", function(){
            view.clear();
            view.clearAttributes();
        });

        this.onFileAdded = function(){
            view.clear();
            startUploaderCallback();
        };

        this.onProgress = function(progress){
            view.progressUpdate(progress);
        };

        this.onError = function(response){
            var parsedResponse = JSON.parse(response);
            var error = errorMessages[parsedResponse.message] ? errorMessages[parsedResponse.message] : parsedResponse.message;
            view.showError(error);
        };

        this.onSuccess = function(response){
            var parsedResponse = JSON.parse(response);

            view.setAttributes(parsedResponse.attributes);

            $.get(previewUrl, { fileId: parsedResponse.attributes.id }, function(response){
                view.showPreview(response);
            });
        };
    };

    window.StorageFileFormType = {
        View: View,
        Controller: Controller,
        EventBus: EventBus
    };
}(window.jQuery));