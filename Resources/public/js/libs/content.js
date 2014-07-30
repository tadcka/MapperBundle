/**
 * Mapper content object.
 */
function MapperContent() {
    var $content = $('div.mapper-content');

    /**
     * Get content.
     *
     * @returns {*|jQuery|HTMLElement}
     */
    this.getContent = function () {
        return $content;
    };

    /**
     * Load items.
     *
     * @param {String} $sourceSlug
     * @param {String} $categorySlug
     */
    this.addItem = function ($sourceSlug, $categorySlug) {
        if (false === this.hasItem($categorySlug)) {
            fadeOn();

            $.ajax({
                url: Routing.generate('tadcka_mapper_add_mapping', {sourceSlug: $sourceSlug, categorySlug: $categorySlug}),
                type: 'GET',
                success: function ($response) {
                    clearItemErrors();

                    var $form = $content.find('form:first');
                    $form.prepend($response);

                    fadeOff();
                },
                error: function ($request, $status, $error) {
                    $content.html($request.responseText);
                    fadeOff();
                }
            });
        }
    };

    /**
     * Load items.
     *
     * @param {String} $sourceSlug
     * @param {String} $otherSourceSlug
     * @param {String} $categorySlug
     */
    this.loadItems = function ($sourceSlug, $otherSourceSlug, $categorySlug) {
        fadeOn();

        $.ajax({
            url: Routing.generate('tadcka_mapper_get_mapping', {sourceSlug: $sourceSlug, otherSourceSlug: $otherSourceSlug, categorySlug: $categorySlug}),
            type: 'GET',
            success: function ($response) {
                $content.html($response);
                fadeOff();
            },
            error: function ($request, $status, $error) {
                $content.html($request.responseText);
                fadeOff();
            }
        });
    };

    /**
     * Has item.
     *
     * @param {String} $categorySlug
     * @returns {boolean}
     */
    this.hasItem = function ($categorySlug) {
        var has = false;
        $('div.mapper-content .mapper-item').each(function () {
            if ($categorySlug === $(this).data('slug')) {
                has = true;
            }
        });

        return has;
    };

    /**
     * Clear item errors.
     */
    var clearItemErrors = function () {
        $('div.mapper-content .mapper-item-error').each(function () {
            $(this).remove();
        });
    };

    /**
     * Fade on.
     */
    var fadeOn = function () {
        $content.fadeTo(300, 0.4);
    };

    /**
     * Fade off.
     */
    var fadeOff = function() {
        $content.fadeTo(0, 1);
    };
}
