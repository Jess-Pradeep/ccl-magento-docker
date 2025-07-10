define([
    'uiRegistry'
], function (registry) {
    "use strict";

    return function () {
        registry.async('aw_ca_company_domain_listing.aw_ca_company_domain_listing_data_source')(
            function (dataSource) {
                dataSource.reload();
            }.bind(this)
        );
    }
});
