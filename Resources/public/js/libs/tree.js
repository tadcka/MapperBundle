/**
 * Mapper tree prototype.
 */
$.fn.mapperTree = function () {
    var $leftTree = $('div#mapper-tree-left');
    var $rightTree = $('div#mapper-tree-right');
    var $content = new MapperContent();

    $leftTree
        .jstree({
            'core': {
                'data': $leftTree.data('tree')
            },
            'plugins': ['dnd']
        })
        .on('dblclick.jstree', function (e) {
            var node = $(e.target).closest('li');
            $content.loadItems($leftTree.data('source'), $rightTree.data('source'), node[0].id)
        });

    $rightTree
        .jstree({
            'core': {
                'data': $rightTree.data('tree')
            },
            'plugins': ['dnd']
        })
        .on('dblclick.jstree', function (e) {
            var node = $(e.target).closest('li');
            $content.loadItems($rightTree.data('source'), $leftTree.data('source'), node[0].id)
        });

    // Example: https://groups.google.com/forum/#!topic/jstree/BYppISuCFRE
    $(document)
        .on('dnd_move.vakata', function (e, data) {
            var target = $(data.event.target);

            if (target.closest('.mapper-drop-place').length) {
                data.helper.find('.jstree-icon').removeClass('jstree-er').addClass('jstree-ok');
            }
            else {
                data.helper.find('.jstree-icon').removeClass('jstree-ok').addClass('jstree-er');
            }
        })
        .on('dnd_stop.vakata', function (e, data) {
            var target = $(data.event.target);
            if (target.closest('.mapper-drop-place').length) {
                if (data.data.jstree && data.data.origin) {
                    var $node = data.data.origin.get_node(data.element);
                    var $currentTree = $(data.element).closest('div.mapper-tree-wrapper');
                    var $source = $currentTree.data('source');

                    $content.addItem($source, $node.id);
                }
            }
        });
};
