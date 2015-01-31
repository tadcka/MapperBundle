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
function MappingForm() {
    var $formWrapper = $('div.mapping-form');

    /**
     * Remove mapper item.
     */
    $formWrapper.on('click', '.mapping > a.remove', function ($event) {
        $(this).closest('div.mapping').remove();
    });

    /**
     * Set main mapper item.
     */
    $formWrapper.on('click', 'a.mapper-main', function ($event) {
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
    $formWrapper.on('click', 'button.form-submit', function ($event) {
        $event.preventDefault();
        var $form = $formWrapper.find('form:first');

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
    $formWrapper.on('click', 'a.form-cancel', function ($event) {
        $formWrapper.html('');
    });

    /**
     * Get content.
     *
     * @returns {*|jQuery|HTMLElement}
     */
    this.getContent = function () {
        return $formWrapper;
    };

    /**
     * Load items.
     *
     * @param {String} $itemId
     * @param {Object} $sourceMetadata
     * @param {Object} $otherSourceMetadata
     */
    this.get = function ($itemId, $sourceMetadata, $otherSourceMetadata) {
        fadeOn();

        $.ajax({
            url: Routing.generate('tadcka_mapper_form_get', {
                itemId: $itemId,
                sourceMetadata: JSON.stringify($sourceMetadata),
                otherSourceMetadata: JSON.stringify($otherSourceMetadata)
            }),
            type: 'GET',
            success: function ($response) {
                $formWrapper.html($response);
                fadeOff();
            },
            error: function ($request, $status, $error) {
                $formWrapper.html($request.responseText);
                fadeOff();
            }
        });
    };

    /**
     * Load items.
     *
     * @param {String} $itemId
     * @param {Object} $sourceMetadata
     */
    this.validateItem = function ($itemId, $sourceMetadata) {
        if (false === this.hasMapping($itemId)) {
            fadeOn();

            $.ajax({
                url: Routing.generate('tadcka_mapper_validate_item', {
                    itemId: $itemId,
                    sourceMetadata: JSON.stringify($sourceMetadata)
                }),
                type: 'GET',
                success: function ($response) {
                    var $collection = $formWrapper.find('.mapping-collection:first');

                    clearErrors($collection);

                    if ($response.error) {
                        $collection.append(getCollectionRow($response.error));
                    } else {
                        var $index = $collection.data('index');
                        var $prototype = $(getCollectionRow($collection.data('prototype').replace(/__name__/g, $index)));

                        $prototype.find('input[type=hidden]').val($response.item_id);
                        $prototype.find('strong:first').html($response.item_title);

                        $collection.data('index', $index + 1);
                        $collection.append($prototype);
                    }

                    fadeOff();
                },
                error: function ($request, $status, $error) {
                    $formWrapper.html($request.responseText);
                    fadeOff();
                }
            });
        }
    };

    /**
     * Check or has mapping by item id in collection.
     *
     * @param $itemId
     *
     * @returns {boolean}
     */
    this.hasMapping = function ($itemId) {
        var $has = false;
        $formWrapper.find('.mapping-collection .mapping input[type=hidden]').each(function () {
            if ($itemId === $(this).val()) {
                $has = true;

                return true;
            }
        });

        return $has;
    };

    /**
     * Clear item errors.
     */
    var clearErrors = function ($wrapper) {
        $wrapper.find('.mapping-error').each(function () {
            $(this).remove();
        });
    };

    /**
     * @param $rowContent
     *
     * @returns {string}
     */
    var getCollectionRow = function ($rowContent) {
        return '<div class="row">' + $rowContent + '</div>';
    };

    /**
     * Fade on.
     */
    var fadeOn = function () {
        $formWrapper.fadeTo(300, 0.4);
    };

    /**
     * Fade off.
     */
    var fadeOff = function () {
        $formWrapper.fadeTo(0, 1);
    };
}
