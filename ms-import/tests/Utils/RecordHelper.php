<?php

namespace Tests\Utils;

class RecordHelper
{
    public static function getRecordsCreate(): array
    {
        return [
            [
                "fileId" => "6742b08a6969406abb0b9615",
                "name" => "Elizabeth Garcia",
                "governmentId" => "3500",
                "email" => "egarcia@example.net",
                "debtAmount" => "1045",
                "debtDueDate" => "2023-04-18",
                "debtID" => "000066c2-6127-4e17-94d9-503929e36101",
                "status" => "processing",
                "created_at" => "2024-11-24T04:50:18.000000Z",
                "updated_at" => "2024-11-24T04:50:18.000000Z",
            ],
            [
                "fileId" => "6742b08a6969406abb0b9615",
                "name" => "Carrie Meyers",
                "governmentId" => "5826",
                "email" => "lauren90@example.org",
                "debtAmount" => "8370",
                "debtDueDate" => "2023-07-23",
                "debtID" => "0001bca7-9319-4d86-bd88-93f6b8200858",
                "status" => "processing",
                "created_at" => "2024-11-24T04:50:19.000000Z",
                "updated_at" => "2024-11-24T04:50:19.000000Z",
            ]
        ];
    }

    public static function getRecordsUpdate(): array
    {
        return [
            [
                "fileId" => "6742b08a6969406abb0b9615",
                "name" => "Elizabeth Garcia",
                "governmentId" => "3500",
                "email" => "egarcia@example.net",
                "debtAmount" => "1045",
                "debtDueDate" => "2023-04-18",
                "debtID" => "000066c2-6127-4e17-94d9-503929e36101",
                "status" => "processing",
                "created_at" => "2024-11-24T04:50:18.000000Z",
                "updated_at" => "2024-11-24T04:50:18.000000Z",
                "id" => "6742b08c0201f201f8041cfb"
            ],
            [
                "fileId" => "6742b08a6969406abb0b9615",
                "name" => "Carrie Meyers",
                "governmentId" => "5826",
                "email" => "lauren90@example.org",
                "debtAmount" => "8370",
                "debtDueDate" => "2023-07-23",
                "debtID" => "0001bca7-9319-4d86-bd88-93f6b8200858",
                "status" => "processing",
                "created_at" => "2024-11-24T04:50:19.000000Z",
                "updated_at" => "2024-11-24T04:50:19.000000Z",
                "id" => "6742b0a50201f201f80481f5"
            ]
        ];
    }

    public static function getRecordsResult(): array
    {
        return [
            "000066c2-6127-4e17-94d9-503929e36101" => [
                "fileId" => "6742b08a6969406abb0b9615",
                "name" => "Elizabeth Garcia",
                "governmentId" => "3500",
                "email" => "egarcia@example.net",
                "debtAmount" => "1045",
                "debtDueDate" => "2023-04-18",
                "debtID" => "000066c2-6127-4e17-94d9-503929e36101",
                "status" => "processing",
                "created_at" => "2024-11-24T04:50:18.000000Z",
                "updated_at" => "2024-11-24T04:50:18.000000Z",
                "id" => "6742b08c0201f201f8041cfb"
            ],
            "0001bca7-9319-4d86-bd88-93f6b8200858" => [
                "fileId" => "6742b08a6969406abb0b9615",
                "name" => "Carrie Meyers",
                "governmentId" => "5826",
                "email" => "lauren90@example.org",
                "debtAmount" => "8370",
                "debtDueDate" => "2023-07-23",
                "debtID" => "0001bca7-9319-4d86-bd88-93f6b8200858",
                "status" => "processing",
                "created_at" => "2024-11-24T04:50:19.000000Z",
                "updated_at" => "2024-11-24T04:50:19.000000Z",
                "id" => "6742b0a50201f201f80481f5"
            ]
        ];
    }

    public static function getDebtID(): array
    {
        return [
            "000066c2-6127-4e17-94d9-503929e36101",
            "0001bca7-9319-4d86-bd88-93f6b8200858",
        ];
    }

    public static function getNewRecord(): array
    {
        return [
            "fileId" => "6742c25c6969406abb0b961b",
            "name" => "Joe Coleman",
            "governmentId" => "6142",
            "email" => "josephhudson@example.net",
            "debtAmount" => "8410",
            "debtDueDate" => "2023-10-28",
            "debtID" => "a1789115-aa45-498a-ba24-730e8d04a61f",
            "status" => "initialized"
        ];
    }
}
