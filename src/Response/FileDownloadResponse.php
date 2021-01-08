<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.11.2019
 * Time: 16:56
 */

namespace App\Response;


/**
 * Формирует инстанс класса для выгрузки файла в браузер
 *
 * Class FileDownloadResponse
 *
 * @package App\Controller\Response
 */
class FileDownloadResponse extends ResponseData
{
    const TYPE_FILE_DOWNLOAD = 'file';

    /**
     * @todo дописать удаление после отправки файла
     * FileDownloadResponse constructor.
     * @param string $filePath
     * @param string|null $fileName
     */
    public function __construct(string $filePath, $fileName = null) {
        $this->data = [self::TYPE_FILE_DOWNLOAD => ['path' => $filePath, 'name' => $fileName]];
    }
}