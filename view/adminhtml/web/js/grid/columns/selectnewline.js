/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'underscore',
    'Magento_Ui/js/grid/columns/select',
], function (_, Select) {
    'use strict';

    return Select.extend({
        getLabel: function () {
            return this
                ._super()
                .replace(/\,/g, '<br>');
        }
    });
});
