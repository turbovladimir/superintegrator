<?php


namespace App\Services\Tools;


use App\Response\ResponseMessage;
use App\Services\FineDiff\FineDiff;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TextComparator implements Tool
{
    public function getToolInfo(): array {
        return [
            'title' => 'Text comparator',
            'description' => 'Сравнивает два текста'
        ];
    }

    public function process(array $parameters, $action = null) {

        if (empty($parameters['from']) || empty($parameters['to'])) {
            throw new BadRequestHttpException('You must fill a both texts area');
        }

        $diff = new FineDiff($parameters['from'], $parameters['to']);
        $result = $diff->renderDiffToHTML();

        return new ResponseMessage($result);
    }
}