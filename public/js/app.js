var content = $('.content');

function geo_send() {

    if ($('#geo_selector').val() === '0') {
        alert('Choose format of geo');
        return false;
    }

    var geoList = $('#country_id').val();
    geoList.trim();
    var geoArray = geoList.split(',');

    geoArray.forEach(
        function (geoElement) {
            geoElement.trim();
        }
    );


    var data  = getRequestJsonData('geo', geoArray);

    $.post(
        '/', {'data' : data}, function (response) {
            response = '<p>' + response + '</p>';
            $(response).appendTo($('.content'));
        }
    );
}

function getRequestJsonData (toolName, data) {
    var responseData = new Object();
    responseData.toolName = toolName;
    responseData.data = data;

    return JSON.stringify(responseData);
}