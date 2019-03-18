<?php

/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 14.03.2019
 * Time: 15:09
 * Преобразует распаршеннный ранее файл ексель в пхп массив к массиву с урлами
 */
class arrayTransform
{
    protected $rowArray;
    protected $postbacks;

    public function __construct(array $array)
    {
        $this->rowArray = $array;
    }

    /**
     * функция для вытаскивания значений ключей массив в строку
     * используется для передачи запроса в бд где ключи это название колонок таблицы
     *  $a = [  'first_column'=> ['first_cell','second_cell'],
                'second_column'=> ['first_cell2','second_cell2'],
                'third_column'=> ['first_cell3','second_cell3']

            ];
     * @return  string (вернет `first_column`, `second_column`)

     */
    public function printColumnName():string
    {
        $array = $this->rowArray;
        $str = '';
        foreach ($array as $key=>$value){
            $str .= sprintf('`%s`',$key).', ';
        }
        $str = mb_substr($str, 0, -2);
        return $str;
    }

    /**
     * функция аналогичная вышестоящей, возвращает значения для ячеек таблицы
     * используется для передачи запроса в бд где ключи это название колонок таблицы
     *  $a = [  'first_column'=> ['first_cell','second_cell'],
                'second_column'=> ['first_cell2','second_cell2'],
                'third_column'=> ['first_cell3','second_cell3']

            ];
     * @return  string (вернет 'first_cell', 'second_cell', 'first_cell', 'second_cell', 'first_cell', 'second_cell', 'first_cell', 'second_cell')
     */
    public function printColumnValue():string
    {
        $array = $this->rowArray;
        $str = '';
        foreach ($array as $column){
            foreach ($column as $cell){
                $str .= sprintf('\'%s\'',$cell).', ';
            }
        }
        $str = mb_substr($str, 0, -2);
        return $str;
    }

    /** @return array   возвращает массив ссылок */
    public function getPostbackArray():array
    {
        $rowData = $this->rowArray;
        $urls = []; // запишем в нее наши потсбэки и пиксели, писаться будет всё с фильтром по наличию определенных значнеий в строке
        $rows = count($rowData);

        for ($i = 0; $i < $rows; $i++) {

            $line = $rowData[$i];

            $line = explode(';', $line);

            if ($line[5] !== '' && $line[5] !== 'request_url' && $line[16] !== ''){
                $type  = $line[5];
                $params   = $line[16];
                $params    = trim($params, '"');
                $params    =  str_replace('""','"', $params);
                $params    = $this->encodeJsonBasket($params);

                if ($type === 'postback'){
                    $urlPath = "http://cityads.ru";
                    $url = $urlPath . $params;
                    $urls[] = $url;

                } elseif ($type === 'pixel'){
                    $urlPath = "http://cityadspix.com";
                    $url = $urlPath . $params;
                    $urls[] = $url;
                }

            }
        }

        return $urls;
    }

    /** @return string   возвращает ссылку с закодированной корзиной
     * @param string   $originalUrl исходная ссылка
     */
    protected function encodeJsonBasket($originalUrl):string
    {
        if((stripos($originalUrl, '&basket=') === false)){

            return $originalUrl;

        } else{
            // basket encode start
            $linkArr = explode('&basket=', $originalUrl);

            $encode_bas = urlencode($linkArr[1]);

            $url = $linkArr[0].'&basket='.$encode_bas;

            // basket encode end

            return $url;
        }


    }
}