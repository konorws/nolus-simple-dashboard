<?php

namespace App\Action;

use App\Service\NolusAPIService;
use Symfony\Component\HttpFoundation\Request;

class DashboardAction extends AbstractAction
{

    public function __invoke(Request $request, NolusAPIService $API)
    {
        $APIS = [
            NolusAPIService::NOLUS_API => 'NOLUS API',
            NolusAPIService::KONORWS_API => 'Konorws API'
        ];

        $activeAPI = NolusAPIService::KONORWS_API;
        $activeAPIName = 'Konorws API';
        if($request->query->has('api') && isset($APIS[$request->query->get('api')])) {
            $activeAPI = $request->query->get('api');
            $activeAPIName = $APIS[$request->query->get('api')];
        }

        $API->setAPI($activeAPI);

        $LastBlock = $API->blockLast();
        $pool = $API->pool();
        $supply = $API->supply();
        $communityPool = $API->communityPool();

        $boundedPercent = (int)$pool['pool']['bonded_tokens'] / $supply;
        $boundedPercent = number_format($boundedPercent * 100, 4);

        $stackingParams = $API->stakingParams();
        $nodeInfo = $API->nodeInfo();
        $activeValidators = $API->validators();
        $inactiveValidators = $API->validators(false);
        $prevBlock = $API->heightInfo($LastBlock['block']['header']['height'] - 1);

        $timePrev = new \DateTime($prevBlock['hist']['header']['time']);
        $lastTime = new \DateTime($LastBlock['block']['header']['time']);
        $diffTime = $lastTime->diff($timePrev);
        $blockTime = number_format($diffTime->s + $diffTime->f, 3);

        $data = [
            'boundedPercent' => $boundedPercent,
            'tokenBounded' => number_format($pool['pool']['bonded_tokens'] / 1000000, 0, ',', ' '),
            'tokenSupply' => number_format($supply / 1000000, 0, ',', ' '),
            'tokenCommunity' => number_format($communityPool / 1000000,0, ',', ' '),
            'activeValidators' => (int)$activeValidators['pagination']['total'],
            'inactiveValidators' => (int)$inactiveValidators['pagination']['total'],
            'totalValidators' => (int)$activeValidators['pagination']['total'] + (int)$inactiveValidators['pagination']['total'],
            'maxValidators' => $stackingParams['params']['max_validators'],
            'APIS' => $APIS,
            'API_URL' => $activeAPI,
            'API_NAME' => $activeAPIName,
            'height' => $LastBlock['block']['header']['height'],
            'block_time' => $blockTime,
            'chain_id' => $LastBlock['block']['header']['chain_id'],
            "app_version" => $nodeInfo['application_version']['version'],
            "app_name" => $nodeInfo['application_version']['app_name'],
            "version" => $nodeInfo['application_version']['version'],
            "sdk_version" => $nodeInfo['application_version']['cosmos_sdk_version'],
            "sync" => $API->syncing(),
        ];

        return $this->render('dashboard/view.html.twig', $data);
    }
}
