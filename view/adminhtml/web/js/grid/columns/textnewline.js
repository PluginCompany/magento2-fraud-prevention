/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'underscore',
    'Magento_Ui/js/grid/columns/column',
], function (_, Column) {
    'use strict';

    return Column.extend({
        getLabel: function () {
            console.log(' bla');
            return this
                ._super()
                .replace(/\,/g, '<br>');
        }
    });
});
