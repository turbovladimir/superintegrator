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
    reload_form();
});


setInterval(function () {

    var countPostbacks = parseInt(waitingPostbacks);
    $('#waitingPostbacks').text(waitingPostbacks);
    log_report();

    if (countPostbacks === 0) {
        enableUploading = true;
        cat_remove_all();
        reload_form();
    }

    if (countPostbacks > 0) {
        enableUploading = false;
        cat_load();
        reload_form();
        $.get("./db2server.php", function (data) {
            console.log('sending ajax to db2server')
        });
        getInfo();
    }
}, 5000);











