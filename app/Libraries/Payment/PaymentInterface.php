<?php

namespace App\Libraries\Payment;

interface PaymentInterface
{
    /**
     * Create PIX payment
     */
    public function createPixPayment(array $order): array;

    /**
     * Create credit/debit card payment
     */
    public function createCardPayment(array $order, array $cardData, string $type = 'credit'): array;

    /**
     * Create boleto payment
     */
    public function createBoletoPayment(array $order): array;

    /**
     * Parse webhook notification
     */
    public function parseWebhook(array $data): ?array;

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $paymentId): array;

    /**
     * Refund payment
     */
    public function refund(string $paymentId, float $amount): array;
}
