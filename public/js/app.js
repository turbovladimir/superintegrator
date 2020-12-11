function copyToClipboard(element) {
    let $temp = $("<input>");
    $("body").append($temp);
    let color = $(element).css("background-color");
    $(element).css("background-color", "#a2a2a2");
    $temp.val($(element).html()).select();
    document.execCommand("copy");
    $temp.remove();
    setTimeout(function () {
        $(element).css("background-color", color);
    }, 100);
}

$(document).ready(function () {
    $( "#alert_message_lg" ).each(function() {
        let randomId = 'pre_' + Math.random().toString(36).substring(7);
        this.id = randomId
        let imgTag = '<i class="fa fa-clone" aria-hidden="true" onclick="copyToClipboard(\'#'+randomId+'\')"></i>';
        $(this).before(imgTag);
    });

    // help button
    $('#btn-tooltip').tooltip();

    // Enable dismissal of an alert via JavaScript
    $('.alert').alert();

    function printAlert(message, level = 'success') {
        let alertClass = '"alert alert-' + level + '"';
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

    let seasonsAndMonths = {
        winter: [12,1,2],
        spring: [3,4,5],
        summer: [6,7,8],
        autumn: [9,10,11],
    };

    let date = new Date();
    let currentMont = date.getMonth() + 1;

    $.each(seasonsAndMonths, function (season, months) {
        if ($.inArray(currentMont, months) !== -1) {
            setUpWallPaperBySeason(season);
            //activateSeasonTimer(season, timeToSeasonStart);
        }
    })
    
    function setUpWallPaperBySeason(season) {
        let backImg = "url('../images/" + season +"_back.jpg')";
        $('#page-content-wrapper').css('background-image', backImg);

        if (season === 'winter') {
            $('#snowfall').css("background-image", "url(https://media.giphy.com/media/Kfrq2V2A7wODGMdEXQ/giphy.gif)");
        }
    }

    function activateSeasonTimer(season, time) {
        $('#timer_season').append(`
<div class="block" id="season_timer"><h3 style="text-align: center">Time to ${season}</h3></div>
<div class="block" style="width: 700px; margin-left: auto; margin-right: auto">
    <div class="clock"></div></div>`);

        $('.clock').FlipClock(Math.floor(time/1000), {
            clockFace: 'DailyCounter',
            countdown: true
        });
    }
});