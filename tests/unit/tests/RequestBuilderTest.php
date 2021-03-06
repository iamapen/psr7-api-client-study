<?php declare(strict_types=1);

namespace Acme;

use PHPUnit\Framework\TestCase;

class RequestBuilderTest extends TestCase
{

    function test_construct()
    {
        $endPoint = 'http://user:pass@hoge.example.com:8080/foo/bar?piyo=puyo#cake';
        $sut = RequestBuilder::createByEndpoint($endPoint);
        $req = $sut->setUserId(10)->build();

        $this->assertInstanceOf(\Psr\Http\Message\RequestInterface::class, $req);
        $this->assertSame('http', $req->getUri()->getScheme());
        $this->assertSame('user:pass', $req->getUri()->getUserInfo());
        $this->assertSame('hoge.example.com', $req->getUri()->getHost());
        $this->assertSame(8080, $req->getUri()->getPort());
        $this->assertSame('user:pass@hoge.example.com:8080', $req->getUri()->getAuthority());
        $this->assertSame('/foo/bar', $req->getUri()->getPath());
        $this->assertSame('piyo=puyo', $req->getUri()->getQuery());
        $this->assertSame('cake', $req->getUri()->getFragment());
        $this->assertSame('/foo/bar?piyo=puyo', $req->getRequestTarget());

        $this->assertSame('header01', $req->getHeaderLine('X-HEADER01'));
    }

    function test_createByEnv()
    {
        $sut = RequestBuilder::create();
        $req = $sut->setUserId(10)->build();

        $this->assertInstanceOf(\Psr\Http\Message\RequestInterface::class, $req);
        $this->assertSame('http', $req->getUri()->getScheme());
        $this->assertSame('example.com', $req->getUri()->getHost());
        $this->assertSame('/v2/abc', $req->getUri()->getPath());
        $this->assertSame('/v2/abc', $req->getRequestTarget());
    }

    function test_build_body()
    {
        $sut = RequestBuilder::createByEndpoint('http://example.com/abc');

        $req = $sut->setUserId(10)
            ->setVerbose(true)
            ->build();

        $this->assertSame('user_id=10&verbose=true', (string)$req->getBody());
    }

    function test_build_順序が固定できること()
    {
        $sut = RequestBuilder::createByEndpoint('http://example.com/abc');

        $req1 = $sut->setUserId(10)
            ->setVerbose(true)
            ->build();
        $req2 = $sut->setVerbose(true)
            ->setUserId(10)
            ->build();
        $this->assertSame((string)$req1->getBody(), (string)$req2->getBody());
    }

    function test_build_必須項目がないと例外が起きること()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('user_id is required');

        $sut = RequestBuilder::createByEndpoint('http://example.com/abc');
        $req = $sut->build();
    }
}
