$(document).ready(function () {
    $('div#mapper-tree').mapperTree();
});

$.fn.mapperTree = function () {
    var leftTree = $('div#mapper-tree-left');
    var rightTree = $('div#mapper-tree-right');

    leftTree.jstree({ 'core': {
        'data': leftTree.data('tree')
    }});

    rightTree.jstree({ 'core': {
        'data': rightTree.data('tree')
    }});
};