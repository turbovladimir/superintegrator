function concatenate() {
var str = document.getElementById("arr").value;
var str2 = document.getElementById("arr2").value;

var re = /\s*\n/;
var arr = str.split(re);
var arr2 = str2.split(re);
     console.log(arr);
     console.log(arr2);
     var sum = [];
     for (var i = 0; i < arr.length; i++) {
        if(arr[i] === undefined){
            arr[i] = "";
        }
        if(arr2[i] === undefined){
            arr2[i] = "";
        }
         sum[i]  = arr[i] + arr2[i];
       
         }
     sum = sum.filter(function(e){return e});  //удалить ВСЕ пустые значения ("", null, undefined и 0)
     var result = sum.join('\n');
     var textarea = document.createElement('textarea');
     document.body.appendChild(textarea);
     textarea.className = "result_style";
     textarea.style = "width:200px; height:400px";
     textarea.innerHTML = result;
}


function date_pars() {
    var str = document.getElementById("US_date").value;
    var re = /\s*\n/; // убрали лишние пробелы между датами
    var date_arr_usa = str.split(re); // распарсили строку в мсассив usa
    var re2 = /(\w+)-(\w+)-(\w+)\s(\w+)/; // замена формата даты
    var date_arr_eu = []; //  новый массив

for (var i = 0; i < date_arr_usa.length; i++) {
    if (date_arr_usa[i] === '') continue;
    date_arr_eu[i] = date_arr_usa[i].replace(re2, "$2.$1.$3 $4") //наполнили массив датами евро формата
}

    date_arr_eu = date_arr_eu.filter(function(e){return e});  //удалить ВСЕ пустые значения ("", null, undefined и 0)
    var result_pars = date_arr_eu.join('\n');
    document.getElementById('EU_date').innerHTML = result_pars;
}

function date_pars2() {
    var str = document.getElementById("EU_date").value;
    var re = /\s*\n/; // убрали лишние пробелы между датами
    var date_arr_eu = str.split(re); // распарсили строку в мсассив usa
    var re2 = /(\w+).(\w+).(\w+)\s(\w+)/; // замена формата даты
    var date_arr_usa = []; //  новый массив

    for (var i = 0; i < date_arr_eu.length; i++) {
        if (date_arr_eu[i] === '') continue;
        date_arr_usa[i] = date_arr_eu[i].replace(re2, "$2-$1-$3 $4") //наполнили массив датами евро формата
    }

    date_arr_usa = date_arr_usa.filter(function(e){return e});  //удалить ВСЕ пустые значения ("", null, undefined и 0)
    var result_pars = date_arr_usa.join('\n');
    document.getElementById('US_date').innerHTML = result_pars;
}

function transport_str_to_column() {
    var str = document.getElementById("string").value;
    var result = str.replace(/\s/g, ''); //remove space
    result = result.replace(/,/g, '\n');
    document.getElementById('column').innerHTML = result;
}

function transport_column_to_str() {
    var str = document.getElementById("column").value;
    if (str.length - 1 === /\s/) str.slice(0, -1);
        str = str.replace(/\n+$/m, ''); // убрали переносы строки если они есть
    var result = str.replace(/\s/g, ',');
        //result = result.slice(0, -1);
    document.getElementById('string').innerHTML = result;
}
