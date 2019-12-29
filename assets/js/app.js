/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
import $ from 'jquery';
import 'bootstrap';

// require the JavaScript
require('bootstrap-star-rating');
// require 2 CSS files needed
require('bootstrap-star-rating/css/star-rating.css');
require('bootstrap-star-rating/themes/krajee-svg/theme.css');

require('../css/app.css');
require('../css/extentions/codemirror.css');
require('../css/extentions/flipclock.css');
require('../css/extentions/simple-sidebar.css');
//require('~/node_modules/bootstrap/dist/css/bootstrap.min.css');


require('./file.js');
require('./extentions/codemirror/codemirror.js');
require('./extentions/codemirror/mode/xml.js');
require('./extentions/codemirror/format/formatting.js');
require('./extentions/flipclock/flipclock.js');
//require('~/node_modules/bootstrap/dist/js/bootstrap.min.js');

$(document).ready(function () {
    // help button
    $('#btn-tooltip').tooltip();

    // Enable dismissal of an alert via JavaScript
    $('.alert').alert();

    function printAlert(message, level = 'success') {
        var alertClass = '"alert alert-' + level + '"';
        $('div[id="alert_message"]').remove(); // remove all elem by class
        $('#page-content').append('<div id="alert_message" class=' + alertClass + ">" + message + '</div>')
    }

    // Обработка сабмитов
    $('button[need_check="1"]').click(function () {
        if (($('#check_field').length > 0 && $('#check_field').val() === '') || (typeof editor != "undefined" && editor.getValue() === '')) {
            printAlert('Empty Fields!', 'danger');
            return false;
        }

        this.submit();
    });

    var timeToNewYear = new Date('2019.12.31').getTime() - Date.now();

    var clock = $('.clock').FlipClock(Math.floor(timeToNewYear/1000), {
        clockFace: 'DailyCounter',
        countdown: true
    });
});
