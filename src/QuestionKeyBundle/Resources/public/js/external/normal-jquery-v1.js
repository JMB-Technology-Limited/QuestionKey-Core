/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */

function QuestionKeyNormalTree(targetSelector, options, theme) {
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
      'showPreviousAnswers':true,
      'callBackAfterDrawNode':null,
      'scrollIntoViewOnChange':false,
      'browserHistoryRewrite':false,
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
            return '<div class="node">'+
                '<div class="title"></div>'+
                '<div class="body"></div>'+
                '<form class="optionsWrapper" onsubmit="var id=$(this).find(\'input.option:checked\').val(); if (id) {'+data.this+'.selectOption(id);} return false;"><div class="optionsList"></div><input type="submit" value="Next"></form>'+
                '</div>'+
                '<div class="restart"><a href="#" onclick="'+ data.resetJavaScript +'; return false;">Restart</a></div>'+
                '<div class="previousAnswersWrapper" style="display: none;"><table class="previousAnswersTable"><tr><th>Previous Answers</th><th>&nbsp;</th></table></div>';
        },
        'bodySelectorTitle':'.title',
        'bodySelectorBody':'.body',
        'bodySelectorOptionsWrapper':'.optionsWrapper',
        'bodySelectorOptionsList':'.optionsList',
        'bodyselectorPreviousAnswersWrapper':'.previousAnswersWrapper',
        'bodyselectorPreviousAnswersList':'.previousAnswersTable',
        'bodyselectorPreviousAnswerItem':'.previousAnswersTable tr.answer',
        'previousAnswerHTML':function(data) {
            return '<tr class="answer">'+
                '<td>'+$("<div>").text(data.node.title_previous_answers).html()+'</td>'+
                '<td>'+$("<div>").text(data.nodeOption.title).html()+'</td>'+
                '<td><a href="#" onclick="'+ data.this +'.goBackTo('+data.stackPos+'); return false;">Change This</a></td>'+
                '</tr>'; // TODO escape!
        },
        'optionHTML':function(data) {
            return '<div class="option">'+
                '<label>'+
                '<input name="option" class="option" type="radio" value="'+data.option.id+'">'+
                '<span class="title">'+$("<div>").text(data.option.title).html()+'</span>'+
                '</label>'+
                (data.option.body_html ? '<span class="body">'+ data.option.body_html + '</span>' : (data.option.body_text ? '<span class="body">' + $("<div>").text(data.option.body_text).html().trim().replace(/\n/g, "<br>") + '</span>' : ''))+
                '</div>';
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
      $(this.targetSelector).html(this.theme.loadingHTML({}));
    }
  };
  this._start = function() {
    $(this.targetSelector).html(this.theme.bodyHTML( {'resetJavaScript':'window.'+this.globalVariableName+'.restart()', 'this':'window.'+this.globalVariableName } ));
    if (this._doBrowserHistoryRewrite()) {
        if (!this._processHistoryState(history.state)) {
            this._showStartNode();
        }
    } else {
        this._showStartNode();
    }
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
    if (this.options.showPreviousAnswers) {
        $(this.targetSelector).find(this.theme.bodyselectorPreviousAnswersWrapper).hide();
        $(this.targetSelector).find(this.theme.bodyselectorPreviousAnswerItem).remove();
    }
    var variables = {};
    if (this._hasFeaturesVariables) {
        for(idx in this.treeData.variables) {
            variables[this.treeData.variables[idx].name] = {  'type': this.treeData.variables[idx].type, 'value': 0 };
        }
    }
    this.stack = [ { 'nodeId': this.treeData.start_node.id, 'nodeOptionId':null, 'goneBackTo': false, 'variables': variables } ];
    this._windowPushState();
    this._showNode();
  };
  this._hasFeatureContentLibrary = function () {
      return this.treeData.features.library_content.status;
  };
  this._hasFeaturesVariables = function () {
      return this.treeData.features.variables != null && this.treeData.features.variables.status;
  };
  this._showNode = function() {
    // Node
    var node = this.treeData.nodes[this.stack[this.stack.length - 1].nodeId];
    var variables = this.stack[this.stack.length - 1].variables;
    $(this.targetSelector).find(this.theme.bodySelectorTitle).html(node.title);
    var bodyHTML = '';
    if (node.body_text) {
        bodyHTML += '<div>'+  $('<div/>').text(node.body_text).html().trim().replace(/\n/g, "<br>") +'</div>';
    } else if (node.body_html) {
        bodyHTML += '<div>'+  node.body_html  +'</div>';
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
                if (libraryContent.body_text) {
                    bodyHTML += '<div>' + $('<div/>').text(libraryContent.body_text).html() + '</div>';
                } else if (libraryContent.body_html) {
                    bodyHTML += '<div>' + libraryContent.body_html + '</div>';
                }
            }
        }
    }
    $(this.targetSelector).find(this.theme.bodySelectorBody).html(bodyHTML);
    // Node Options
    var optionsHTML = '';
    for(i in node.options) {
      var option = this.treeData.nodeOptions[node.options[i].id];
      optionsHTML += this.theme.optionHTML({
          'selectOptionJavaScript':  'window.'+this.globalVariableName+'.selectOption(\''+option.id+'\')' ,
          'option': option
      });
    }
    if (optionsHTML) {
        $(this.targetSelector).find(this.theme.bodySelectorOptionsWrapper).show();
        $(this.targetSelector).find(this.theme.bodySelectorOptionsList).html(optionsHTML);
    } else {
        $(this.targetSelector).find(this.theme.bodySelectorOptionsWrapper).hide();
    }
    // Previous Answers
    if (this.options.showPreviousAnswers && this.stack.length > 1) {
        $(this.targetSelector).find(this.theme.bodyselectorPreviousAnswersWrapper).show();
        var html = '';
        for (var i = 1; i < this.stack.length; i++) {
            html += this.theme.previousAnswerHTML({'node':this.treeData.nodes[this.stack[i-1].nodeId], 'nodeOption':this.treeData.nodeOptions[this.stack[i].nodeOptionId], 'stackPos':i, 'this':'window.'+this.globalVariableName});
        }
        $(this.targetSelector).find(this.theme.bodyselectorPreviousAnswersList).empty().append(html);
    } else {
        $(this.targetSelector).find(this.theme.bodyselectorPreviousAnswersWrapper).hide();
    }
    // Final
    if (this.options.scrollIntoViewOnChange) {
        $(this.targetSelector).get(0).scrollIntoView();
    }
    if (this.options.callBackAfterDrawNode) {
        this.options.callBackAfterDrawNode({ 'node':node });
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
            }
        });
    }
  };
  this.selectOption = function(optionId) {
    var node = this.treeData.nodes[this.stack[this.stack.length - 1].nodeId];
    if (!node.title_previous_answers) {
        node.title_previous_answers = node.title;
    }
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
    this.stack.push({ 'nodeId':option.destination_node.id, 'nodeOptionId':option.id, 'goneBackTo': false,'variables': variables });
    this._windowPushState();
    this._showNode();
  };
  this.goBackTo = function(stackPos) {
      if (stackPos < 2) {
          this.restart();
      } else {
          while(this.stack.length > stackPos) {
              this.stack.pop();
          }
          this.stack[this.stack.length - 1].goneBackTo = true;
          this._windowPushState();
          this._showNode();
      }
  };
    this._doBrowserHistoryRewrite = function() {
        return this.options.browserHistoryRewrite && (typeof history != 'undefined') && (typeof history.pushState != 'undefined');
    };
    this._windowPushState = function() {
        if (this._doBrowserHistoryRewrite()) {
            history.pushState({
                'questionkey':{
                    'global_variable_name':this.globalVariableName,
                    'tree_id':this.options.treeId,
                    'tree_version_id': this.treeData.version.public_id,
                    'stack':JSON.parse(JSON.stringify(this.stack)),
                }
            },document.title,window.location.href);
        }
    };
    this.onWindownPopState = function(event) {
        console.log(event.state.questionkey);
        this._processHistoryState(event.state)
    };
    this._processHistoryState = function(state) {
        if (this.options.browserHistoryRewrite
            && state
            && state.questionkey
            && state.questionkey.global_variable_name == this.globalVariableName
            && state.questionkey.tree_id == this.options.treeId
            && state.questionkey.tree_version_id == this.treeData.version.public_id) {
            this.stack = state.questionkey.stack;
            this._showNode();
            return true;
        }
        return false;
    };
  var globalRefNum = Math.floor(Math.random() * 999999999) + 1;
  while("QuestionKeyNormalTree"+globalRefNum in window) {
    globalRefNum = Math.floor(Math.random() * 999999999) + 1;
  }
  window["QuestionKeyNormalTree"+globalRefNum] = this;
  this.globalVariableName = "QuestionKeyNormalTree"+globalRefNum;
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
