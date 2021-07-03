<?php

namespace Osiset\ShopifyApp\Traits;

use App\Jobs\AbstractShopifyWebhookJob;
use Illuminate\Http\Request;
use Illuminate\Http\Response as ResponseResponse;
use Illuminate\Support\Facades\Response;
use function Osiset\ShopifyApp\getShopifyConfig;

/**
 * Responsible for handling incoming webhook requests.
 */
trait WebhookController
{
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

        $jobClass::dispatch(
            $request->header('x-shopify-shop-domain'),
            AbstractShopifyWebhookJob::compressPayload($request->getContent())
        )->onQueue(getShopifyConfig('job_queues')['webhooks']);

        return Response::make('', 201);
    }
}
