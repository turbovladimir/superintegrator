<?php


namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ToolControllerTest extends WebTestCase
{
    /**
     * @dataProvider provideUrls
     */
    public function testUrl(string $url) {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->getStatusCode() === 200);
    }

    public function provideUrls()
    {
        return [
            ['/'],
            ['/login'],
            ['/blog'],
            ['/tools'],
            ['/tools/geo'],
            ['/tools/ali_orders'],
            ['/tools/xml_emulator'],
            ['/tools/cryptor'],
            ['/tools/text_comparator'],
        ];
    }
}