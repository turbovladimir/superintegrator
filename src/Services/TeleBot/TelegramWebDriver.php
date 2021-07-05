<?php


namespace App\Services\TeleBot;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\InvalidBotTokenException;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\TelegramLog;

/**
 * Class Request
 *
 * @method static ServerResponse getUpdates(array $data)                      Use this method to receive incoming updates using long polling (wiki). An Array of Update objects is returned.
 * @method static ServerResponse setWebhook(array $data)                      Use this method to specify a url and receive incoming updates via an outgoing webhook. Whenever there is an update for the bot, we will send an HTTPS POST request to the specified url, containing a JSON-serialized Update. In case of an unsuccessful request, we will give up after a reasonable amount of attempts. Returns true.
 * @method static ServerResponse deleteWebhook()                              Use this method to remove webhook integration if you decide to switch back to getUpdates. Returns True on success. Requires no parameters.
 * @method static ServerResponse getWebhookInfo()                             Use this method to get current webhook status. Requires no parameters. On success, returns a WebhookInfo object. If the bot is using getUpdates, will return an object with the url field empty.
 * @method static ServerResponse getMe()                                      A simple method for testing your bot's auth token. Requires no parameters. Returns basic information about the bot in form of a User object.
 * @method static ServerResponse forwardMessage(array $data)                  Use this method to forward messages of any kind. On success, the sent Message is returned.
 * @method static ServerResponse sendPhoto(array $data)                       Use this method to send photos. On success, the sent Message is returned.
 * @method static ServerResponse sendAudio(array $data)                       Use this method to send audio files, if you want Telegram clients to display them in the music player. Your audio must be in the .mp3 format. On success, the sent Message is returned. Bots can currently send audio files of up to 50 MB in size, this limit may be changed in the future.
 * @method static ServerResponse sendDocument(array $data)                    Use this method to send general files. On success, the sent Message is returned. Bots can currently send files of any type of up to 50 MB in size, this limit may be changed in the future.
 * @method static ServerResponse sendSticker(array $data)                     Use this method to send .webp stickers. On success, the sent Message is returned.
 * @method static ServerResponse sendVideo(array $data)                       Use this method to send video files, Telegram clients support mp4 videos (other formats may be sent as Document). On success, the sent Message is returned. Bots can currently send video files of up to 50 MB in size, this limit may be changed in the future.
 * @method static ServerResponse sendAnimation(array $data)                   Use this method to send animation files (GIF or H.264/MPEG-4 AVC video without sound). On success, the sent Message is returned. Bots can currently send animation files of up to 50 MB in size, this limit may be changed in the future.
 * @method static ServerResponse sendVoice(array $data)                       Use this method to send audio files, if you want Telegram clients to display the file as a playable voice message. For this to work, your audio must be in an .ogg file encoded with OPUS (other formats may be sent as Audio or Document). On success, the sent Message is returned. Bots can currently send voice messages of up to 50 MB in size, this limit may be changed in the future.
 * @method static ServerResponse sendVideoNote(array $data)                   Use this method to send video messages. On success, the sent Message is returned.
 * @method static ServerResponse sendMediaGroup(array $data)                  Use this method to send a group of photos or videos as an album. On success, an array of the sent Messages is returned.
 * @method static ServerResponse sendLocation(array $data)                    Use this method to send point on the map. On success, the sent Message is returned.
 * @method static ServerResponse editMessageLiveLocation(array $data)         Use this method to edit live location messages sent by the bot or via the bot (for inline bots). A location can be edited until its live_period expires or editing is explicitly disabled by a call to stopMessageLiveLocation. On success, if the edited message was sent by the bot, the edited Message is returned, otherwise True is returned.
 * @method static ServerResponse stopMessageLiveLocation(array $data)         Use this method to stop updating a live location message sent by the bot or via the bot (for inline bots) before live_period expires. On success, if the message was sent by the bot, the sent Message is returned, otherwise True is returned.
 * @method static ServerResponse sendVenue(array $data)                       Use this method to send information about a venue. On success, the sent Message is returned.
 * @method static ServerResponse sendContact(array $data)                     Use this method to send phone contacts. On success, the sent Message is returned.
 * @method static ServerResponse sendPoll(array $data)                        Use this method to send a native poll. A native poll can't be sent to a private chat. On success, the sent Message is returned.
 * @method static ServerResponse sendDice(array $data)                        Use this method to send a dice, which will have a random value from 1 to 6. On success, the sent Message is returned.
 * @method static ServerResponse sendChatAction(array $data)                  Use this method when you need to tell the user that something is happening on the bot's side. The status is set for 5 seconds or less (when a message arrives from your bot, Telegram clients clear its typing status). Returns True on success.
 * @method static ServerResponse getUserProfilePhotos(array $data)            Use this method to get a list of profile pictures for a user. Returns a UserProfilePhotos object.
 * @method static ServerResponse getFile(array $data)                         Use this method to get basic info about a file and prepare it for downloading. For the moment, bots can download files of up to 20MB in size. On success, a File object is returned. The file can then be downloaded via the link https://api.telegram.org/file/bot<token>/<file_path>, where <file_path> is taken from the response. It is guaranteed that the link will be valid for at least 1 hour. When the link expires, a new one can be requested by calling getFile again.
 * @method static ServerResponse kickChatMember(array $data)                  Use this method to kick a user from a group, a supergroup or a channel. In the case of supergroups and channels, the user will not be able to return to the group on their own using invite links, etc., unless unbanned first. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Returns True on success.
 * @method static ServerResponse unbanChatMember(array $data)                 Use this method to unban a previously kicked user in a supergroup or channel. The user will not return to the group or channel automatically, but will be able to join via link, etc. The bot must be an administrator for this to work. Returns True on success.
 * @method static ServerResponse restrictChatMember(array $data)              Use this method to restrict a user in a supergroup. The bot must be an administrator in the supergroup for this to work and must have the appropriate admin rights. Pass True for all permissions to lift restrictions from a user. Returns True on success.
 * @method static ServerResponse promoteChatMember(array $data)               Use this method to promote or demote a user in a supergroup or a channel. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Pass False for all boolean parameters to demote a user. Returns True on success.
 * @method static ServerResponse setChatAdministratorCustomTitle(array $data) Use this method to set a custom title for an administrator in a supergroup promoted by the bot. Returns True on success.
 * @method static ServerResponse setChatPermissions(array $data)              Use this method to set default chat permissions for all members. The bot must be an administrator in the group or a supergroup for this to work and must have the can_restrict_members admin rights. Returns True on success.
 * @method static ServerResponse exportChatInviteLink(array $data)            Use this method to generate a new invite link for a chat. Any previously generated link is revoked. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Returns the new invite link as String on success.
 * @method static ServerResponse setChatPhoto(array $data)                    Use this method to set a new profile photo for the chat. Photos can't be changed for private chats. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Returns True on success.
 * @method static ServerResponse deleteChatPhoto(array $data)                 Use this method to delete a chat photo. Photos can't be changed for private chats. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Returns True on success.
 * @method static ServerResponse setChatTitle(array $data)                    Use this method to change the title of a chat. Titles can't be changed for private chats. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Returns True on success.
 * @method static ServerResponse setChatDescription(array $data)              Use this method to change the description of a group, a supergroup or a channel. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Returns True on success.
 * @method static ServerResponse pinChatMessage(array $data)                  Use this method to pin a message in a supergroup or a channel. The bot must be an administrator in the chat for this to work and must have the ‘can_pin_messages’ admin right in the supergroup or ‘can_edit_messages’ admin right in the channel. Returns True on success.
 * @method static ServerResponse unpinChatMessage(array $data)                Use this method to unpin a message in a supergroup or a channel. The bot must be an administrator in the chat for this to work and must have the ‘can_pin_messages’ admin right in the supergroup or ‘can_edit_messages’ admin right in the channel. Returns True on success.
 * @method static ServerResponse leaveChat(array $data)                       Use this method for your bot to leave a group, supergroup or channel. Returns True on success.
 * @method static ServerResponse getChat(array $data)                         Use this method to get up to date information about the chat (current name of the user for one-on-one conversations, current username of a user, group or channel, etc.). Returns a Chat object on success.
 * @method static ServerResponse getChatAdministrators(array $data)           Use this method to get a list of administrators in a chat. On success, returns an Array of ChatMember objects that contains information about all chat administrators except other bots. If the chat is a group or a supergroup and no administrators were appointed, only the creator will be returned.
 * @method static ServerResponse getChatMembersCount(array $data)             Use this method to get the number of members in a chat. Returns Int on success.
 * @method static ServerResponse getChatMember(array $data)                   Use this method to get information about a member of a chat. Returns a ChatMember object on success.
 * @method static ServerResponse setChatStickerSet(array $data)               Use this method to set a new group sticker set for a supergroup. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Use the field can_set_sticker_set optionally returned in getChat requests to check if the bot can use this method. Returns True on success.
 * @method static ServerResponse deleteChatStickerSet(array $data)            Use this method to delete a group sticker set from a supergroup. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Use the field can_set_sticker_set optionally returned in getChat requests to check if the bot can use this method. Returns True on success.
 * @method static ServerResponse answerCallbackQuery(array $data)             Use this method to send answers to callback queries sent from inline keyboards. The answer will be displayed to the user as a notification at the top of the chat screen or as an alert. On success, True is returned.
 * @method static ServerResponse answerInlineQuery(array $data)               Use this method to send answers to an inline query. On success, True is returned.
 * @method static ServerResponse setMyCommands(array $data)                   Use this method to change the list of the bot's commands. Returns True on success.
 * @method static ServerResponse getMyCommands()                              Use this method to get the current list of the bot's commands. Requires no parameters. Returns Array of BotCommand on success.
 * @method static ServerResponse editMessageText(array $data)                 Use this method to edit text and game messages sent by the bot or via the bot (for inline bots). On success, if edited message is sent by the bot, the edited Message is returned, otherwise True is returned.
 * @method static ServerResponse editMessageCaption(array $data)              Use this method to edit captions of messages sent by the bot or via the bot (for inline bots). On success, if edited message is sent by the bot, the edited Message is returned, otherwise True is returned.
 * @method static ServerResponse editMessageMedia(array $data)                Use this method to edit audio, document, photo, or video messages. On success, if the edited message was sent by the bot, the edited Message is returned, otherwise True is returned.
 * @method static ServerResponse editMessageReplyMarkup(array $data)          Use this method to edit only the reply markup of messages sent by the bot or via the bot (for inline bots). On success, if edited message is sent by the bot, the edited Message is returned, otherwise True is returned.
 * @method static ServerResponse stopPoll(array $data)                        Use this method to stop a poll which was sent by the bot. On success, the stopped Poll with the final results is returned.
 * @method static ServerResponse deleteMessage(array $data)                   Use this method to delete a message, including service messages, with certain limitations. Returns True on success.
 * @method static ServerResponse getStickerSet(array $data)                   Use this method to get a sticker set. On success, a StickerSet object is returned.
 * @method static ServerResponse uploadStickerFile(array $data)               Use this method to upload a .png file with a sticker for later use in createNewStickerSet and addStickerToSet methods (can be used multiple times). Returns the uploaded File on success.
 * @method static ServerResponse createNewStickerSet(array $data)             Use this method to create new sticker set owned by a user. The bot will be able to edit the created sticker set. Returns True on success.
 * @method static ServerResponse addStickerToSet(array $data)                 Use this method to add a new sticker to a set created by the bot. Returns True on success.
 * @method static ServerResponse setStickerPositionInSet(array $data)         Use this method to move a sticker in a set created by the bot to a specific position. Returns True on success.
 * @method static ServerResponse deleteStickerFromSet(array $data)            Use this method to delete a sticker from a set created by the bot. Returns True on success.
 * @method static ServerResponse setStickerSetThumb(array $data)              Use this method to set the thumbnail of a sticker set. Animated thumbnails can be set for animated sticker sets only. Returns True on success.
 * @method static ServerResponse sendInvoice(array $data)                     Use this method to send invoices. On success, the sent Message is returned.
 * @method static ServerResponse answerShippingQuery(array $data)             If you sent an invoice requesting a shipping address and the parameter is_flexible was specified, the Bot API will send an Update with a shipping_query field to the bot. Use this method to reply to shipping queries. On success, True is returned.
 * @method static ServerResponse answerPreCheckoutQuery(array $data)          Once the user has confirmed their payment and shipping details, the Bot API sends the final confirmation in the form of an Update with the field pre_checkout_query. Use this method to respond to such pre-checkout queries. On success, True is returned.
 * @method static ServerResponse setPassportDataErrors(array $data)           Informs a user that some of the Telegram Passport elements they provided contains errors. The user will not be able to re-submit their Passport to you until the errors are fixed (the contents of the field for which you returned the error must change). Returns True on success. Use this if the data submitted by the user doesn't satisfy the standards your service requires for any reason. For example, if a birthday date seems invalid, a submitted document is blurry, a scan shows evidence of tampering, etc. Supply some details in the error message to make sure the user knows how to correct the issues.
 * @method static ServerResponse sendGame(array $data)                        Use this method to send a game. On success, the sent Message is returned.
 * @method static ServerResponse setGameScore(array $data)                    Use this method to set the score of the specified user in a game. On success, if the message was sent by the bot, returns the edited Message, otherwise returns True. Returns an error, if the new score is not greater than the user's current score in the chat and force is False.
 * @method static ServerResponse getGameHighScores(array $data)               Use this method to get data for high score tables. Will return the score of the specified user and several of his neighbors in a game. On success, returns an Array of GameHighScore objects.
 */
