/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */

function QuestionKeyCascadeTree(targetSelector, options, theme) {
    this.options = $.extend({
        // required
        'treeServer':null,
        'treeId':null,
        'treeData':null,
        // optional
        'hasSSL':true,
        'forceSSL':false,
        'serverURLSSL':null,
        'serverURLNotSSL':null,
        'logUserActions':true,
        'callBackAfterDrawNode':null,
        'scrollIntoViewOnChange':false,
    }, options || {});
    if (!this.options.treeServer) {
        this.options.treeServer = document.location.host;
    }
    if (!this.options.serverURLSSL) {
        this.options.serverURLSSL = 'https://' + this.options.treeServer;
    }
    if (!this.options.serverURLNotSSL) {
        this.options.serverURLNotSSL = 'http://' + this.options.treeServer;
    }
    this.theme = $.extend({
        'loadingHTML':function(data) { return '<div class="loading">Loading, Please Wait</div>'; },
        'bodyHTML':function(data) {
            return '<div class="tree">'+
                '<div class="title"></div>'+
                '<div class="body"></div>'+
                '<ul class="cascade"></ul>'+
                '</div>';
        },
        'bodySelectorTitle':'.title',
        'bodySelectorBody':'.body',
        'bodySelectorCascadeWrapper':'.cascade',
        'nodeHTML':function(data) {
            var html = '<li class="node">'+
                '<span class="title">'+$("<div>").text(data.node.title).html()+'</span>'+
                data.nodeBodyHTML;
            if (data.options.length > 0) {
                html += '<select onchange="var id=$(this).find(\'option:checked\').val(); if (id) {'+data.this+'.selectOption('+data.cascadeIndex+', id);} return false;"><option></option>';
                for (var i = 0; i < data.options.length; i++) {
                    html += '<option value="'+ data.options[i].id+'">'+  $("<div>").text(data.options[i].title).html() + '</option>';
                }
                html += '</select>';
            }
            return html + '</li>';
        }
    }, theme || {});
    if (this.options.hasSSL && this.options.forceSSL) {
        this.serverURL = this.options.serverURLSSL;
    } else if (this.options.hasSSL) {
        this.serverURL = ('https:' == document.location.protocol ? this.options.serverURLSSL : this.options.serverURLNotSSL);
    } else {
        this.serverURL = this.options.serverURLNotSSL;
    }
    this.targetSelector = targetSelector;
    this.treeData = null;
    this.globalVariableName = null;
    this.sessionId = null;
    this.sessionRanTreeVersionId = null;
    this.started = false;
    this.stack = [];
    this.start = function() {
        this.started = true;
        if (this.treeData) {
            this._start();
        } else {
            $(this.targetSelector).html(this.theme.loadingHTML(this._themeVariables));
        }
    };
    this._start = function() {
        $(this.targetSelector).html(this.theme.bodyHTML( this._themeVariables ));
        this._showStartNode();
    };
    this.restart = function() {
        if (!this.started) {
            return;
        }
        // remove this, as we are starting a new instance of running this tree
        this.sessionRanTreeVersionId = null;
        this._showStartNode();
    };
    this._showStartNode = function() {
        var variables = {};
        if (this._hasFeaturesVariables) {
            for(idx in this.treeData.variables) {
                variables[this.treeData.variables[idx].name] = {  'type': this.treeData.variables[idx].type, 'value': 0 };
            }
        }
        this.stack = [ { 'nodeId': this.treeData.start_node.id, 'nodeOptionId':null, 'goneBackTo': false, 'variables': variables } ];
        this._addNode();
    };
    this._hasFeatureContentLibrary = function () {
        return this.treeData.features.library_content.status;
    };
    this._hasFeaturesVariables = function () {
        return this.treeData.features.variables != null && this.treeData.features.variables.status;
    };
    this._addNode = function() {
        // Node
        var node = this.treeData.nodes[this.stack[this.stack.length - 1].nodeId];
        var variables = this.stack[this.stack.length - 1].variables;

        var themeOptions = {
            'this':'window.'+this.globalVariableName,
            'node': node,
            'nodeBodyHTML':'',
            'options': [],
            'cascadeIndex': this.stack.length - 1,
        };

        if (node.body_html) {
            themeOptions.nodeBodyHTML += '<div>' + node.body_html + '</div>';
        } else if (node.body_text) {
            themeOptions.nodeBodyHTML += '<div>'+  $('<div/>').text(node.body_text).html().trim().replace(/\n/g, "<br>") +'</div>';
        }

        if (this._hasFeatureContentLibrary()) {
            for(id in node.library_content) {
                var libraryContent = this.treeData.library_content[id];
                var show = true;
                if (this._hasFeaturesVariables()) {
                    for(conditionIdx in node.library_content[id].conditions) {
                        var condition = node.library_content[id].conditions[conditionIdx];
                        if (condition.action == "==") {
                            show = (variables[condition.variable].value == parseInt(condition.value));
                        } else if (condition.action == ">") {
                            show = (variables[condition.variable].value > parseInt(condition.value));
                            // TODO >=  =<  <
                        } else {
                            show = false;
                        }
                    }
                }
                if (show) {
                    if (libraryContent.body_html) {
                        themeOptions.nodeBodyHTML += '<div>' + libraryContent.body_html + '</div>';
                    } else if (libraryContent.body_text) {
                        themeOptions.nodeBodyHTML += '<div>' + $('<div/>').text(libraryContent.body_text).html() + '</div>';
                    }
                }
            }
        }



        for(i in node.options) {
            themeOptions.options.push(this.treeData.nodeOptions[node.options[i].id]);
        }

        var html = this.theme.nodeHTML(themeOptions);

        $(this.targetSelector).find(this.theme.bodySelectorCascadeWrapper).append(html);


        // Final
        if (this.options.scrollIntoViewOnChange) {
            $(this.targetSelector).get(0).scrollIntoView();
        }
        if (this.options.callBackAfterDrawNode) {
            this.options.callBackAfterDrawNode({ 'node':node, 'stackSize':this.stack.length });
        }
        // Stats
        if (this.options.logUserActions) {
            var data = {
                'session_id': this.sessionId,
                'ran_tree_version_id': this.sessionRanTreeVersionId,
                'tree_id': this.options.treeId,
                'tree_version_id': this.treeData.version.public_id,
                'node_id': this.stack[this.stack.length - 1].nodeId,
                'node_option_id': this.stack[this.stack.length - 1].nodeOptionId,
                'gone_back_to': this.stack[this.stack.length - 1].goneBackTo,
            };
            $.ajax({
                context: this,
                dataType: "jsonp",
                url: this.serverURL + '/api/v1/visitorsession/action.jsonp?features=library_content&callback=?',
                data: data,
                method: 'GET',
                success: function(data) {
                    this.sessionId = data.session.id;
                    if (data.session_ran_tree_version) {
                        this.sessionRanTreeVersionId = data.session_ran_tree_version.id;
                    }
                },
            });
        }
    };
    this.selectOption = function(stackIdx, optionId) {
        // If other nodes, go back!
        var goneBackTo = false;
        if (this.stack.length > (stackIdx + 1)) {
            while (this.stack.length > (stackIdx + 1)) {
                this.stack.pop();
            }
            goneBackTo = true;
            var listItems = $(this.targetSelector).find(this.theme.bodySelectorCascadeWrapper).find('li');
            while(listItems.length > (stackIdx + 1)) {
                listItems.last().remove();
                listItems = $(this.targetSelector).find(this.theme.bodySelectorCascadeWrapper).find('li');
            }
        }
        // Now draw this node
        var option = this.treeData.nodeOptions[optionId];
        var variables = $.extend({}, this.stack[this.stack.length - 1].variables)
        if (this._hasFeaturesVariables()) {
            for(idx in option.variableActions) {
                var value = option.variableActions[idx].value;
                if (this.treeData.variables[option.variableActions[idx].variable].type.toLowerCase() == 'integer') {
                    value = parseInt(value);
                }
                if (option.variableActions[idx].action.toLowerCase() == 'assign') {
                    variables[option.variableActions[idx].variable].value = value;
                } else if (option.variableActions[idx].action.toLowerCase() == 'increase') {
                    variables[option.variableActions[idx].variable].value += value;
                }
            }
        }
        this.stack.push({ 'nodeId':option.destination_node.id, 'nodeOptionId':option.id, 'goneBackTo': goneBackTo,'variables': variables });
        this._addNode();
    };
    var globalRefNum = Math.floor(Math.random() * 999999999) + 1;
    while("QuestionKeyCascadeTree"+globalRefNum in window) {
        globalRefNum = Math.floor(Math.random() * 999999999) + 1;
    }
    window["QuestionKeyCascadeTree"+globalRefNum] = this;
    this.globalVariableName = "QuestionKeyCascadeTree"+globalRefNum;
    if (this.options.treeData) {
        this.treeData = this.options.treeData;
    } else {
        $.ajax({
            context: this,
            dataType: "jsonp",
            url: this.serverURL + '/api/v1/tree/' + this.options.treeId + '/data.jsonp?features=library_content&callback=?',
            success: function(data) {
                this.treeData = data;
                if (this.started) {
                    this._start();
                }
            }
        });
    }
}
