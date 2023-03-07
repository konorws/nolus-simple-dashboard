<?php

namespace App\Service;

class NolusAPIService
{
    const NOLUS_API = 'https://net-rila.nolus.io:1317/';
    const KONORWS_API = 'http://65.109.3.210:1317/';

    protected string $API = self::KONORWS_API;

    public function setAPI(string $uri): void
    {
        $this->API = $uri;
    }

    public function syncing(): bool
    {
        $data =  $this->request('cosmos/base/tendermint/v1beta1/syncing');
        return $data['syncing'];
    }

    public function nodeInfo(): array
    {
        return $this->request('cosmos/base/tendermint/v1beta1/node_info');
    }

    public function pool(): array
    {
        return $this->request('/cosmos/staking/v1beta1/pool');
    }

    public function supply(): int
    {
        $coin = 'unls';
        $data = $this->request('cosmos/bank/v1beta1/supply');

        foreach ($data['supply'] as $denom) {
            if($denom['denom'] == $coin) {
                return $denom['amount'];
            }
        }

        return 0;
    }
    public function communityPool(): int
    {
        $coin = 'unls';
        $data = $this->request('cosmos/distribution/v1beta1/community_pool');

        foreach ($data['pool'] as $denom) {
            if($denom['denom'] == $coin) {
                return $denom['amount'];
            }
        }

        return 0;
    }

    public function blockLast(): array
    {
        $data = $this->request('cosmos/base/tendermint/v1beta1/blocks/latest');
        return $data;
    }

    public function validators(bool $active = true): array
    {
        $status = $active ? 'BOND_STATUS_BONDED' : 'BOND_STATUS_UNBONDED';
        return $this->request('cosmos/staking/v1beta1/validators?status='.$status);
    }

    public function stakingParams(): array
    {
        return $this->request('cosmos/staking/v1beta1/params');
    }

    public function heightInfo(int $height): array
    {
        return  $this->request('cosmos/staking/v1beta1/historical_info/'. $height);
    }


    private function request(string $uri, array $params = []): array
    {
        $json = file_get_contents($this->API . $uri);

        return json_decode($json, true);
    }
}
