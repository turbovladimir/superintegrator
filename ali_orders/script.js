var order_list = 1;
var order_list_arr = [];
var orders = [];
var result = [];
var order_split = [];
var str = "string";
var orders_json = [];


$.order_submit = function ()
{  // получить содержимое в переменную
	order_list = $("#order_input").val();
    var re = /\s*\n/;
    //преобразовать столбец в массив
    order_list_arr = order_list.split(re);
    // проверить на пустые элементы массив и удалить их
    for (var i = 0; i < order_list_arr.length; i++) {
        if (order_list_arr[i] == ""){
            order_list_arr.splice([i], 1)
        }
    }
    // проверка на кол-во ордеров, если более ста то один сценарий, если менее то другой
	if (order_list_arr.length <= 100 && order_list_arr.length != 0 && order_list_arr.length != undefined)
	{
        str = order_list_arr.join(',');
		$.ajax(
		{
			type: "GET",
			url: ("https://gw.api.alibaba.com/openapi/param2/2/portals.open/api.getOrderStatus/30056?appSignature=9FIO77dDIidM&orderNumbers=" + str),
			async: false,
			success: function (data)
			{
				result = data.result.orders;
			  orders_json = JSON.stringify(result);
			}
		})
	}
	else
	{
    console.log("Выполняю елсе " );
		var part = order_list_arr.length / 100;
		part = Math.ceil(part);
		var n = 0;
		var a = 0;
		for (var i = 1; i <= part; i++)
		{
			n = 100 * [i]
			order_split[i] = order_list_arr.slice(a, n)
			a = n;
		}
		for (var z = 1; z < order_split.length; z++)
		{
                console.log("z- " + z);
          			 str = order_split[z].join(',');




                $.ajax(
                {
                  type: "GET",
                  url: ("https://gw.api.alibaba.com/openapi/param2/2/portals.open/api.getOrderStatus/30056?appSignature=9FIO77dDIidM&orderNumbers=" + str),
                  async: false,
                  success: function (data)
                  {

                      result[z] = data.result.orders;
                      console.log("Результат- " + result[z]);



                  }
                  //setTimeout (function, 10000);
                })

	  };
		for (var i = 1; i < order_split.length; i++) {
					Array.prototype.push.apply(orders,result[i]);
					console.log("i- " + i);
					console.log("Результат- " + orders);
		}
		 orders_json = JSON.stringify(orders);


} //тег если
$('.form').append('<div class= output><th>Json данные:</th><th><input id="order_output" type="text" size="40"></th><a href="http://www.convertcsv.com/json-to-csv.htm"  style="color: greenyellow; padding-left: 10px" target="_blank">Онлайн сервис для преобразовния</a></div>');
$('#order_output').val(orders_json);
	$.ajax({
        type: "POST",
        url: "http://test/ali_orders/get_file.php" , // заменить на боевой superintegrator.tk
        async: false,
        data: { orders: orders_json},
        success: function (data)
        {

            window.location.replace('http://test/ali_orders/get_file.php')

        }

	})



};
