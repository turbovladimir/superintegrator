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

    var currentYear = new Date().getFullYear();
    var SeasonsTimes = {
        'Spring': `${currentYear}.03.01`,
        'Summer': `${currentYear}.06.01`,
        'Autumn': `${currentYear}.09.01`,
        'Winter': `${currentYear}.12.01`};

    for (let [season, time] of Object.entries(SeasonsTimes)) {
        var timeToSeasonStart = new Date(time).getTime() - Date.now();

        if (timeToSeasonStart > 0)  {
            activateSeasonTimer(season, timeToSeasonStart);
            break;
        }
    }

    function activateSeasonTimer(season, time) {
        $('#timer_season').append(`
<div class="block" id="season_timer"><h3 style="text-align: center">Time to ${season}</h3></div>
<div class="block" style="width: 700px; margin-left: auto; margin-right: auto">
    <div class="clock"></div></div>`);

        var clock = $('.clock').FlipClock(Math.floor(time/1000), {
            clockFace: 'DailyCounter',
            countdown: true
        });
    }
});