class TelegramWebDriver
{
    private static $config;

    public static function init(string $botName, string $apiKey) {
        self::$config = ['bot_name' => $botName, 'api_key' => $apiKey];
    }

    public static function __callStatic($action, array $data): ServerResponse {
        array_unshift($data, $action);
        return call_user_func_array('static::send', $data);
    }


    public static function send($action, array $data = []) {
        $bot_username = self::$config['bot_name'];

        if (defined('PHPUNIT_TESTSUITE')) {
            $fake_response = self::generateGeneralFakeServerResponse($data);

            return new ServerResponse($fake_response, $bot_username);
        }

        $raw_response = self::execute($action, $data);
        $response = json_decode($raw_response, true);

        if (null === $response) {
            TelegramLog::debug($raw_response);
            throw new TelegramException('Telegram returned an invalid response!');
        }

        $response = new ServerResponse($response, $bot_username);

        if (!$response->isOk() && $response->getErrorCode() === 401 && $response->getDescription() === 'Unauthorized') {
            throw new InvalidBotTokenException();
        }

        if ($response->isOk() && ($message = $response->getResult()) && ($message instanceof Message) && $poll = $message->getPoll()) {
            DB::insertPollRequest($poll);
        }

        return $response;
    }

    public static function emptyResponse()
    {
        return new ServerResponse(['ok' => true, 'result' => true], null);
    }

