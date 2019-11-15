$(document).ready(function () {
// Enable dismissal of an alert via JavaScript
    $('.alert').alert();

    function printAlert(message, level = 'success'){
        var alertClass = '"alert alert-' + level + '"';
        $('div[id="alert_message"]').remove(); // remove all elem by class
        $('#page-content').append('<div id="alert_message" class=' + alertClass + ">" +message + '</div>')
    }

    var content = $('.content');
    var printResponse = false;

////////////////xml emulator////////////////
    function generate_link() {
        var xml = $('#xml').val();
        var requestObj = {
            tool: 'xml_emulator',
            parameters: {
                xml: xml.trim()
            }
        };
        printResponse = true;
        push('/xml_emulator', requestObj);
    }

////////////////xml emulator////////////////

////////////////geo////////////////
    $('.dropdown-item').click(function () {
        var buttonName = $(this).attr('name');
        var input = $('#geo_input').val();
        console.log(buttonName);
        console.log(input);

        if (input === '') {
            printAlert( 'Не указано гео', 'danger')
            return false;
        }

        this.submit();
    });

////////////////geo////////////////

////////////////ali orders////////////////
    function get_csv_file() {
        var orders = $('#ali_orders').val();
        orders = Input2Array(orders, /\s*\n/);

        $('<form>', {
            id: 'form',
            action: '/ali_orders',
            method: "post"

        }).appendTo('#content');

        $('<input>', {
            type: 'text',
            method: "post",
            name: "orders",
            value: JSON.stringify(orders)

        }).appendTo('#form');

        $('form').submit().remove();

    }

////////////////ali orders////////////////


//todo доделать
    function Input2Array(string, splitRegExp) {
        string.trim();

        if (string === '') {
            alert('Incorrect input values');
            return string;
        }

        var array = string.split(splitRegExp);

        if (array.length === 0 || array === undefined) {
            alert('Incorrect input values');
            return array;
        }

        array = $.map(array, $.trim);
        array = $.map(array, function (element) {
            if (element !== '') {
                return element;
            }
        });

        return array;
    }

    function push(url, data) {
        if (Object.prototype.toString.call(data) !== '[object Object]') {
            console.log('data not an object');
            return null;
        }

        data = JSON.stringify(data);

        sendToServer(url, data);
    }

    function sendToServer(url, requestData) {
        $.post(
            url, {'data': requestData},
            function (responseData) {
                if (printResponse) {
                    print(responseData);
                }
            }
        );
    }

    function print(responseData) {
        var response = jQuery.parseJSON(responseData);

        if ($('.response')) {
            $('.response').remove();
        }

        for (var key in response) {
            $('<p class="response">' + key + ': ' + response[key] + '</p>').appendTo($('.content'))
        }
    }
});