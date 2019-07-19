var table_count;
var url_amount;
var files; // Переменная куда будут располагаться данные файлов

function check_element (id_element){ // проверка дом элементов
   if (document.querySelector(id_element) === null) return false;
   else return true;
}

function getInfo(){
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
    + "Раз в несколько минут мы будем переотправлять данные..."],
    typeSpeed: 3,
       //backSpeed: 5,
       //loop: true
   });
    getInfo();
});



setInterval(function () {

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











