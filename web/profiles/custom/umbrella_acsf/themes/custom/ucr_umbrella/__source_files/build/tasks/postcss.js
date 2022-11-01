'use strict';

var NOAH = NOAH || {};

// Requirements
NOAH.postcss = require('../modules/postcss').NOAH;

NOAH.postcss({
    src : '../css/default.min.css',
    dest: '../css/default.min.css'
});
