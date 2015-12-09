/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */

function QuestionKeyNormalTree(targetSelector, options, theme) {
  this.options = $.extend({
      // required
      'treeServer':null,
      'treeId':null,
      // optional
      'hasSSL':true,
      'forceSSL':false,
      'serverURLSSL':null,
      'serverURLNotSSL':null,
      'logUserActions':true,
      'showPreviousAnswers':true,
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
    this._showStartNode();
  }
  this.restart = function() {
    if (!this.started) {
      return;
    }
    // remove this, as we are starting a new instance of running this tree
    this.sessionRanTreeVersionId = null;
    this._showStartNode();
  }
  this._showStartNode = function() {
    if (this.options.showPreviousAnswers) {
        $(this.targetSelector).find(this.theme.bodyselectorPreviousAnswersWrapper).hide();
        $(this.targetSelector).find(this.theme.bodyselectorPreviousAnswerItem).remove();
    }
    this.stack = [ { 'nodeId': this.treeData.start_node.id, 'nodeOptionId':null, 'goneBackTo': false  } ];
    this._showNode();
  }
  this._showNode = function() {
    var node = this.treeData.nodes[this.stack[this.stack.length - 1].nodeId];
    $(this.targetSelector).find(this.theme.bodySelectorTitle).html(node.title);
    if (node.body_text) {
      $(this.targetSelector).find(this.theme.bodySelectorBody).text(node.body_text);
    } else if (node.body_html) {
        $(this.targetSelector).find(this.theme.bodySelectorBody).html(node.body_html);
    }
    var optionsHTML = '';
    for(i in node.options) {
      var option = node.options[i];
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
            url: this.serverURL + '/api/v1/visitorsession/action.jsonp?callback=?',
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
  this.selectOption = function(optionId) {
    var node = this.treeData.nodes[this.stack[this.stack.length - 1].nodeId];
    if (!node.title_previous_answers) {
        node.title_previous_answers = node.title;
    }
    var option = node.options[optionId];
    if (this.options.showPreviousAnswers) {
        $(this.targetSelector).find(this.theme.bodyselectorPreviousAnswersWrapper).show();
        $(this.targetSelector).find(this.theme.bodyselectorPreviousAnswersList).append(this.theme.previousAnswerHTML({'node':node, 'nodeOption':option,'stackPos':this.stack.length, 'this':'window.'+this.globalVariableName}));
    }
    this.stack.push({ 'nodeId':option.destination_node.id, 'nodeOptionId':option.id, 'goneBackTo': false });
    this._showNode();
  };
  this.goBackTo = function(stackPos) {
      if (stackPos < 2) {
          this.restart();
      } else {
          while(this.stack.length > stackPos) {
              this.stack.pop();
              $(this.targetSelector).find(this.theme.bodyselectorPreviousAnswerItem).last().remove();
          }
          this.stack[this.stack.length - 1].goneBackTo = true;
          this._showNode();
      }
  };
  var globalRefNum = Math.floor(Math.random() * 999999999) + 1;
  while("QuestionKeyNormalTree"+globalRefNum in window) {
    globalRefNum = Math.floor(Math.random() * 999999999) + 1;
  }
  window["QuestionKeyNormalTree"+globalRefNum] = this;
  this.globalVariableName = "QuestionKeyNormalTree"+globalRefNum;
  $.ajax({
    context: this,
    dataType: "jsonp",
    url: this.serverURL + '/api/v1/tree/' + this.options.treeId + '/data.jsonp?callback=?',
    success: function(data) {
      this.treeData = data;
      if (this.started) {
        this._start();
      }
    },
  });

}
