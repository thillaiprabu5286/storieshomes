(function ($, window) {
    'use strict';
    $.widget('lotusbreath.onestepcheckout', $.lotusbreath.onestepcheckout, {

            _create: function () {
                this._super();
            },
            /*public call*/
            updateLocation: function(){
                this._updateLocation();
            },
            /* Call when update location of address that cause to change shipping rates, shipping methods ,or payment  */
            _updateLocation: function (data, typeUpdate) {

                if (!typeUpdate)
                    typeUpdate = 'shipping';
                var _this = this;
                var params = $("#checkout_form").serializeArray();
                if (typeUpdate == 'billing') {
                    params[params.length] = {'name': 'step', 'value': 'update_location_billing'};
                    window.oscObserver.beforeUpdateBilling();
                    window.oscObserver.beforeUpdateShipping();

                } else {
                    if (typeUpdate == 'billing_shipping') {
                        window.oscObserver.beforeUpdateBilling();
                        window.oscObserver.beforeUpdateShipping();
                        params[params.length] = {'name': 'step', 'value': 'update_location_billing_shipping'};
                    } else {
                        oscObserver.beforeUpdateShipping();
                        params[params.length] = {'name': 'step', 'value': 'update_location'};
                    }
                }
                if (_this.isSavingAddress)
                    return;

                this.queueProcess('update_location',{
                    url: _this.options.saveStepUrl,
                    type: 'POST',
                    data: params,
                    //async : false,
                    beforeSend: function () {
                        _this.isSavingAddress = true;
                        if (typeUpdate == 'billing') {
                            _this._loadWait('payment_partial');
                        }
                        if (typeUpdate == 'billing_shipping') {
                            _this._loadWait('shipping_partial');
                            _this._loadWait('payment_partial');
                        }
                        if (typeUpdate == 'shipping') {
                            _this._loadWait('shipping_partial');
                        }
                        _this._loadWait('review_partial');
                    },
                    complete: function (response) {
                        try {
                            var responseObject = $.parseJSON(response.responseText);
                            _this._updateHtml(responseObject);
                            if (typeUpdate == 'billing') {
                                _this._loadWait('payment_partial');
                            }else{
                                if (typeUpdate == 'billing_shipping') {
                                    window.oscObserver.afterUpdatingBilling(responseObject);
                                    window.oscObserver.afterUpdateShipping(responseObject);
                                }else{
                                    window.oscObserver.afterUpdateShipping(responseObject);
                                }
                            }
                            _this.isSavingAddress = false;
                        } catch (ex) {
                            _this.isSavingAddress = false;
                        }
                    },
                    error: function () {
                        _this.isSavingAddress = false;
                    }

                });
            }
        }
    )
})(jQuery, window);