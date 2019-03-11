function log_report() {
     if (check_element("#report") === false){
         $('<div class= "report" id="report"></div>').insertAfter($(".form"));
         $('.report').append('<table>' +
             '<tr>'+
             //'<td>Количество очередей:</td>' + '<td id="table_count">' + 'Запрашиваю...' + '</td>' +
             '<td><p>Количество запросов ожидающих отправку:</p></td>' + '<td><p id="url_amount">' + 'Запрашиваю...' + '</p></td>' +
             '</tr>' +
             '</table>' +
             '</div>' +
             '<p><a href="../index.php">Вернуться в меню</a></p>')
     }
 }

