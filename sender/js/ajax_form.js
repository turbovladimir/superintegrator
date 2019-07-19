function ajax_form (){
    $('input[type=file]').change(function(){ // Вешаем функцию на событие
        files = this.files; // Получим данные файлов и добавим их в переменную
    });

// Вешаем функцию не событие click и отправляем AJAX запрос с данными файлов
    $('#check_status').click(function(){
        getInfo();
    });

    $('#send_files').click(function( event ){

        // загрузка
        $('#div_send_files').remove();
        $('.form').append('<img id="load" style="margin-left: 5%; height: 50px; width: 50px" src="../../content/images/loading2.gif"/>');

        
        event.stopPropagation(); // Остановка происходящего
        event.preventDefault();  // Полная остановка происходящего

        // Создадим данные формы и добавим в них данные файлов из files

        var data = new FormData();
        $.each( files, function( key, value ){
            data.append( key, value );
        });

        // Отправляем запрос

        $.ajax({
            url: './server2db.php',
            type: 'POST',
            data: data,
            cache: false,
            processData: false, // Не обрабатываем файлы (Don't process the files)
            contentType: false, // Так jQuery скажет серверу что это строковой запрос
            success: function(respond){

                respond = respond.trim();
                $('#load').remove();
                if (respond === "error") {
                    $('.form').append('<p id="error" style="height: 50px">Некорректные файлы</p>');
                }
            }
        });

    });

}