/**
 * Created by v.sadovnikov on 18.03.2019.
 */
var response = '';

$.addNewCity = function () {
    var id = $('#id').val();
    var city = $('#city').val();
    var url = 'add_new_city.php';

    $.post( url, { city: city.trim(), id: id.trim()}, function (data){ response = data});

    setTimeout(function () {
        if(response !== ''){
            $('#response').remove();
            $('<p>', {id: 'response'}).text(response).appendTo('#form'); // выводимм юзеру ответ сервера
        }else{
            $('<p>', {id: 'response'}).text(response).appendTo('#form'); // выводимм юзеру ответ сервера
        }

    },1000);


};
