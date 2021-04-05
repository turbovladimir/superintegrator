<?php


namespace App\Services\TeleBot;

use App\Entity\History;
use App\Repository\HistoryRepository;
use App\Services\TeleBot\Entity\InputData;
use App\Services\TeleBot\Exception\UnauthorisedUserException;
use App\Services\TeleBot\Exception\UnknownUserException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TelebotProcessor
{
    const COMMAND_LOGIN = '/login';
    const COMMAND_CLEAR_HISTORY = '/clear_history';
    const COMMAND_CREDENTIALS_NEW = '/credentials_new';
    const COMMAND_CREDENTIALS_GET = '/credentials_get';
    const COMMAND_CREDENTIALS_DELETE = '/credentials_delete';

    private $sender;
    private $logger;
    private $telebotLogDir;
    private $securityProvider;
    private $telebotHistoryRepo;
    private $entityManager;

    public function __construct(
        LoggerInterface $telebotLogger,
        SecurityProvider $securityProvider,
        Sender $sender,
        HistoryRepository $telebotHistoryRepo,
        EntityManagerInterface $entityManager,
        string $telebotLogDir
    ) {
        $this->sender = $sender;
        $this->securityProvider = $securityProvider;
        $this->logger = $telebotLogger;
        $this->telebotLogDir = $telebotLogDir;
        $this->telebotHistoryRepo = $telebotHistoryRepo;
        $this->entityManager = $entityManager;
    }

    public function process(InputData $inputData): string {
            try {
                $this->saveMessage($inputData);

                if ($inputData->isBotCommand()) {
                    if (!$inputData->textLike(self::COMMAND_LOGIN)) {
                        $this->securityProvider->checkUser($inputData->getUserId());
                    }

                    return $this->processCommand($inputData);
                }
            } catch (UnauthorisedUserException $exception) {
                $message = $exception->getMessage() . ' Please write: `' . self::COMMAND_LOGIN . ' name:password1234`';
                $this->sender->sendMessage($inputData->getChatId(), $message);
                return $message;
            } catch (UnknownUserException $exception) {
                $message = $exception->getMessage();
                $this->sender->sendMessage($inputData->getChatId(), $message);
                return $message;
            } catch (\InvalidArgumentException $exception) {
                $this->logger->error('InvalidArgumentException');
                throw $exception;
            } catch (\Throwable $exception) {
                $this->logger->critical("Undefined exception in file {$exception->getFile()}");
                throw $exception;
            }

            return 'Only bot command allow for using!';
    }

    public function debug(InputData $inputData) {
        $this->saveMessage($inputData);

        return $this->sender->sendMessage($inputData->getChatId(), $this->preFormat((string)$inputData), $this->createKeyboard());
    }

    private function saveMessage(InputData $inputData) {
        $this->logger->debug('save input data to db...', json_decode((string)$inputData, true));
        $history = new History();
        $history
            ->setChatId($inputData->getChatId())
            ->setMessageId($inputData->getMessageId())
            ->setMessageData((string)$inputData);
        $this->entityManager->persist($history);
        $this->entityManager->flush();
    }



    /**
     * @return false|string
     */
    public function fetchDebugLogs() {
        return file_get_contents("{$this->telebotLogDir}/telebot.log");
    }

    public function clearDebugLogs() {
        file_put_contents("{$this->telebotLogDir}/telebot.log", "");
    }

    /**
     * @param InputData $inputData
     * @return string
     * @throws \Throwable
     */
    private function processCommand(InputData $inputData) {
        switch ($inputData->getCommand()) {
            case self::COMMAND_CLEAR_HISTORY:
                return $this->deleteHistoryCommand($inputData);
            case self::COMMAND_LOGIN:
                 return $this->loginCommand($inputData);
            default:
                throw new \InvalidArgumentException('Command not found!');
        }
    }

    private function loginCommand(InputData $inputData) {
        $credentials = explode(' ', $inputData->getText())[1] ?? null;

        if (!$credentials) {
           throw new \InvalidArgumentException('Invalid login format!');
        }

        list($name, $pass) = explode(':', $credentials);

        if (!$name || !$pass) {
            throw new \InvalidArgumentException('Cannot parse name and password!');
        }

        $this->securityProvider->login($inputData->getUserId(), $name, $pass);
        $message = 'Login success!';
        $this->sender->sendMessage($inputData->getChatId(), $message);

        return $message;
    }

    /**
     * @param InputData $inputData
     * @throws \Throwable
     * @return string
     */
    private function deleteHistoryCommand(InputData $inputData) {
        $histories = $this->telebotHistoryRepo->findBy(['chatId' => $inputData->getChatId()]);

        if (empty($histories)) {
            return 'Nothing for deletion';
        }

        foreach ($histories as $history) {
            $this->entityManager->remove($history);
            $this->sender->deleteMessage($history->getChatId(), $history->getMessageId());
            sleep(1);
        }

        $this->entityManager->flush();

        return 'All message deleted!';
    }

    private function preFormat($message) : string {
        return nl2br('<pre>'.$message.'</pre>', false);
    }

    private function createKeyboard() : array {

        return [
            'inline_keyboard' => [
                [["text" => "My Button Text1", "callback_data" => "myCallbackData"], ["text" => "My Button Text2", "callback_data" => "myCallbackData"]]
            ]
        ];
    }
}