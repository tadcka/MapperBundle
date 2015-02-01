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
    $formWrapper.on('click', '.mapping a.remove', function ($event) {
        $(this).closest('div.mapping').remove();
    });

    /**
     * Set main mapper item.
     */
    $formWrapper.on('click', '.mapping > a.main', function ($event) {
        $formWrapper.find('.mapping-collection .mapping').each(function () {
            $(this).find('input[type=checkbox]:first').prop('checked', false);
            $(this).removeClass('is-main');
        });

        var $mapping = $(this).closest('.mapping');
        $mapping.addClass('is-main');
        $mapping.find('input[type=checkbox]:first').prop('checked', true);
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
     * Load items.
     *
     * @param {String} $itemId
     * @param {Object} $metadata
     * @param {Object} $otherMetadata
     */
    this.get = function ($itemId, $metadata, $otherMetadata) {
        fadeOn();

        $.ajax({
            url: Routing.generate('tadcka_mapper_form', {
                itemId: $itemId,
                metadata: JSON.stringify($metadata),
                otherMetadata: JSON.stringify($otherMetadata)
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
     * @param {Object} $metadata
     */
    this.validateItem = function ($itemId, $metadata) {
        if (false === hasMapping($itemId)) {
            fadeOn();

            $.ajax({
                url: Routing.generate('tadcka_mapper_validate_item', {
                    itemId: $itemId,
                    metadata: JSON.stringify($metadata)
                }),
                type: 'GET',
                success: function ($response) {
                    var $collection = $formWrapper.find('.mapping-collection:first');

                    clearErrors($collection);

                    if ($response.error) {
                        $collection.append(getCollectionRow($response.error));
                    } else {
                        var $index = $collection.data('index');
                        var $prototype = $($collection.data('prototype').replace(/__name__/g, $index));

                        $prototype.find('input[type=hidden]').val($response.item_id);
                        $prototype.find('strong:first').html($response.item_title);

                        if ($response.item_full_path) {
                            $prototype.find('.full-path').html($response.item_full_path);
                        }

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
    var hasMapping = function ($itemId) {
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
