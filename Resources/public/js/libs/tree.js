/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        .on('dnd_move.vakata', function ($event, $data) {
            var $target = $($data.event.target);
            var $dropPlace = $target.closest('.mapper-drop-place');

            if ($dropPlace.length) {
                var $currentTreeSource = $($data.element).closest('div.mapper-tree-wrapper').data('source');
                if ($dropPlace.data('current_source') !== $currentTreeSource) {
                    $data.helper.find('.jstree-icon').removeClass('jstree-er').addClass('jstree-ok');
                }
            }
            else {
                $data.helper.find('.jstree-icon').removeClass('jstree-ok').addClass('jstree-er');
            }
        })
        .on('dnd_stop.vakata', function ($event, $data) {
            var $target = $($data.event.target);
            var $dropPlace = $target.closest('.mapper-drop-place');

            if ($dropPlace) {
                var $currentTreeSource = $($data.element).closest('div.mapper-tree-wrapper').data('source');
                if (($dropPlace.data('current_source') !== $currentTreeSource) && $data.data.jstree && $data.data.origin) {
                    var $node = $data.data.origin.get_node($data.element);
                    var $currentTree = $($data.element).closest('div.mapper-tree-wrapper');
                    var $source = $currentTree.data('source');

                    $content.addItem($source, $node.id);
                }
            }
        });
};
