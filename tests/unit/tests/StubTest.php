<?php declare(strict_types=1);

use Acme\RequestBuilder;

class StubTest extends \PHPUnit\Framework\TestCase
{
    /**
     * レスポンスオブジェクトを作ることになったらスタブが役に立つ
     */
    function test_スタブの例()
    {
        $mockRes = new \GuzzleHttp\Psr7\Response(200, ['X-RES-HEADER' => 'xxx'], '{foo:bar}');
        $mockHandler = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([$mockRes]));
        $client = new \GuzzleHttp\Client(['handler' => $mockHandler]);

        $req = RequestBuilder::create()->setUserId(10)->build();
        $res = $client->send($req);

        $this->assertSame(200, $res->getStatusCode());
        $this->assertSame('xxx', $res->getHeaderLine('X-RES-HEADER'));
        $this->assertSame('{foo:bar}', (string)$res->getBody());
    }
}
