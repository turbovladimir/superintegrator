var content = $('.content');

function geo_send() {

    if ($('#geo_selector').val() === '0') {
        alert('Choose format of geo');
        return false;
    }

    var geoList = $('#country_id').val();
    geoList.trim();
    var geoArray = geoList.split(',');
    geoArray = $.map(geoArray, $.trim);
    var data = getRequestJsonData('geo', geoArray);

    $.post(
        '/', {'data': data}, function (response) {
            response = JSON.parse(response);

            var responseElementExisting = '<p class="response">' + 'Existing: ' + response.existing.join() + '</p>';
            var responseElementMissing = '<p class="response">' + 'Missing: ' + response.missing.join() + '</p>';

            if ($('.response')) {
                $('.response').remove();
            }
            $(responseElementExisting).appendTo($('.content'));
            $(responseElementMissing).appendTo($('.content'));
        }
    );
}

function getRequestJsonData(toolName, data) {
    var responseData = {
        tool: toolName,
        parameters: {
            geoType: $('#geo_selector').val(),
            geoList: data
        }
    };

    return JSON.stringify(responseData);
}