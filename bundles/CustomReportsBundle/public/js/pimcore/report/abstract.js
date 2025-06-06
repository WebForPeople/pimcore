/**
* This source file is available under the terms of the
* Pimcore Open Core License (POCL)
* Full copyright and license information is available in
* LICENSE.md which is distributed with this source code.
*
*  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.com)
*  @license    Pimcore Open Core License (POCL)
*/

pimcore.registerNS("pimcore.bundle.customreports.abstract");
/**
 * @private
 */
pimcore.bundle.customreports.abstract = Class.create({

    initialize: function (reportPanel, type, reference, config) {
        this.reportPanel = reportPanel;
        this.type = type;
        this.reference = reference;
        this.config = config;

        this.addPanel();
    },

    getName: function () {
        return "no name set";
    },

    getIconCls: function () {
        return "";
    },

    matchType: function (type) {
        return false;
    },

    getPanel: function () {
        console.log("You have to implement the getPanel() method.");
    },

    addPanel: function () {
        this.reportPanel.addReport(this.getPanel());
    },

    matchTypeValidate: function (type, validTypes) {
        if (typeof type == "string") {
            return in_array(type, validTypes);
        }
        else if (typeof type == "object") {
            for (var i = 0; i < type.length; i++) {
                if (in_array(type[i], validTypes)) {
                    return true;
                }
            }
        }
        return false;
    }
});