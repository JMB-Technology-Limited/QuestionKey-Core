/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */

var ShowAllOptionsForNodeData = null;

function ShowAllOptionsForNode(nodeId, wrapperElement, nodeOptionId) {

    wrapperElement.children('.showAllNodeOptionsWrapper').remove();

    if (ShowAllOptionsForNodeData) {
        ShowAllOptionsForNodeGotData(nodeId, wrapperElement, nodeOptionId, ShowAllOptionsForNodeData)
    } else {

        wrapperElement.children('.nodeOptionsWrapper').append('<div>Loading ...</div>');


        $.ajax({
            url: "/admin/tree/" + thisPage.tree.id + "/version/" + thisPage.treeVersion.id + "/data.json",
            context: document.body
        }).done(function (data) {
            ShowAllOptionsForNodeData = data;
            ShowAllOptionsForNodeGotData(nodeId, wrapperElement, nodeOptionId, data);
        });
    }
}

function ShowAllOptionsForNodeGotData(nodeId, wrapperElement, nodeOptionId, data) {
    var found = false;
    var html = '<table class="table table-striped"><thead><tr><th>Incoming Title</th><th>Body Text</th><th>Body HTML</th></tr></thead><tbody>';
    for(var i in data.nodes[nodeId].options) {
        var nodeOption = data.nodeOptions[data.nodes[nodeId].options[i].id];
        var selectedNode = (nodeOption.id == nodeOptionId);
        if (selectedNode) {
            html += '<tr class="info">';
        } else {
            html += '<tr>';
        }
        html += '<td>'+escapeHTML(nodeOption.title)+'</td>';
        html += '<td>'+escapeHTML(nodeOption.body_text)+'</td>';
        html += '<td>'+escapeHTML(nodeOption.body_html)+'</td>';
        html += '</tr>';
        found = true;
    }
    if (found) {
        html += '</tbody></table>';
    } else {
        html = '<div>No options found!</div>'
    }
    wrapperElement.children('.nodeOptionsWrapper').html(html);
}

function escapeHTML(inString) {
    if (inString != null ) {
        return $('<div/>').text(inString).html();
    } else {
        return '';
    }
}