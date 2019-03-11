var result = [];
var double = [];
var city_list = "";
function f() {
    for (var i = 1; i < city_table.length; i++) {

        if (city_table[i] !== undefined) {
            // пока не закончится наш массив
            var str = city_table[i]
            // складываем во временную переменную по элементу массива
            str = JSON.stringify(str); // приводим к строке
            str = str.replace(/{/g, "").replace(/}/g, "").replace("\"city\"\:", "").replace(",", ":").replace("\"id\"\:", "").replace("\:\"", ":").replace(/\"+$/ig, "")
            // преобразуем в нужную строку
            city_list += str + ","
            // складываем строки через запятую в нашу переменную
        }
    }

    city_list = "{" + city_list + "}" // добавим фигурные скобки
    city_list = city_list.replace("\,\}", "}") // отпилим последнюю запятую для того чтобы она не мешала нам преобразовать строку в объект
    city_list = city_list.toLowerCase(); // приводим все симваолы заглавные к строчным
    city_list = JSON.parse(city_list); // превращаем строку в объект js
}

setTimeout(f, 1000);



function geo_submit() {
    var not_found = [];
    var input = document.getElementById("country_id").value;
    input = input.toUpperCase()
    var re = /\s*,\s*/;
    var inputJson = input.split(re);
    console.log(inputJson);
    for (var i = 0, n = 1; i <= inputJson.length; i++, n++) {

        if (inputJson[i] == inputJson[n]) {
            if (inputJson[i] !== undefined) {
                alert("У вас дубль " + inputJson[i])
            }
        }
    }
// Проверка на наличие гео и сборка результата
    for (var i = 0; i < inputJson.length; i++) {
        if (code_list[inputJson[i]] !== undefined) {
            result.push(code_list[inputJson[i]]);
        } else {
            // неопознанные гео складываем в массив
            not_found.push(inputJson[i]);
            console.log(not_found);
        }
        // Если массив содержит какие то неопознанные гео то выведем его
        if (not_found.length > 0 ) {
            document.getElementById('error').innerHTML = "регионов нет в списке =(( " + "</br>" + "<p>" + not_found + "</p>";
        }
    }
    for (var i = 1; i < result.length; i++) {
        if (( ([i] / 20) +"").indexOf(".") > 0) {
        result[i]
        } else {
            result[i] = '</br>' + result[i]
        }
    }
    var arr = result.join(',');
    document.getElementById('finish').innerHTML = arr;
    result = [];
}

function geo_submit2() {
    var not_found = [];
    var input = document.getElementById("country_name").value;
    var re = /\s*,\s*/;
    var inputJson = input.split(re);
    console.log(inputJson);
    for (var i = 0, n = 1; i <= inputJson.length; i++, n++) {

        if (inputJson[i] == inputJson[n]) {
            if (inputJson[i] !== undefined) {
                alert("У вас дубль " + inputJson[i])
            }
        }
    }
// Проверка на наличие гео и сборка результата
    for (var i = 0; i < inputJson.length; i++) {
        if (eng_list[inputJson[i]] !== undefined) {
            result.push(eng_list[inputJson[i]]);
        } else {
            // неопознанные гео складываем в массив
            not_found.push(inputJson[i]);
            console.log(not_found);
        }
        // Если массив содержит какие то неопознанные гео то выведем его
        if (not_found.length > 0 ) {
            document.getElementById('error').innerHTML = "регионов нет в списке =(( " + "</br>" + "<p>" + not_found + "</p>";
        }
    }
    for (var i = 1; i < result.length; i++) {
        if (( ([i] / 20) +"").indexOf(".") > 0) {
        result[i];
        } else {
            result[i] = '</br>' + result[i];
        }
    }
    var arr = result.join(',');
    document.getElementById('finish').innerHTML = arr;
    result = [];
}

function geo_submit3() {
    var not_found = [];
    var input = document.getElementById("city_name").value;
    input = input.toLowerCase(); // приводим все симваолы заглавные к строчным
    var re = /\s*,\s*/;
    var inputJson = input.split(re);
    console.log(inputJson);
    for (var i = 0, n = 1; i <= inputJson.length; i++, n++) {

        if (inputJson[i] == inputJson[n]) {
            if (inputJson[i] !== undefined) {
                alert("У вас дубль " + inputJson[i]);
            }
        }
    }
// Проверка на наличие гео и сборка результата
        for (var i = 0; i < inputJson.length; i++) {
            if (city_list[inputJson[i]] !== undefined) {
                result.push(city_list[inputJson[i]]);
            } else {
                // неопознанные гео складываем в массив
                var str = inputJson[i]
                str = str[0].toUpperCase() + str.slice(1);

                not_found.push(str);
            }
        }
    // Если массив содержит какие то неопознанные гео то выведем его
    if (not_found.length > 0 ) {
        document.getElementById('error').innerHTML = "<p>" + " регионов нет в списке =((" + "</br>" +  not_found   + "</br>" + "Помоги добавить их в <a href='/geo/add_new_geo' target=\"_blank\" style='color: lawngreen'>таблицу с гео</a>" + "</p>";
    }
    for (var i = 1; i < result.length; i++) {
        if (( ([i] / 20) +"").indexOf(".") > 0) {
            result[i];
        } else {
            result[i] = '</br>' + result[i];
        }
    }
    var arr = result.join(',');
    document.getElementById('finish').innerHTML = arr;
    result = [];
};

function geo_delete() {
    document.getElementById('finish').innerHTML = "";
    document.getElementById('error').innerHTML = "";
};

function geo_delete2() {
    document.getElementById('finish').innerHTML = "";
    document.getElementById('error').innerHTML = "";
};

function geo_delete3() {
    document.getElementById('finish').innerHTML = "";
    document.getElementById('error').innerHTML = "";
};