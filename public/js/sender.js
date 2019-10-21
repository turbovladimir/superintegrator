$(document).ready(function() {
    //print('0');

    var requestObj = {
        tool: 'sender',
        parameters: {
            ask_server_about_requests: 1
        }
    };

    push('/', requestObj);




    function push(url, data) {
        if (Object.prototype.toString.call(data) !== '[object Object]') {
            console.log('data not an object');
            return null;
        }

        data = JSON.stringify(data);

        $.post(
            url, {'data': data},
            function (responseData) {
                    //print(responseData);
            }
        );
    }

    //todo выпилить нах
    function print(string) {

        if ($('.response')) {
            $('.response').remove();
        }

        $('<p class="response">' + string + '</p>').appendTo($('#requestsInProcess'));
    }
});


