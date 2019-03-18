function registration() {
    //отрисовываем форму регистрации
     if (document.querySelector('.reg_menu') == null)
         {
             $('<div class="reg_menu"></div>').insertAfter($(".main_menu"));
             $('.reg_menu').append('<input name="Логин" id="login" placeholder="Придумайте логин" type="text" size="20" maxlength="10"></br>')
             $('.reg_menu').append('<input name="Почта" id="mail" placeholder="Введите почту"  type="text" size="20" maxlength="20"></br>')
             $('.reg_menu').append('<input name="Пароль" id="password" placeholder="Придумайте пароль" type="text" size="20" maxlength="20"></br>')
             $('.reg_menu').append('<input name="Проверочный пароль" id="password_check" placeholder="Еще раз введите пароль" type="text" size="20" maxlength="20"></br>')
             $('.reg_menu').append('<input type="submit" value="Входи" onclick = "submit_reg()"></br>')
         }
 };

    function submit_reg() {
        //создаем объект с данными регистрации
        var reg_obj = {};
        // заполняем объект
        reg_obj= {login: "", mail: "", password: "", password_check: ""};
        var value_obj = Object.keys(reg_obj)
        for (var i = 0; i < value_obj.length; i++ ) {
            if (value_obj[i] !== "") {
                var elem_id = value_obj[i]
                reg_obj[value_obj[i]] = document.getElementById(elem_id).value;
            } else {
                reg_obj[value_obj[i]] = "";
            }
        }
        // проверка полей, если ок =4 то валидно
        var ok = 0;
        for (key in reg_obj) {
            if (reg_obj[key] == "") {
                //вывести сообщение о том что поле не заполнено
                alert("Заполните поле " + key);
            } else {
                ok += 1;
            }
        }

        // если все параметры заполнены(ок = 4), отправляем запрос
        if(ok == 4) {
            var reg_str = JSON.stringify(reg_obj);
            var resp = "";
            $.ajax(
                {
                    type: "POST",
                    url: ("http://test/menu/auth_form.php"),
                    async: false,
                    data: {reg_str},
                    success: function (data) {
                        resp = data ;
                    }
                })
            if (resp == "Создали юзера") {
                alert (resp)
            };
            if (resp == "Такой юзер уже есть") {
                alert (resp)
            };
        }

    };


