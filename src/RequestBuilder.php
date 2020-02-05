<?php declare(strict_types=1);

namespace Acme;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * とあるweb-APIのPSR-7 Request組み立て
 *
 * - 各種パラメタの知識をここにカプセル化する
 * - RequestInterfaceの実装はGuzzleのものを使っているが、
 *   将来的にzend-diactorosなどに差し替えられるようにPSR-7でやり取りする
 */
class RequestBuilder
{
    const ENDPOINT_TERMINAL = '/abc';

    /** @var RequestInterface */
    private $req;

    /** @var string[] application/x-www-form-urlencoded */
    private $arrBody = [];


    /**
     * @param string|UriInterface $endpoint
     */
    private function __construct($endpoint)
    {
        $this->req = new \GuzzleHttp\Psr7\Request('post', $endpoint);
        $this->init();
    }

    /**
     * endpointを環境変数から取得して作成する
     * @return static
     */
    public static function createByEnv(): self
    {
        $endpoint = getenv('SAMPLE_API_ENDPOINT_PREFIX') . static::ENDPOINT_TERMINAL;
        return static::createByEndpoint($endpoint);
    }

    /**
     * endpointを指定して作成する
     * @param $endpoint
     * @return static
     */
    public static function createByEndpoint($endpoint): self
    {
        return new static($endpoint);
    }

    private function init()
    {
        // application/x-www-form-urlencoded のkeyの順序を揃えたい
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

    /**
     * 必須チェックなど
     */
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
        // low-layerな表現形式はsetterでカプセル化する
        $this->arrBody['verbose'] = $enable ? 'true' : 'false';
        return $this;
    }
}
