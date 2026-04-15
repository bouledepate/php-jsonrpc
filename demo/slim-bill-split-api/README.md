# Slim v4 JSON-RPC demo: restaurant bill split API

Демо-приложение показывает, как подключить middleware из этого репозитория в Slim v4 и реализовать API расчёта ресторанного счёта.

## Что реализовано

Методы JSON-RPC (`POST /rpc`):

1. `bill.create` — создать счёт.
2. `bill.configure` — настраивать счёт (название/валюта).
3. `bill.addServiceCharge` — добавить процент за обслуживание.
4. `bill.joinByCode` — присоединиться к счёту по коду.
5. `bill.addOrder` — записать заказ человека в счёт (`isShared`: `true/false`).
6. `bill.getSummary` — получить итог к оплате по каждому и общий итог.

Логика расчёта:
- личные позиции учитываются только за конкретным участником;
- общие позиции делятся поровну на всех участников;
- сервисный процент применяется ко всей сумме каждого участника (личная + доля общего).

## Запуск

```bash
cd demo/slim-bill-split-api
composer install
php -S 127.0.0.1:8080 -t public
```

## Пример запросов

### 1) Создать счёт

```bash
curl -s http://127.0.0.1:8080/rpc \
  -H 'Content-Type: application/json' \
  -d '{
    "jsonrpc":"2.0",
    "id":1,
    "method":"bill.create",
    "params":{"creatorName":"Alice","title":"Dinner","currency":"RUB"}
  }' | jq
```

Сохраните из ответа `billId`, `code`, `ownerParticipantId`.

### 2) Присоединиться по коду

```bash
curl -s http://127.0.0.1:8080/rpc \
  -H 'Content-Type: application/json' \
  -d '{
    "jsonrpc":"2.0",
    "id":2,
    "method":"bill.joinByCode",
    "params":{"code":"ABC123","name":"Bob"}
  }' | jq
```

### 3) Добавить общий заказ

```bash
curl -s http://127.0.0.1:8080/rpc \
  -H 'Content-Type: application/json' \
  -d '{
    "jsonrpc":"2.0",
    "id":3,
    "method":"bill.addOrder",
    "params":{
      "billId":"bill_xxx",
      "participantId":"person_xxx",
      "item":"Pizza",
      "amount":1800,
      "isShared":true
    }
  }' | jq
```

### 4) Добавить личный заказ

```bash
curl -s http://127.0.0.1:8080/rpc \
  -H 'Content-Type: application/json' \
  -d '{
    "jsonrpc":"2.0",
    "id":4,
    "method":"bill.addOrder",
    "params":{
      "billId":"bill_xxx",
      "participantId":"person_yyy",
      "item":"Steak",
      "amount":1400,
      "isShared":false
    }
  }' | jq
```

### 5) Установить сервисный процент

```bash
curl -s http://127.0.0.1:8080/rpc \
  -H 'Content-Type: application/json' \
  -d '{
    "jsonrpc":"2.0",
    "id":5,
    "method":"bill.addServiceCharge",
    "params":{"billId":"bill_xxx","percent":10}
  }' | jq
```

### 6) Получить итог

```bash
curl -s http://127.0.0.1:8080/rpc \
  -H 'Content-Type: application/json' \
  -d '{
    "jsonrpc":"2.0",
    "id":6,
    "method":"bill.getSummary",
    "params":{"billId":"bill_xxx"}
  }' | jq
```

Данные сохраняются в файл `var/bills.json`.
