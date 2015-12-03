define(function(require, exports, module) {
    var rules = require('../base/rules');

    rules.password = /^\d[8]$/;

    module.exports = rules;
});
