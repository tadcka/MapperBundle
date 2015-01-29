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
function MapperForm() {
    var $form = $('div.mapper-form');

    /**
     * Remove mapper item.
     */
    $form.on('click', 'a.mapper-remove', function ($event) {
        $(this).closest('div.mapper-item').remove();
    });

    /**
     * Set main mapper item.
     */
    $form.on('click', 'a.mapper-main', function ($event) {
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
    $form.on('click', 'button.form-submit', function ($event) {
        $event.preventDefault();
        var $form = $form.find('form:first');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function ($response) {
                $form.html($response);
                fadeOff();
            },
            error: function ($request, $status, $error) {
                $form.html($request.responseText);
                fadeOff();
            }
        });
    });

    /**
     * Cancel form.
     */
    $form.on('click', 'a.form-cancel', function ($event) {
        $form.html('');
    });

    /**
     * Get content.
     *
     * @returns {*|jQuery|HTMLElement}
     */
    this.getContent = function () {
        return $form;
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

                    var $form = $form.find('form:first');
                    $form.prepend($response);

                    fadeOff();
                },
                error: function ($request, $status, $error) {
                    $form.html($request.responseText);
                    fadeOff();
                }
            });
        }
    };

    /**
     * Load items.
     *
     * @param {String} $itemId
     * @param {String} $sourceMetadata
     * @param {String} $otherSourceMetadata
     */
    this.get = function ($itemId, $sourceMetadata, $otherSourceMetadata) {
        fadeOn();

        $.ajax({
            url: Routing.generate('tadcka_mapper_form_get', {itemId: $itemId, sourceMetadata: $sourceMetadata, otherSourceMetadata: $otherSourceMetadata}),
            type: 'GET',
            success: function ($response) {
                $form.html($response);
                fadeOff();
            },
            error: function ($request, $status, $error) {
                $form.html($request.responseText);
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
        $form.fadeTo(300, 0.4);
    };

    /**
     * Fade off.
     */
    var fadeOff = function () {
        $form.fadeTo(0, 1);
    };
}
