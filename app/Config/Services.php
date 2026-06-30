<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use App\Services\ProductService;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\ShippingService;
use App\Services\StockService;
use App\Services\CustomerService;

class Services extends BaseService
{
    /**
     * Product Service
     */
    public static function product(bool $getShared = true): ProductService
    {
        if ($getShared) {
            return static::getSharedInstance('product');
        }

        return new ProductService();
    }

    /**
     * Cart Service
     */
    public static function cart(bool $getShared = true): CartService
    {
        if ($getShared) {
            return static::getSharedInstance('cart');
        }

        return new CartService();
    }

    /**
     * Order Service
     */
    public static function order(bool $getShared = true): OrderService
    {
        if ($getShared) {
            return static::getSharedInstance('order');
        }

        return new OrderService();
    }

    /**
     * Payment Service
     */
    public static function payment(bool $getShared = true): PaymentService
    {
        if ($getShared) {
            return static::getSharedInstance('payment');
        }

        return new PaymentService();
    }

    /**
     * Shipping Service
     */
    public static function shipping(bool $getShared = true): ShippingService
    {
        if ($getShared) {
            return static::getSharedInstance('shipping');
        }

        return new ShippingService();
    }

    /**
     * Stock Service
     */
    public static function stock(bool $getShared = true): StockService
    {
        if ($getShared) {
            return static::getSharedInstance('stock');
        }

        return new StockService();
    }

    /**
     * Customer Service
     */
    public static function customer(bool $getShared = true): CustomerService
    {
        if ($getShared) {
            return static::getSharedInstance('customer');
        }

        return new CustomerService();
    }
}
