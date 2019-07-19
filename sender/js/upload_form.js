function upload_form () {
    if (check_element(".menu_button") === false) { // если очередь разобрана отрисовать форму

        if (check_element("#cat")){
            cat_remove_all();
        }

        $('.form').append('<form action="SendToDb.php" method="POST" enctype="multipart/form-data">\n' +
            '\n' +
            '        <div class="menu_button">\n' +
            '        <label  class="button_send" id="get_files">\n' +
            '        <input id="input" type="file" multiple="multiple" accept=".csv"' +
            '        <span>Выберите файлы</span>\n' +
            '    </label>\n' +
            '    </div>\n' +
            '    <div class="menu_button" id="div_send_files">\n' +
            '        <label  class="button_send" id="send_files">\n' +
            '        <input type="button" class="button_send" value="Send" />\n' +
            '        <span>Отправить</span>\n' +
            '        </label>\n' +
            '        </div>\n' +
            '    <div class="menu_button" id="div_send_files">\n' +
            '        <label  class="button_send" id="check_status">\n' +
            '        <input type="button" class="button_send" value="Send" />\n' +
            '        <span>Обновить статус</span>\n' +
            '        </label>\n' +
            '        </div>\n' +
            '        </form>');


        ajax_form(); // Imultifile ajax init


    }
}