$(document).ready(function () {
    $('div#mapper-tree').mapperTree();
});

$.fn.mapperTree = function () {
    var $leftTree = $('div#mapper-tree-left');
    var $rightTree = $('div#mapper-tree-right');
    var $content = $('div.mapper-content');

    $leftTree.jstree({ 'core': {
        'data': $leftTree.data('tree')
    }})
        .on('dblclick.jstree', function (e) {
            var node = $(e.target).closest('li');
            var $nodeId = node[0].id;
            $content.fadeTo(300, 0.4);

            $.ajax({
                url: Routing.generate('tadcka_mapper_get_mapping', {sourceSlug: $leftTree.data('source'), otherSourceSlug: $rightTree.data('source'), categorySlug: $nodeId}),
                type: 'GET',
                success: function ($response) {
                    $content.html($response);
                    $content.fadeTo(0, 1);
                },
                error: function ($request, $status, $error) {
                    $content.html($request.responseText);
                    $content.fadeTo(0, 1);
                }
            });

            console.log($nodeId);
        });

    $rightTree.jstree({
        'core': {
            'data': $rightTree.data('tree')
        },
        'plugins': ['dnd']
    });

    console.log($(this));
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
            if (target.closest('.mapper-drop-place')) {
                if(data.data.jstree && data.data.origin) {
                    var $node = data.data.origin.get_node(data.element);
                    var $currentTree = $(data.element).closest('div.mapper-tree-wrapper');
                    var $source = $currentTree.data('source');

                    var $otherSource;
                    if ('mapper-tree-right' === $currentTree.attr('id')) {
                        $otherSource = $('div#mapper-tree-left').data('source')
                    } else {
                        $otherSource = $('div#mapper-tree-right').data('source')
                    }

                    $content.fadeTo(300, 0.4);

                    $.ajax({
                        url: Routing.generate('tadcka_mapper_add_mapping', {sourceSlug: $source, otherSourceSlug: $otherSource, categorySlug: $node.id}),
                        type: 'GET',
                        success: function ($response) {
                            $content.find('form:first').prepend($response);
                            $content.fadeTo(0, 1);
                        },
                        error: function ($request, $status, $error) {
                            $content.html($request.responseText);
                            $content.fadeTo(0, 1);
                        }
                    });
                }
            }
        });

};