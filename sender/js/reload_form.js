var sendingButtons = "<div class='div_send_files'  id='div_send_files'> \n" +
    "<label  class='button_send' id='get_files'> \n" +
    "<input id='input' type='file' multiple='multiple' accept='.csv'> \n" +
    "<span>Выберите файлы</span> \n" +
    "</label>  \n" +
    "<label  class='button_send' id='send_files'>  \n" +
    "<input type='button' class=\"button_send\" value=\"Send\" />  \n" +
    "<span>Отправить</span>  \n" +
    "</label>  \n" +
    "</div>";

var updateButton = '    <div class="div_update_info" id="div_update_info">\n' +
    '        <label  class="button_send" id="check_status">\n' +
    '        <input type="button" class="button_send" value="Send" />\n' +
    '        <span>Обновить статус</span>\n' +
    '        </label>\n' +
    '        </div>\n';

function reload_form() {

    //initialize buttons
    if (!$('.div_update_info').length && !($('.div_send_files').length)) {
        $('.main').append('<form action="SendToDb.php" class="form" method="POST" enctype="multipart/form-data"></form>');
    }

    if (enableUploading) {
        enableUploading = false;

        if ($('.div_send_files').length === 0) {
            $(sendingButtons).appendTo(".form");

            if ($('.div_update_info').length === 1) {
                $('.div_update_info').remove();
            }
        }

    } else {
        $('.div_send_files').remove();
        if ($('.div_update_info').length === 0) {
            $(updateButton).appendTo(".form");
        }
    }
}