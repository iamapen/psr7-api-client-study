<?php declare(strict_types=1);

namespace Acme;

use Psr\Http\Message\RequestInterface;

class RequestBuilder
{
    const ENDPOINT_TERMINAL = '/abc';

    /** @var RequestInterface */
    private $req;

    /** @var string[] */
    private $arrBody = [];

    private function __construct($endpoint)
    {
        $this->req = new \GuzzleHttp\Psr7\Request('post', $endpoint);
        $this->init();
    }

    public static function createByEnv()
    {
        $endpoint = getenv('SAMPLE_API_ENDPOINT_PREFIX') . static::ENDPOINT_TERMINAL;
        return static::createByEndpoint($endpoint);
    }

    public static function createByEndpoint($endpoint)
    {
        return new static($endpoint);
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
