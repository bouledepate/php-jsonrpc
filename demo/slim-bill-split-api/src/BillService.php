<?php

declare(strict_types=1);

namespace Demo\BillApi;

use Bouledepate\JsonRpc\Exceptions\Core\InvalidParamsException;

final readonly class BillService
{
    public function __construct(private BillStore $store)
    {
    }

    /** @param array<string, mixed> $params */
    public function createBill(array $params): array
    {
        $creatorName = $this->asTrimmedString($params, 'creatorName');
        $title = $this->asOptionalTrimmedString($params, 'title') ?? 'Restaurant bill';
        $currency = strtoupper($this->asOptionalTrimmedString($params, 'currency') ?? 'RUB');

        $billId = $this->id('bill');
        $participantId = $this->id('person');
        $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

        $this->store->mutate(function (array $state) use ($billId, $participantId, $creatorName, $title, $currency, $code): array {
            $state['bills'][$billId] = [
                'id' => $billId,
                'code' => $code,
                'title' => $title,
                'currency' => $currency,
                'servicePercent' => 0.0,
                'participants' => [
                    $participantId => ['id' => $participantId, 'name' => $creatorName],
                ],
                'orders' => [],
            ];

            return $state;
        });

        return [
            'billId' => $billId,
            'code' => $code,
            'ownerParticipantId' => $participantId,
            'title' => $title,
            'currency' => $currency,
        ];
    }

    /** @param array<string, mixed> $params */
    public function configureBill(array $params): array
    {
        $billId = $this->asTrimmedString($params, 'billId');
        $title = $this->asOptionalTrimmedString($params, 'title');
        $currency = $this->asOptionalTrimmedString($params, 'currency');

        $state = $this->store->mutate(function (array $state) use ($billId, $title, $currency): array {
            $bill = $this->findBillById($state, $billId);

            if ($title !== null) {
                $bill['title'] = $title;
            }

            if ($currency !== null) {
                $bill['currency'] = strtoupper($currency);
            }

            $state['bills'][$billId] = $bill;
            return $state;
        });

        return $this->publicBillMeta($this->findBillById($state, $billId));
    }

    /** @param array<string, mixed> $params */
    public function addServiceCharge(array $params): array
    {
        $billId = $this->asTrimmedString($params, 'billId');
        $percent = $this->asNumeric($params, 'percent');

        if ($percent < 0 || $percent > 100) {
            throw new InvalidParamsException(['percent' => ['Service percent must be in range 0..100.']]);
        }

        $state = $this->store->mutate(function (array $state) use ($billId, $percent): array {
            $bill = $this->findBillById($state, $billId);
            $bill['servicePercent'] = round($percent, 2);
            $state['bills'][$billId] = $bill;
            return $state;
        });

        return [
            'billId' => $billId,
            'servicePercent' => $this->findBillById($state, $billId)['servicePercent'],
        ];
    }

    /** @param array<string, mixed> $params */
    public function joinByCode(array $params): array
    {
        $code = strtoupper($this->asTrimmedString($params, 'code'));
        $name = $this->asTrimmedString($params, 'name');
        $participantId = $this->id('person');

        $state = $this->store->mutate(function (array $state) use ($code, $name, $participantId): array {
            $billId = $this->findBillIdByCode($state, $code);
            $bill = $this->findBillById($state, $billId);

            $bill['participants'][$participantId] = ['id' => $participantId, 'name' => $name];
            $state['bills'][$billId] = $bill;

            return $state;
        });

        $billId = $this->findBillIdByCode($state, $code);
        $bill = $this->findBillById($state, $billId);

        return [
            'billId' => $billId,
            'participantId' => $participantId,
            'participantsCount' => count($bill['participants']),
        ];
    }

    /** @param array<string, mixed> $params */
    public function addOrder(array $params): array
    {
        $billId = $this->asTrimmedString($params, 'billId');
        $participantId = $this->asTrimmedString($params, 'participantId');
        $item = $this->asTrimmedString($params, 'item');
        $amount = $this->asNumeric($params, 'amount');
        $isShared = $this->asBool($params, 'isShared');

        if ($amount <= 0) {
            throw new InvalidParamsException(['amount' => ['Amount must be greater than 0.']]);
        }

        $orderId = $this->id('order');

        $state = $this->store->mutate(function (array $state) use (
            $billId,
            $participantId,
            $item,
            $amount,
            $isShared,
            $orderId
        ): array {
            $bill = $this->findBillById($state, $billId);

            if (!array_key_exists($participantId, $bill['participants'])) {
                throw new InvalidParamsException(['participantId' => ['Participant is not part of this bill.']]);
            }

            $bill['orders'][] = [
                'id' => $orderId,
                'item' => $item,
                'amount' => round($amount, 2),
                'isShared' => $isShared,
                'participantId' => $participantId,
            ];

            $state['bills'][$billId] = $bill;
            return $state;
        });

        return [
            'billId' => $billId,
            'orderId' => $orderId,
            'ordersCount' => count($this->findBillById($state, $billId)['orders']),
        ];
    }

    /** @param array<string, mixed> $params */
    public function getSummary(array $params): array
    {
        $billId = $this->asTrimmedString($params, 'billId');
        $bill = $this->findBillById($this->store->load(), $billId);

        $participants = $bill['participants'];
        $participantCount = max(count($participants), 1);

        $sharedSubtotal = 0.0;
        $personalSubtotals = [];

        foreach ($participants as $participant) {
            $personalSubtotals[$participant['id']] = 0.0;
        }

        foreach ($bill['orders'] as $order) {
            if ($order['isShared'] === true) {
                $sharedSubtotal += $order['amount'];
                continue;
            }

            $personalSubtotals[$order['participantId']] += $order['amount'];
        }

        $sharedPerPerson = $sharedSubtotal / $participantCount;
        $servicePercent = (float)$bill['servicePercent'];

        $byPerson = [];
        $totalWithoutService = 0.0;
        $totalService = 0.0;

        foreach ($participants as $participant) {
            $id = $participant['id'];
            $subtotal = $personalSubtotals[$id] + $sharedPerPerson;
            $serviceAmount = $subtotal * ($servicePercent / 100);
            $total = $subtotal + $serviceAmount;

            $totalWithoutService += $subtotal;
            $totalService += $serviceAmount;

            $byPerson[] = [
                'participantId' => $id,
                'name' => $participant['name'],
                'personalSubtotal' => round($personalSubtotals[$id], 2),
                'sharedPart' => round($sharedPerPerson, 2),
                'subtotal' => round($subtotal, 2),
                'serviceAmount' => round($serviceAmount, 2),
                'totalToPay' => round($total, 2),
            ];
        }

        return [
            'billId' => $bill['id'],
            'title' => $bill['title'],
            'currency' => $bill['currency'],
            'servicePercent' => round($servicePercent, 2),
            'ordersCount' => count($bill['orders']),
            'participantsCount' => count($participants),
            'sharedSubtotal' => round($sharedSubtotal, 2),
            'totalWithoutService' => round($totalWithoutService, 2),
            'serviceChargeTotal' => round($totalService, 2),
            'grandTotal' => round($totalWithoutService + $totalService, 2),
            'byPerson' => $byPerson,
        ];
    }

    /** @param array<string, mixed> $state */
    private function findBillIdByCode(array $state, string $code): string
    {
        foreach ($state['bills'] as $billId => $bill) {
            if (($bill['code'] ?? null) === $code) {
                return (string)$billId;
            }
        }

        throw new InvalidParamsException(['code' => ['Bill with this code was not found.']]);
    }

    /** @param array<string, mixed> $state */
    private function findBillById(array $state, string $billId): array
    {
        $bill = $state['bills'][$billId] ?? null;
        if (!is_array($bill)) {
            throw new InvalidParamsException(['billId' => ['Bill was not found.']]);
        }

        return $bill;
    }

    /** @param array<string, mixed> $bill */
    private function publicBillMeta(array $bill): array
    {
        return [
            'billId' => $bill['id'],
            'code' => $bill['code'],
            'title' => $bill['title'],
            'currency' => $bill['currency'],
            'servicePercent' => $bill['servicePercent'],
            'participantsCount' => count($bill['participants']),
            'ordersCount' => count($bill['orders']),
        ];
    }

    /** @param array<string, mixed> $params */
    private function asTrimmedString(array $params, string $key): string
    {
        $value = $params[$key] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new InvalidParamsException([$key => [sprintf('`%s` must be a non-empty string.', $key)]]);
        }

        return trim($value);
    }

    /** @param array<string, mixed> $params */
    private function asOptionalTrimmedString(array $params, string $key): ?string
    {
        if (!array_key_exists($key, $params) || $params[$key] === null) {
            return null;
        }

        return $this->asTrimmedString($params, $key);
    }

    /** @param array<string, mixed> $params */
    private function asNumeric(array $params, string $key): float
    {
        $value = $params[$key] ?? null;
        if (!is_numeric($value)) {
            throw new InvalidParamsException([$key => [sprintf('`%s` must be numeric.', $key)]]);
        }

        return (float)$value;
    }

    /** @param array<string, mixed> $params */
    private function asBool(array $params, string $key): bool
    {
        $value = $params[$key] ?? null;
        if (!is_bool($value)) {
            throw new InvalidParamsException([$key => [sprintf('`%s` must be boolean.', $key)]]);
        }

        return $value;
    }

    private function id(string $prefix): string
    {
        return $prefix . '_' . bin2hex(random_bytes(6));
    }
}
