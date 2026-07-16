<?php

namespace App\Services;

use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\ProductVariationModel;
use App\Models\CouponModel;

class CartService
{
    protected CartModel $cartModel;
    protected ProductModel $productModel;
    protected ProductVariationModel $variationModel;
    protected CouponModel $couponModel;

    public function __construct()
    {
        $this->cartModel = model('CartModel');
        $this->productModel = model('ProductModel');
        $this->variationModel = model('ProductVariationModel');
        $this->couponModel = model('CouponModel');
    }

    /**
     * Get current cart
     */
    public function getCurrentCart(): array
    {
        $session = session();
        $customerId = $session->get('customer_id');
        $cartId = $session->get('cart_id');

        // Se tem cart_id na sessao, usar ele
        if ($cartId) {
            $cart = $this->cartModel->find($cartId);
            if ($cart) {
                // Atualizar customer_id se logou
                if ($customerId && empty($cart['customer_id'])) {
                    $this->cartModel->update($cartId, ['customer_id' => $customerId]);
                }
                return $this->cartModel->getWithItems($cartId);
            }
        }

        // Se tem customer logado, buscar carrinho dele
        if ($customerId) {
            $cart = $this->cartModel->where('customer_id', $customerId)->first();
            if ($cart) {
                $session->set('cart_id', $cart['id']);
                return $this->cartModel->getWithItems($cart['id']);
            }
        }

        // Criar novo carrinho
        $newCartId = $this->cartModel->insert([
            'customer_id' => $customerId,
            'session_id' => $session->session_id,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
        ]);

        $session->set('cart_id', $newCartId);

        return $this->cartModel->getWithItems($newCartId);
    }

    /**
     * Add item to cart
     */
    public function addItem(int $productId, int $quantity = 1, ?int $variationId = null, array $attributes = []): array
    {
        // Validate product
        $product = $this->productModel->find($productId);

        if (!$product || $product['status'] !== 'active') {
            return ['success' => false, 'message' => 'Produto nao encontrado.'];
        }

        // Get price and check stock
        $price = $this->productModel->getCurrentPrice($product);
        $name = $product['name'];
        $sku = $product['sku'];
        $image = $product['featured_image'];

        if ($variationId) {
            $variation = $this->variationModel->find($variationId);

            if (!$variation || $variation['product_id'] != $productId) {
                return ['success' => false, 'message' => 'Variacao nao encontrada.'];
            }

            $price = $this->variationModel->getCurrentPrice($variation) ?: $price;
            $sku = $variation['sku'] ?: $sku;
            $image = $variation['image'] ?: $image;
            $name .= ' - ' . $variation['name'];

            // Check stock
            if ($variation['stock'] < $quantity) {
                return ['success' => false, 'message' => 'Quantidade indisponivel em estoque.'];
            }
        } else {
            // Check stock
            if (!$this->productModel->isInStock($productId, null, $quantity)) {
                return ['success' => false, 'message' => 'Quantidade indisponivel em estoque.'];
            }
        }

        // Get cart
        $cart = $this->getCurrentCart();

        // Add item
        $this->cartModel->addItem($cart['id'], [
            'product_id' => $productId,
            'variation_id' => $variationId,
            'name' => $name,
            'sku' => $sku,
            'image' => $image,
            'price' => $price,
            'quantity' => $quantity,
            'attributes' => $attributes,
        ]);

        $updatedCart = $this->getCurrentCart();

        return [
            'success' => true,
            'message' => 'Produto adicionado ao carrinho!',
            'cart' => $updatedCart,
            'cart_count' => $updatedCart['items_count'] ?? 0,
        ];
    }

    /**
     * Update item quantity
     */
    public function updateQuantity(int $itemId, int $quantity): array
    {
        $cart = $this->getCurrentCart();

        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }

        // Get item to check stock
        $db = \Config\Database::connect();
        $item = $db->table('cart_items')
                   ->where('id', $itemId)
                   ->where('cart_id', $cart['id'])
                   ->get()
                   ->getRowArray();

        if (!$item) {
            return ['success' => false, 'message' => 'Item nao encontrado.'];
        }

