<?php declare(strict_types=1);

namespace Acme;

use Psr\Http\Message\RequestInterface;

class RequestBuilder
{
    /** @var RequestInterface */
    private $req;

    /** @var string[] */
    private $arrBody = [];

    public function __construct($endPoint)
    {
        $this->req = new \GuzzleHttp\Psr7\Request('post', $endPoint);
        $this->init();
    }

    private function init()
    {
        $fields = ['user_id', 'articleId', 'verbose'];
        foreach ($fields as $name) {
            $this->arrBody[$name] = null;
        }
    }

    public function build(): RequestInterface
    {
        $this->validate();
        return $this->req
            ->withHeader('X-HEADER01', 'header01')
            ->withBody(
                \GuzzleHttp\Psr7\stream_for(http_build_query($this->arrBody, '', '&'))
            );
    }

    public function validate()
    {
        if ('' === ($this->arrBody['user_id'] ?? '')) {
            throw new \InvalidArgumentException('user_id is required');
        }
    }

    public function setUserId(string $userId): self
    {
        $this->arrBody['user_id'] = $userId;
        return $this;
    }

    public function setArticleId(string $articleId): self
    {
        $this->arrBody['article_id'] = $articleId;
        return $this;
    }

    public function setVerbose(bool $enable): self
    {
        $this->arrBody['verbose'] = $enable ? 'true' : 'false';
        return $this;
    }
}
