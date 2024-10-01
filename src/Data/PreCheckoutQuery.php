<?php

namespace Tgb\Data;

class PreCheckoutQuery
{
    private $preCheckoutQuery;
    private $id;
    private $from;
    private $fromId;
    private $fromIsBot;
    private $fromFirstName;
    private $fromUsername;
    private $fromLanguageCode;
    private $fromIsPremium;
    private $currency;
    private $totalAmount;
    private $invoicePayload;
    private $shippingOptionId;
    private $orderInfo;

    public function __construct(array $data)
    {
        $this->preCheckoutQuery = $data['pre_checkout_query'] ?? null;
        $this->id = $data['pre_checkout_query']['id'] ?? null;
        $this->from = $data['pre_checkout_query']['from'] ?? null;
        $this->fromId = $data['pre_checkout_query']['from']['id'] ?? null;
        $this->fromIsBot = $data['pre_checkout_query']['from']['is_bot'] ?? null;
        $this->fromFirstName = $data['pre_checkout_query']['from']['first_name'] ?? null;
        $this->fromUsername = $data['pre_checkout_query']['from']['username'] ?? null;
        $this->fromLanguageCode = $data['pre_checkout_query']['from']['language_code'] ?? null;
        $this->fromIsPremium = $data['pre_checkout_query']['from']['is_premium'] ?? null;
        $this->currency = $data['pre_checkout_query']['currency'] ?? null;
        $this->totalAmount = $data['pre_checkout_query']['total_amount'] ?? null;
        $this->invoicePayload = $data['pre_checkout_query']['invoice_payload'] ?? null;
        $this->shippingOptionId = $data['pre_checkout_query']['shipping_option_id'] ?? null;
        $this->orderInfo = $data['pre_checkout_query']['order_info'] ?? null;
    }

    public function getPreCheckoutQuery()
    {
        return $this->preCheckoutQuery;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getFromId()
    {
        return $this->fromId;
    }

    public function getFromIsBot()
    {
        return $this->fromIsBot;
    }

    public function getFromFirstName()
    {
        return $this->fromFirstName;
    }

    public function getFromUsername()
    {
        return $this->fromUsername;
    }

    public function getFromLanguageCode()
    {
        return $this->fromLanguageCode;
    }

    public function getFromIsPremium()
    {
        return $this->fromIsPremium;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    public function getInvoicePayload()
    {
        return $this->invoicePayload;
    }

    public function getShippingOptionId()
    {
        return $this->shippingOptionId;
    }

    public function getOrderInfo()
    {
        return $this->orderInfo;
    }
}
