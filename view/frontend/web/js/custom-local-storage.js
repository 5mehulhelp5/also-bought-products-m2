/**
 * @author MageMoto Commerce Team
 * @copyright Copyright (c) 2020 MageMoto Commerce (https://www.magemoto.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define([
    'jquery',
    'mage/storage',
    'jquery/jquery-storageapi'
], function ($) {
    'use strict';

    var cacheKey = 'mm-alsobought-local-storage-field', // define field cache key of storage
        // define local storage namespace
        storage = $.initNamespaceStorage('mm-alsobought-local-storage').localStorage,

        /**
         * @param {Object} data
         */
        saveData = function (data) {
            storage.set(cacheKey, data);
        },

        reset = function () {
            var data = {
                'hasData': false, // all data have been defined below
                'alsoboughtData': {}
            };

            saveData(data);
        },

        /**
         * @return {*}
         */
        getData = function () {
            if (!storage.get(cacheKey)) {
                reset();
            }

            return storage.get(cacheKey);
        };

    return {
        // use reset function to reset all data saved in storage
        reset : function () {
            reset();
        },

        // define getter, setter of data to save in local storage
        getHasData: function () {
            var obj = getData();

            return obj.hasData;
        },

        setHasData: function (hasData) {
            var obj = getData();

            obj.hasData = hasData;
            saveData(obj);
        },

        getAlsoBoughtData: function (key) {
            var obj = getData();

            if (key) {
                return obj.alsoboughtData[key];
            }
            return obj.alsoboughtData;
        },

        setAlsoBoughtData: function (data) {
            var obj = getData();

            obj.alsoboughtData = data;
            saveData(obj);
        }
    };
});