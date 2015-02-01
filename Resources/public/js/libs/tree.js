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
    var $leftSource = $('div#mapper-source-left');
    var $rightSource = $('div#mapper-source-right');
    var $leftTree = $leftSource.find('.mapper-tree:first');
    var $rightTree = $rightSource.find('.mapper-tree:first');
    var $mappingForm = new MappingForm();

    $leftTree
        .jstree({
            core: {
                data: $leftTree.data('tree')
            },
            plugins: ['dnd']
        })
        .on('activate_node.jstree', function ($event, $data) {
            $mappingForm.get($data.node.id, $leftSource.data('metadata'), $rightSource.data('metadata'));
        });

    $rightTree
        .jstree({
            core: {
                data: $rightTree.data('tree')
            },
            plugins: ['dnd']
        })
        .on('activate_node.jstree', function ($event, $data) {
            $mappingForm.get($data.node.id, $rightSource.data('metadata'), $leftSource.data('metadata'));
        });

    // Example: https://groups.google.com/forum/#!topic/jstree/BYppISuCFRE
    $(document)
        .on('dnd_move.vakata', function ($event, $data) {
            var $target = $($data.event.target);
            var $dropPlace = $target.closest('.mapping-drop-place');

            if ($dropPlace.length) {
                if ($dropPlace.data('current_source') !== $($data.element).closest('div.mapper-block').data('metadata').name) {
                    $data.helper.find('.jstree-icon').removeClass('jstree-er').addClass('jstree-ok');
                }
            }
            else {
                $data.helper.find('.jstree-icon').removeClass('jstree-ok').addClass('jstree-er');
            }
        })
        .on('dnd_stop.vakata', function ($event, $data) {
            var $target = $($data.event.target);
            var $dropPlace = $target.closest('.mapping-drop-place');

            if ($dropPlace.length) {
                var $metadata = $($data.element).closest('div.mapper-block').data('metadata');

                if (($dropPlace.data('current_source') !== $metadata.name) && $data.data.jstree && $data.data.origin) {
                    var $node = $data.data.origin.get_node($data.element);

                    $mappingForm.validateItem($node.id, $metadata);
                }
            }
        });
};
