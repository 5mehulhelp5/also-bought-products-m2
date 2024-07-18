/**
 * @author Mavenbird Commerce Team
 * @copyright Copyright (c) 2020 Mavenbird Commerce (https://www.mavenbird.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define([
    'jquery',
    'Mavenbird_AlsoBought/js/custom-local-storage',
    'mage/template',
    'mavenbird/alsobought/owl.carousel'
], function ($, customStorage, mageTemplate) {
    'use strict';

    $.widget('mavenbird.alsobought_block', {
        options: {
            ajaxData: {}
        },
        _create: function () {
            var data = this.options.ajaxData.originalRequest,
                key = data.action + '_' + data.entity_id,
                now = new Date().getTime(),
                cacheLifeTime = this.options.ajaxData.cache_lifetime*1000;

            if (customStorage.getHasData()
                && customStorage.getAlsoBoughtData(key)
                && now - customStorage.getAlsoBoughtData(key).added_at < cacheLifeTime
            ) {
                this.processTemplate(customStorage.getAlsoBoughtData(key).product_list);
            } else {
                this._loadAjax();
            }
        },

        /**
         *
         * @param position
         * @private
         */
        _blink: function (position) {
            var elementSlider = $('#' + this.options.sliderId);

            if (position === 'before-sidebar'
                || position === 'after-sidebar'
                || elementSlider.parents('.sidebar').length
            ) {
                elementSlider.owlCarousel({
                    center: true,
                    loop: true,
                    margin: 10,
                    autoplay: true,
                    autoplayTimeout: 4000,
                    autoplayHoverPause: true,
                    lazyLoad: true,
                    dots: false,
                    responsiveClass: true,
                    responsiveBaseElement: '#' + elementSlider.attr('id'),
                    responsive: {
                        0: {items: 1},
                        360: {items: 2},
                        540: {items: 3},
                        720: {items: 4},
                        900: {items: 5}
                    }
                });
            } else {
                elementSlider.owlCarousel({
                    items: 5,
                    margin: 20,
                    autoplay: true,
                    autoplayTimeout: 5000,
                    lazyLoad: true,
                    dots: false,
                    responsive: {
                        0: {
                            items: 2
                        },
                        640: {
                            items: 3
                        },
                        1024: {
                            items: 5
                        }
                    }
                });
            }
        },

        _loadAjax: function () {
            var _this = this,
                url = this.options.ajaxData.url,
                payLoad = this.options.ajaxData.originalRequest,
                key = payLoad.action + '_' + payLoad.entity_id;

            $.ajax({
                url: url,
                type: 'POST',
                data: payLoad,
                success: function (response) {
                    var data;

                    if (!response.status) {
                        return;
                    }
                    data = customStorage.getAlsoBoughtData();
                    data[key] = {
                        'product_list': response.productList,
                        'added_at': new Date().getTime()
                    };
                    customStorage.setAlsoBoughtData(data);
                    customStorage.setHasData(true);
                    _this.processTemplate(response.productList);
                }
            });
        },

        processTemplate: function (productList) {
            var template,
                element = $('#' + this.options.elementId),
                templateId = '#' + this.options.templateId;

            template = mageTemplate(
                templateId,
                {
                    data: {
                        productList: productList
                    }
                }
            );
            element.append(template);
            if (this.options.ajaxData.isSlider) {
                this._blink(element.data('position'));
            }
            element.trigger('contentUpdated');
        }
    });

    return $.mavenbird.alsobought_block;
});