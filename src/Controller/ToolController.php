<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 02.09.2019
 * Time: 13:45
 */

namespace App\Controller;

use App\Exceptions\Warning;
use App\Response\ResponseMessage;

use App\Services\ToolNotFoundException;
use App\Services\ToolsCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class ToolController extends AbstractController
{
    public const PAGE_MAIN = 'base';
    
    public const ACTION_GET_XML_PAGE = 'xml';
    public const ACTION_GET_JSON_PAGE = 'json';
    public const ACTION_NEW = 'new';
    public const ACTION_UPLOAD = 'upload';
    public const ACTION_DELETE = 'delete';
    public const ACTION_SAVE = 'save';
    public const ACTION_SEARCH = 'search';
    public const ACTION_GET_LIST = 'list';

    private $logger;
    private $tools;
    private $isProduction;

    /**
     * ToolController constructor.
     * @param LoggerInterface $logger
     * @param ToolsCollection $tools
     * @param string $environment
     */
    public function __construct(
        LoggerInterface $logger,
        ToolsCollection $tools,
        string $environment
    ) {
        $this->isProduction = $environment === 'prod';
        $this->tools = $tools;
        $this->logger = $logger;
    }
    
    /**
     * @param         $tool
     * @param         $action
     * @param Request $request
     *
     * @return Response
     */
    public function index($tool, $action, Request $request)
    {
        try {
            return $this->handle($request, $tool, $action);
        } catch (Warning $warning) {
            $message = (new ResponseMessage())->addWarning($warning->getMessage());
        } catch (ToolNotFoundException $exception) {
            return $this->redirect('/');
        } catch (\Exception $exception) {
            $message = new ResponseMessage('Обнаружена ошибка!', 'Невероятно но такое бывает', ResponseMessage::TYPE_DANGER);
            
            if (!$this->isProduction) {
                $message->addError('Details',
                        sprintf('message: %s, file: %s(%d)',
                            $exception->getMessage(),
                            $exception->getFile(),
                            $exception->getLine()));
            }
        }

        return $this->render('base.html.twig', $message->getData());
    }

    /**
     * @return Response
     */
    public function renderMainPage() {
        return $this->render('base.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function xmlPage(Request $request) : Response {
        $data = $this->tools->getToolByName('xml_emulator')
            ->process(['key' => $request->get('key')], self::ACTION_GET_XML_PAGE)->getData();
        $xml = reset($data);

        return new Response($xml, 200, ['Content-type' => 'text/xml']);
    }
    
    /**
     * @param Request $request
     * @param string $toolName
     * @param string|null $action
     *
     * @return Response
    * @throws
     */
    private function handle(Request $request, string $toolName, $action = null) : Response {
        $tool = $this->tools->getToolByName($toolName);

        if ($request->getMethod() === Request::METHOD_POST) {
            if (!$this->isCsrfTokenValid('token', $request->request->get('token'))) {
                $this->logger->alert(sprintf('Something nasty is going one from ip `%s`', $request->getClientIp()));
                throw new \InvalidArgumentException('Incorrect or empty csrf token!');
            }

             parse_str($request->getContent(), $requestParameters);

            if (!empty($requestParameters['action']) || $action) {
                $responseData = $tool->process($requestParameters, $requestParameters['action'] ?? $action);
            }
        }

        $pathToTemplate =  $action ? "{$toolName}/{$action}" : "{$toolName}/index";
        $pathToTemplate = "tools/{$pathToTemplate}.html.twig";
        $pageParameters = array_merge(isset($responseData) ? $responseData->getData() : [], $tool->getToolInfo());

        return $this->render($pathToTemplate, $pageParameters);
    }
}