    private static function generateGeneralFakeServerResponse(array $data = []) {
        $fake_response = ['ok' => true]; // :)

        if ($data === []) {
            $fake_response['result'] = true;
        }

        //some data to let iniatilize the class method SendMessage
        if (isset($data['chat_id'])) {
            $data['message_id'] = '1234';
            $data['date'] = '1441378360';
            $data['from'] = [
                'id' => 123456789,
                'first_name' => 'botname',
                'username' => 'namebot',
            ];
            $data['chat'] = ['id' => $data['chat_id']];

            $fake_response['result'] = $data;
        }

        return $fake_response;
    }

    private static function execute($action, array $data = []) {
        $result = null;
        $response = null;
        $request_params = self::setUpRequestParams($data);
        $request_params['debug'] = TelegramLog::getDebugLogTempStream();

        try {
            $response = (new Client())->post(
                '/bot' . self::$config['api_key'] . '/' . $action,
                $request_params
            );
            $result = (string)$response->getBody();

            //Logging getUpdates Update
            if ($action === 'getUpdates') {
                TelegramLog::update($result);
            }
        } catch (RequestException $e) {
            $response = null;
            $result = $e->getResponse() ? (string)$e->getResponse()->getBody() : '';
        } finally {
            //Logging verbose debug output
            if (TelegramLog::$always_log_request_and_response || $response === null) {
                TelegramLog::debug('Request data:' . PHP_EOL . print_r($data, true));
                TelegramLog::debug('Response data:' . PHP_EOL . $result);
                TelegramLog::endDebugLogTempStream('Verbose HTTP Request output:' . PHP_EOL . '%s' . PHP_EOL);
            }
        }

        return $result;
    }
}