<?php

namespace App\Action\API\Block;

use App\Action\AbstractAction;
use App\Service\NolusAPIService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GetAction extends AbstractAction
{

    public function __invoke(Request $request, NolusAPIService $API)
    {
        $APIS = [
            NolusAPIService::NOLUS_API => 'NOLUS API',
            NolusAPIService::KONORWS_API => 'Konorws API'
        ];

        $activeAPI = NolusAPIService::KONORWS_API;
        if($request->query->has('api') && isset($APIS[$request->query->get('api')])) {
            $activeAPI = $request->query->get('api');
        }

        $API->setAPI($activeAPI);

        $lastBlock = $API->blockLast();
        $prevBlock = $API->heightInfo($lastBlock['block']['header']['height'] - 1);

        $timePrev = new \DateTime($prevBlock['hist']['header']['time']);
        $lastTime = new \DateTime($lastBlock['block']['header']['time']);
        $diffTime = $lastTime->diff($timePrev);
        $blockTime = number_format($diffTime->s + $diffTime->f, 3);

        $data = [
            'time' => $blockTime,
            'height' => $lastBlock['block']['header']['height']
        ];

        return new JsonResponse($data);
    }
}
