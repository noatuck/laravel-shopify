<?php

namespace Osiset\ShopifyApp\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response as ResponseResponse;
use Illuminate\Support\Facades\Response;
use function Osiset\ShopifyApp\getShopifyConfig;

/**
 * Responsible for handling incoming webhook requests.
 */
trait WebhookController
{
    protected function compressPayload(string $payload)
    {
        $compressed = gzcompress($payload, 9);

        if ($compressed === false) {
            throw new \Exception('gzcompress() failed in compressPayload');
        }

        return bin2hex($compressed);
    }

    /**
     * Handles an incoming webhook.
     *
     * @param string  $type    The type of webhook
     * @param Request $request The request object.
     *
     * @return ResponseResponse
     */
    public function handle(string $type, Request $request): ResponseResponse
    {
        // Get the job class and dispatch
        $jobClass = getShopifyConfig('job_namespace').str_replace('-', '', ucwords($type, '-')).'Job';

        if (defined("{$jobClass}::DELAY_SECONDS")) {
            $delayTime = now()->addSecond(constant("{$jobClass}::DELAY_SECONDS"));
        } else {
            $delayTime = now();
        }

        $jobClass::dispatch(
            $request->header('x-shopify-shop-domain'),
            $this->compressPayload($request->getContent())
        )->delay($delayTime)->onQueue(getShopifyConfig('job_queues')['webhooks']);

        return Response::make('', 201);
    }
}
