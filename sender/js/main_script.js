var table_count;
var url_amount;
var files; // Переменная куда будут располагаться данные файлов

function check_element (id_element){ // проверка дом элементов
   if (document.querySelector(id_element) === null) return false;
   else return true;
}

function request(){
    $.ajax({
        type: "POST",
        url: "../../sender/updateTable.php",
        data: "refresh=1",
        success: function (data) {
            if (data !== "") {
                url_amount = data.trim();
            }
        }
    });

}


jQuery(document).ready(function(){
   var typed = new Typed(".typein", {
    strings: ["Приветствую интегратор.\<\/br\> Этот инструмент создан для переотправки пикселей и постбэков.\<\/br> Для того чтобы переотправить данные из архива процессинга, скачай файлы с нужными неделями.\<\/br> Есть поддержка мультизагрузки файлов, для этого выдели нужные файлы и нажми кнопку \"Отправить\""
    + "</br>"
    + "Раз в 2 минуты мы будем отправлять по 3000 запросов..."
    + "</br>" + "</br>" + "###########################################################################################################"],
    typeSpeed: 10,
       //backSpeed: 5,
       //loop: true
   });
});



setInterval(function () {
    request();

if (url_amount !== undefined) {
    $('#url_amount').text(url_amount);
    log_report();
    if (url_amount == '0') {
        upload_form();

    } else {
        cat_load();
    }
}
    }, 3000);











