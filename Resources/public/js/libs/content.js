/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mapper content object.
 */
function MapperContent() {
    var $content = $('div.mapper-content');

    /**
     * Remove mapper item.
     */
    $content.on('click', 'a.mapper-remove', function ($event) {
        $(this).closest('div.mapper-item').remove();
    });

    /**
     * Set main mapper item.
     */
    $content.on('click', 'a.mapper-main', function ($event) {
        $('div.mapper-content .mapper-item').each(function () {
            $(this).find('input[type=radio]:first').removeAttr('checked');
            $(this).removeClass('is-main');
        });

        var $mapperItem = $(this).closest('div.mapper-item');
        $mapperItem.addClass('is-main');
        $mapperItem.find('input[type=radio]:first').attr('checked', 'checked');
    });

    /**
     * Submit form.
     */
    $content.on('click', 'button.form-submit', function ($event) {
        $event.preventDefault();
        var $form = $content.find('form:first');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function ($response) {
                $content.html($response);
                fadeOff();
            },
            error: function ($request, $status, $error) {
                $content.html($request.responseText);
                fadeOff();
            }
        });
    });

    /**
     * Cancel form.
     */
    $content.on('click', 'a.form-cancel', function ($event) {
        $content.html('');
    });

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

                return true;
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
    var fadeOff = function () {
        $content.fadeTo(0, 1);
    };
}