        // Check stock
        if ($item['variation_id']) {
            $variation = $this->variationModel->find($item['variation_id']);
            if ($variation && $variation['stock'] < $quantity) {
                return ['success' => false, 'message' => 'Quantidade indisponivel. Maximo: ' . $variation['stock']];
            }
        } else {
            if (!$this->productModel->isInStock($item['product_id'], null, $quantity)) {
                $product = $this->productModel->find($item['product_id']);
                return ['success' => false, 'message' => 'Quantidade indisponivel. Maximo: ' . ($product['stock'] ?? 0)];
            }
        }

        $this->cartModel->updateItemQuantity($cart['id'], $itemId, $quantity);

        $updatedCart = $this->getCurrentCart();

        return [
            'success' => true,
            'message' => 'Quantidade atualizada.',
            'cart' => $updatedCart,
            'cart_count' => $updatedCart['items_count'] ?? 0,
            'subtotal' => $updatedCart['subtotal'] ?? 0,
            'total' => $updatedCart['total'] ?? 0,
        ];
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $itemId): array
    {
        $cart = $this->getCurrentCart();

        $this->cartModel->removeItem($cart['id'], $itemId);

        $updatedCart = $this->getCurrentCart();

        return [
            'success' => true,
            'message' => 'Item removido do carrinho.',
            'cart' => $updatedCart,
            'cart_count' => $updatedCart['items_count'] ?? 0,
            'subtotal' => $updatedCart['subtotal'] ?? 0,
            'total' => $updatedCart['total'] ?? 0,
        ];
    }

    /**
     * Remove item from cart by product_id and variation_id
     */
    public function removeByProduct(int $productId, ?int $variationId = null): array
    {
        $cart = $this->getCurrentCart();

        $db = \Config\Database::connect();
        $builder = $db->table('cart_items')
                      ->where('cart_id', $cart['id'])
                      ->where('product_id', $productId);

        if ($variationId) {
            $builder->where('variation_id', $variationId);
        } else {
            $builder->where('variation_id IS NULL');
        }

        $builder->delete();

        // Recalculate cart totals
        $this->cartModel->recalculate($cart['id']);

        $updatedCart = $this->getCurrentCart();

        return [
            'success' => true,
            'message' => 'Item removido do carrinho.',
            'cart' => $updatedCart,
            'cart_count' => $updatedCart['items_count'] ?? 0,
            'subtotal' => $updatedCart['subtotal'] ?? 0,
            'total' => $updatedCart['total'] ?? 0,
        ];
    }

    /**
     * Apply coupon
     */
    public function applyCoupon(string $code): array
    {
        $cart = $this->getCurrentCart();
        $customerId = session()->get('customer_id');

        // Validate coupon
        $validation = $this->couponModel->validateCoupon(
            $code,
            $cart['subtotal'],
            $customerId,
            $cart['items'] ?? []
        );

        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }

        // Log para debug
        log_message('debug', 'Cupom validado: ' . json_encode([
            'code' => $code,
            'subtotal' => $cart['subtotal'],
            'discount' => $validation['discount'],
            'coupon_id' => $validation['coupon']['id'],
            'applies_to' => $validation['coupon']['applies_to'] ?? 'null',
        ]));

        // Apply coupon
        $this->cartModel->applyCoupon($cart['id'], $validation['coupon']['id'], $validation['discount']);

        $updatedCart = $this->getCurrentCart();

        return [
            'success' => true,
            'message' => $validation['message'],
            'discount' => $validation['discount'],
            'cart' => $updatedCart,
            'total' => $updatedCart['total'] ?? 0,
        ];
    }

    /**
     * Remove coupon
     */
    public function removeCoupon(): array
    {
        $cart = $this->getCurrentCart();

        $this->cartModel->removeCoupon($cart['id']);

        $updatedCart = $this->getCurrentCart();

        return [
            'success' => true,
            'message' => 'Cupom removido.',
            'cart' => $updatedCart,
            'total' => $updatedCart['total'] ?? 0,
        ];
    }

    /**
     * Calculate shipping
     */
    public function calculateShipping(string $zipcode): array
    {
        $cart = $this->getCurrentCart();

        if (empty($cart['items'])) {
            return ['success' => false, 'message' => 'Carrinho vazio.'];
        }

        // Clean zipcode
        $zipcode = preg_replace('/[^0-9]/', '', $zipcode);

        if (strlen($zipcode) !== 8) {
            return ['success' => false, 'message' => 'CEP invalido.'];
        }

        // Build items with dimensions for shipping calculation
        $products = [];

        foreach ($cart['items'] as $item) {
            $product = $this->productModel->find($item['product_id']);

            if ($product) {
                $products[] = [
                    'id' => $item['product_id'],
                    'weight' => (float) ($product['weight'] ?? 0.3),
                    'width' => (int) ($product['width'] ?? 11),
                    'height' => (int) ($product['height'] ?? 2),
                    'length' => (int) ($product['length'] ?? 16),
                    'price' => (float) $item['price'],
                    'quantity' => (int) $item['quantity'],
                ];
            }
        }

        // Get shipping options using Melhor Envio
        $melhorEnvio = new \App\Services\MelhorEnvioService();
        $options = $melhorEnvio->calculate($zipcode, $products);

        if (empty($options)) {
            return ['success' => false, 'message' => 'Nao foi possivel calcular o frete para este CEP.'];
        }

        // Save zipcode for later use
        $this->cartModel->update($cart['id'], ['shipping_zipcode' => $zipcode]);

        return [
            'success' => true,
            'options' => $options,
            'zipcode' => $zipcode,
        ];
    }

    /**
     * Set shipping method
     */
    public function setShipping(string $method, float $cost, string $zipcode): array
    {
        $cart = $this->getCurrentCart();

        $this->cartModel->setShipping($cart['id'], $method, $cost, $zipcode);

        $updatedCart = $this->getCurrentCart();

        return [
            'success' => true,
            'message' => 'Frete selecionado.',
            'cart' => $updatedCart,
            'total' => $updatedCart['total'] ?? 0,
        ];
    }

    /**
     * Clear cart
     */
    public function clearCart(): bool
    {
        $cart = $this->getCurrentCart();
        $result = $this->cartModel->clearCart($cart['id']);

        // Remover cart_id da sessao para criar novo carrinho
        session()->remove('cart_id');

        return $result;
    }

    /**
     * Get cart count
     */
    public function getCartCount(): int
    {
        $cart = $this->getCurrentCart();
        return $cart['items_count'] ?? 0;
    }

    /**
     * Merge carts on login
     */
    public function mergeOnLogin(int $customerId): void
    {
        $session = session();
        $cartId = $session->get('cart_id');

        if ($cartId) {
            // Atualizar carrinho atual para o cliente
            $this->cartModel->update($cartId, ['customer_id' => $customerId]);
        } else {
            // Buscar carrinho do cliente
            $cart = $this->cartModel->where('customer_id', $customerId)->first();
            if ($cart) {
                $session->set('cart_id', $cart['id']);
            }
        }
    }

    /**
     * Validate cart before checkout
     */
    public function validateForCheckout(): array
    {
        $cart = $this->getCurrentCart();
        $errors = [];

        if (empty($cart['items'])) {
            $errors[] = 'Seu carrinho esta vazio.';
            return ['valid' => false, 'errors' => $errors];
        }

        // Validate each item
        foreach ($cart['items'] as $item) {
            $product = $this->productModel->find($item['product_id']);

            if (!$product || $product['status'] !== 'active') {
                $errors[] = "O produto '{$item['name']}' nao esta mais disponivel.";
                continue;
            }

            // Check stock
            if ($item['variation_id']) {
                $variation = $this->variationModel->find($item['variation_id']);
                if (!$variation || $variation['stock'] < $item['quantity']) {
                    $errors[] = "Estoque insuficiente para '{$item['name']}'.";
                }
            } else {
                if (!$this->productModel->isInStock($item['product_id'], null, $item['quantity'])) {
                    $errors[] = "Estoque insuficiente para '{$item['name']}'.";
                }
            }

            // Check price changes
            $currentPrice = $this->productModel->getCurrentPrice($product);
            if (abs($currentPrice - $item['price']) > 0.01) {
                $errors[] = "O preco de '{$item['name']}' foi alterado. Atualize seu carrinho.";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'cart' => $cart,
        ];
    }
}
