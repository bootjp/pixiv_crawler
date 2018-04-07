<?php

namespace pixiv;

use GuzzleHttp\Client;

class Crawl implements CrawlInterface
{
    protected $client;
    protected $id;
    protected $password;
    protected static $config = [
        'uri' => [
            'loginToken' => 'https://accounts.pixiv.net/login',
            'loginSessionCreate' => 'https://accounts.pixiv.net/login_bc.gif',
            'loginApi' => 'https://accounts.pixiv.net/api/login?lang=ja',
            'loginCheck' => 'https://www.pixiv.net/'
        ]
    ];

    public function __construct(string $id, string $password)
    {
        $this->id = $id;
        $this->password = $password;

        $this->client = new Client([
            'debug' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36'
            ]
        ]);
    }

    private function fetchLoginToken() :string
    {
        $body = $this->client->get(self::$config['uri']['loginToken'])->getBody()->getContents();
        $this->client->get(self::$config['uri']['loginSessionCreate']);

        preg_match('#<input type="hidden" name="post_key" value="(?<loginToken>.+?)">#', $body, $marches);

        return $marches['loginToken'];
    }

    public function login():bool
    {
        $token = $this->fetchLoginToken();
        $response = $this->client->post(self::$config['uri']['loginApi'], [
            'form_params' => [
                'pixiv_id' => $this->id,
                'password' => $this->password,
                'captcha' => '',
                'g_recaptcha_response' => '',
                'post_key' => $token,
                'source' => 'pc',
                'ref' => 'wwwtop_accounts_index',
                'return_to' => 'https://www.pixiv.net/',
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $body = $this->client->get(self::$config['uri']['loginCheck'])->getBody()->getContents();

        return mb_strpos($body, 'ログアウトします。よろしいですか？') !== false;
    }

    public function fetchImageByTag(string $string): array
    {
        return [];
    }
}

