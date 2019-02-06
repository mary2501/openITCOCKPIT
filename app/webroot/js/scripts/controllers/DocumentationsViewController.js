angular.module('openITCOCKPIT')
    .controller('DocumentationsViewController', function($scope, $sce, $http, QueryStringService, MassChangeService, NotyService, BBParserService, $stateParams) {

        $scope.uuid = $stateParams.uuid;
        $scope.type = $stateParams.type;

        $scope.contentView = "";
        $scope.hyperlink = "";
        $scope.hyperlinkDescription = "";
        $scope.displayView = true;

        $scope.load = function() {
            $http.get("/documentations/view/" + $scope.uuid + "/" + $scope.type + ".json", {
                params: {
                    'angular': true
                }
            }).then(function(result) {
                $scope.host = result.data.host;
                $scope.service = result.data.service;
                $scope.post = result.data.post;
                $scope.docuExists = result.data.docuExists;

                if ($scope.docuExists && $scope.post.Documentation.content !== null) {
                    $scope.contentView = $sce.trustAsHtml(BBParserService.parse($scope.post.Documentation.content));
                }

                if ($scope.post.length <= 0) {
                    $scope.post = {
                        Documentation: {
                            uuid: $scope.uuid,
                            content: null,
                        }
                    };
                }

                if ($scope.host.Host.id) {
                    $scope.id = $scope.host.Host.id;
                    $http.get("/hosts/hostBrowserMenu/" + $scope.host.Host.id + ".json", {
                        params: {
                            'angular': true
                        }
                    }).then(function(result) {
                        $scope.host = result.data.host;

                        $scope.hostBrowserMenu = {
                            hostId: $scope.host.Host.id,
                            hostUuid: $scope.host.Host.uuid,
                            allowEdit: $scope.host.Host.allowEdit,
                            hostUrl: $scope.host.Host.host_url_replaced,
                            docuExists: result.data.docuExists,
                            isHostBrowser: false
                        };
                    });
                }

            }, function errorCallback (result) {
                if (result.status === 404) {
                    window.location.href = '/angular/not_found';
                }
            });


        };

        $scope.saveText = function(action) {
            if (typeof action === 'undefined') {
                action = 'add_or_edit';
            }
            $scope.post.Documentation.content = $('#docuText').val();

            if ($scope.post.Documentation.content !== null && typeof $scope.post.Documentation.content !== 'undefined') {

                $http.post("/documentations/view/" + $scope.uuid + "/" + $scope.type + ".json?angular=true",
                    $scope.post
                ).then(function(result) {
                    $scope.errors = {};

                    genericSuccess();
                }, function errorCallback (result) {
                    if (result.data.hasOwnProperty('error')) {
                        $scope.errors = result.data.error;
                    }
                    genericError();
                });

                $scope.docuExists = true;
            }
        };

        $scope.rebuildContentView = function() {
            var content = $('#docuText').val();
            if (content !== null && typeof content !== 'undefined') {
                $scope.contentView = $sce.trustAsHtml(BBParserService.parse(content));
            }
        };

        $scope.showView = function() {
            $scope.displayView = true;
            $scope.rebuildContentView();
        };

        $scope.showEdit = function() {
            $scope.displayView = false;
        };

        var genericSuccess = function() {
            new Noty({
                theme: 'metroui',
                type: 'success',
                text: 'Data saved successfully',
                timeout: 3500
            }).show();
        };

        var genericError = function() {
            new Noty({
                theme: 'metroui',
                type: 'error',
                text: 'Error while saving data',
                timeout: 3500
            }).show();
        };


        $scope.load();


        //jQuery Bases WYSIWYG Editor
        $("[wysiwyg='true']").click(function() {
            var $textarea = $('#docuText');
            var task = $(this).attr('task');
            switch (task) {
                case 'bold':
                    $textarea.surroundSelectedText('[b]', '[/b]');
                    break;

                case 'italic':
                    $textarea.surroundSelectedText('[i]', '[/i]');
                    break;

                case 'underline':
                    $textarea.surroundSelectedText('[u]', '[/u]');
                    break;

                case 'left':
                    $textarea.surroundSelectedText('[left]', '[/left]');
                    break;

                case 'center':
                    $textarea.surroundSelectedText('[center]', '[/center]');
                    break;

                case 'right':
                    $textarea.surroundSelectedText('[right]', '[/right]');
                    break;

                case 'justify':
                    $textarea.surroundSelectedText('[justify]', '[/justify]');
                    break;
            }
        });

        // Bind click event for color selector
        $("[select-color='true']").click(function() {
            var color = $(this).attr('color');
            var $textarea = $('#docuText');
            $textarea.surroundSelectedText("[color='" + color + "']", '[/color]');
        });

        // Bind click event for font size selector
        $("[select-fsize='true']").click(function() {
            var fontSize = $(this).attr('fsize');
            var $textarea = $('#docuText');
            $textarea.surroundSelectedText("[text='" + fontSize + "']", "[/text]");
        });

        $scope.prepareHyperlinkSelection = function() {
            var $textarea = $('#docuText');
            var selection = $textarea.getSelection();
            if (selection.length > 0) {
                $scope.hyperlinkDescription = selection.text;
            }
        };

        $scope.insertWysiwygHyperlink = function() {
            var $textarea = $('#docuText');
            var selection = $textarea.getSelection();
            var newTab = $('#modalLinkNewTab').is(':checked') ? " tab" : "";
            if (selection.length > 0) {
                $textarea.surroundSelectedText("[url='" + $scope.hyperlink + "'" + newTab + "]", "[/url]");
            } else {
                $textarea.insertText("[url='" + $scope.hyperlink + "'" + newTab + "]" + $scope.hyperlinkDescription + '[/url]', selection.start, "collapseToEnd");
            }
            $scope.hyperlink = "";
            $scope.hyperlinkDescription = "";
            $scope.addLink = false;
        };
        /***** End WYSIWYG *****/

    });