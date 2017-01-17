
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

angular.module('sharevixen', [
    "ngResource",
    "ui.tree",
    "ui.router",
    "angularMoment"
]);

require("./controllers/files.js");
require("./controllers/users.js");
require("./factories/files.js");
require("./factories/users.js");
