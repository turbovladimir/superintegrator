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