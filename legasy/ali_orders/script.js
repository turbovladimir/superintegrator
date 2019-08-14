/**
 * Created by v.sadovnikov on 11.03.2019.
 */

$.order_submit = function ()
{
    var string = $("#order_input").val();
    var orders = string.split(/\s*\n/);
    var data = [];
    for (var i = 0; i < orders.length; i++) {
        data.push(orders[i].trim());
    }
    if(data !== undefined){
        var url = 'get_data.php';
        var str = data.join(',');
            str = str.replace(/,(\s+)?$/, '');
        //$.post( url, { data: str} );

        $('<form>', {
            id: 'form',
            action: url,
            method: "post"

        }).appendTo('body');

        $('<input>', {
            type: 'text',
            name:"data",
            method: "post",
            value: str

        }).appendTo('#form');

        $('form').submit().remove();
    }
};