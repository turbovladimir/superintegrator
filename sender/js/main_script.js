var table_count;
var waitingPostbacks;
var files; // Переменная куда будут располагаться данные файлов
var enableUploading = false; // Разрешает загрузку файлов аяксом

function check_element(id_element) { // проверка дом элементов
    if (document.querySelector(id_element) === null) return false;
    else return true;
}

function getInfo() {
    $.ajax({
        type: "POST",
        url: "../../sender/updateTable.php",
        data: "refresh=1",
        success: function (data) {
            if (data !== "") {
                waitingPostbacks = data.trim();
            }
        }
    });

}


jQuery(document).ready(function () {
    var typed = new Typed(".typein", {
        strings: ["Приветствую интегратор.\<\/br\> Этот инструмент создан для переотправки пикселей и постбэков.\<\/br> Для того чтобы переотправить данные из архива процессинга, скачай файлы с нужными неделями.\<\/br> Есть поддержка мультизагрузки файлов, для этого выдели нужные файлы и нажми кнопку \"Отправить\""
        + "</br>"
        + "Раз в несколько минут мы будем переотправлять данные..."],
        typeSpeed: 3,
        //backSpeed: 5,
        //loop: true
    });
    getInfo();
    upload_form();
});


setInterval(function () {

    if (waitingPostbacks) {
        $('#waitingPostbacks').text(waitingPostbacks);
        log_report();

        if (waitingPostbacks > 0) {
            cat_load();
            $.get( "./db2server.php", function( data ) {console.log('sending ajax to db2server')
            });
        } else {
            enableUploading = true;
        }

    }
}, 3000);











