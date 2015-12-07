/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */

function QuestionKeyNormalTree(treeServer, treeId, targetSelector, options) {
  this.options = $.extend({
      'hasSSL':true,
      'forceSSL':false,
      'serverURLSSL':'https://' + treeServer,
      'serverURLNotSSL':'http://' + treeServer,
      'logUserActions':true
  }, options || {});
  if (this.options.hasSSL && this.options.forceSSL) {
      this.serverURL = this.options.serverURLSSL;
  } else if (this.options.hasSSL) {
      this.serverURL = ('https:' == document.location.protocol ? this.options.serverURLSSL : this.options.serverURLNotSSL);
  } else {
      this.serverURL = this.options.serverURLNotSSL;
  }
  this.treeId = treeId;
  this.targetSelector = targetSelector;
  this.treeData = null;
  this.currentNodeId = null;
  this.globalVariableName = null;
  this.sessionId = null;
  this.sessionRanTreeVersionId = null;
  this.started = false;
  this.start = function() {
    this.started = true;
    if (this.treeData) {
      this._start();
    } else {
      $(this.targetSelector).html('<div class="loading">Loading, Please Wait</div>');
    }
  };
  this._start = function() {
    $(this.targetSelector).html('<div class="title"></div>'+
    '<div class="body"></div>'+
    '<div class="optionWrapper"></div>'+
    '<div class="restart"><a href="#" onclick="window.'+this.globalVariableName+'.restart(); return false;">Restart</a></div>');
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
    this.currentNodeId = this.treeData.start_node.id;
    this._showNode();
  }
  this._showNode = function() {
    var node = this.treeData.nodes[this.currentNodeId];
    $(this.targetSelector).find('.title').html(node.title);
    if (node.body_text) {
      $(this.targetSelector).find('.body').html(node.body_text); // TODO escape!
    } else if (node.body_html) {
        $(this.targetSelector).find('.body').html(node.body_html);
    }
    var optionsHTML = '';
    for(i in node.options) {
      var option = node.options[i];
      optionsHTML += '<div class="option">';
      optionsHTML += '<form onsubmit="window.'+this.globalVariableName+'.selectOption(\''+option.id+'\'); return false;">';
      optionsHTML += '<input type="submit" value="'+option.title+'" class="option'+option.id+'">';  // TODO escape!
      optionsHTML += '</form>';
      optionsHTML += '</div>';
    }
    $(this.targetSelector).find('.optionWrapper').html(optionsHTML);
    // Stats
    if (this.options.logUserActions) {
        var data = {
            'session_id': this.sessionId,
            'ran_tree_version_id': this.sessionRanTreeVersionId,
            'tree_id': this.treeId,
            'tree_version_id': this.treeData.version.public_id,
            'node_id': this.currentNodeId,
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
    var node = this.treeData.nodes[this.currentNodeId];
    var option = node.options[optionId];
    this.currentNodeId = option.destination_node.id;
    this._showNode();
  }
  var globalRefNum = Math.floor(Math.random() * 10000000) + 1;
  while("NormalTree"+globalRefNum in window) {
    globalRefNum = Math.floor(Math.random() * 10000000) + 1;
  }
  window["NormalTree"+globalRefNum] = this;
  this.globalVariableName = "NormalTree"+globalRefNum;
  $.ajax({
    context: this,
    dataType: "jsonp",
    url: this.serverURL + '/api/v1/tree/' + this.treeId + '/data.jsonp?callback=?',
    success: function(data) {
      this.treeData = data;
      if (this.started) {
        this._start();
      }
    },
  });

}
