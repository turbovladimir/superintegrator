var cat_count = $('#cat').length;


function cat_remove_all() {
    if ($('#cat').length) {
        $('#cat').each(function () { // удаляет все элементы с выбранным айди
            var idAttr = $(this).attr('id'),
                selector = '[id=' + idAttr + ']';
            if ($(selector).length > 0) {
                $(selector).remove();
            }
        });
    }
}

function cat_load() {
    if (cat_count < 5) {

        $('.cat').append('<img id="cat" src="https://i.gifer.com/19wN.gif"/>');
        cat_count++;

    } else {
        cat_remove_all();
        cat_count = 0; // сбросили счетик
    }
}