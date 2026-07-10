<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class CartController extends BaseController
{
    protected $cartService;

    public function __construct()
    {
        $this->cartService = service('cart');
    }

    /**
     * Retorna JSON limpo sem debug output
     */
    protected function jsonResponse(array $data)
    {
        // Limpar qualquer output anterior
        while (ob_get_level()) {
            ob_end_clean();
        }

        return $this->response
            ->setContentType('application/json')
            ->setBody(json_encode($data));
    }

    public function index()
    {
        $cart = $this->cartService->getCurrentCart();

        return view('front/cart/index', [
            'title' => 'Carrinho',
            'cart' => $cart,
        ]);
    }

    /**
     * Mini cart data for offcanvas sidebar
     */
    public function mini()
    {
        try {
            $cart = $this->cartService->getCurrentCart();

            $items = [];
            foreach ($cart['items'] as $item) {
                // Tratar URL da imagem (externa ou local)
                $image = $item['image'] ?? '';
                if (empty($image)) {
                    $imageUrl = 'https://placehold.co/60x60/e9ecef/495057?text=P';
                } elseif (strpos($image, 'http') === 0) {
                    $imageUrl = $image;
                } else {
                    $imageUrl = base_url('uploads/products/thumbs/' . $image);
                }

                $items[] = [
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'] ?? null,
                    'name' => $item['name'],
                    'image' => $imageUrl,
                    'price' => $item['price'],
                    'price_formatted' => number_format($item['price'], 2, ',', '.'),
                    'quantity' => $item['quantity'],
                    'attributes' => $item['attributes'] ?? '',
                ];
            }

            return $this->jsonResponse([
                'success' => true,
                'items' => $items,
                'items_count' => $cart['items_count'],
                'subtotal' => $cart['subtotal'],
                'subtotal_formatted' => number_format($cart['subtotal'], 2, ',', '.'),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'CartController::mini - ' . $e->getMessage());
            return $this->jsonResponse([
                'success' => false,
                'items' => [],
                'items_count' => 0,
                'subtotal' => 0,
                'subtotal_formatted' => '0,00',
            ]);
        }
    }

    public function add()
    {
        try {
            $productId = (int) $this->request->getPost('product_id');
            $quantity = (int) ($this->request->getPost('quantity') ?? 1);
            $variationId = $this->request->getPost('variation_id') ? (int) $this->request->getPost('variation_id') : null;

            $result = $this->cartService->addItem($productId, $quantity, $variationId);

            // Salvar carrinho abandonado para recuperacao
            if ($result['success']) {
                $this->salvarCarrinhoAbandonado();
            }

            // Sempre retorna JSON (endpoint usado via AJAX)
            return $this->jsonResponse($result);
        } catch (\Exception $e) {
            log_message('error', 'CartController::add - ' . $e->getMessage());

            return $this->jsonResponse([
                'success' => false,
                'message' => 'Erro ao adicionar produto ao carrinho.'
            ]);
        }
    }

    public function update()
    {
        $itemId = (int) $this->request->getPost('item_id');
        $quantity = (int) $this->request->getPost('quantity');

        $result = $this->cartService->updateQuantity($itemId, $quantity);

        return $this->jsonResponse($result);
    }

    public function remove()
    {
        $itemId = $this->request->getPost('item_id');
        $productId = $this->request->getPost('product_id');
        $variationId = $this->request->getPost('variation_id');

        // Support removal by item_id or by product_id+variation_id
        if ($itemId) {
            $result = $this->cartService->removeItem((int) $itemId);
        } elseif ($productId) {
            $result = $this->cartService->removeByProduct(
                (int) $productId,
                $variationId ? (int) $variationId : null
            );
        } else {
            $result = ['success' => false, 'message' => 'Item nao encontrado.'];
        }

        return $this->jsonResponse($result);
    }

    public function applyCoupon()
    {
        $code = $this->request->getPost('coupon_code');

        if (empty($code)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Digite um cupom.']);
        }

        $result = $this->cartService->applyCoupon($code);

        return $this->jsonResponse($result);
    }

    public function removeCoupon()
    {
        $result = $this->cartService->removeCoupon();

        return $this->jsonResponse($result);
    }

    public function calculateShipping()
    {
        $zipcode = $this->request->getPost('zipcode');

        if (empty($zipcode)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Digite seu CEP.']);
        }

        $result = $this->cartService->calculateShipping($zipcode);

        return $this->jsonResponse($result);
    }

    public function selectShipping()
    {
        $method = $this->request->getPost('shipping_method');
        $price = (float) $this->request->getPost('shipping_price');

        if (empty($method)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Selecione um metodo de envio.']);
        }

        $cart = $this->cartService->getCurrentCart();
        $zipcode = $cart['shipping_zipcode'] ?? '';

        $result = $this->cartService->setShipping($method, $price, $zipcode);

        return $this->jsonResponse($result);
    }

    /**
     * Salvar carrinho abandonado para recuperacao por email
     */
    protected function salvarCarrinhoAbandonado()
    {
        try {
            $cart = $this->cartService->getCurrentCart();

            // So salvar se tiver itens
            if (empty($cart['items'])) {
                return;
            }

            $customerId = session()->get('customer_id');
            $sessionId = session_id();

            // Se cliente nao esta logado, nao temos email para enviar
            if (!$customerId) {
                return;
            }

            $db = \Config\Database::connect();

            // Verificar se ja existe registro para este cliente
            $existing = $db->table('cart_abandonment')
                ->where('customer_id', $customerId)
                ->where('recovered', 0)
                ->get()
                ->getRowArray();

            $data = [
                'customer_id' => $customerId,
                'session_id' => $sessionId,
                'items' => json_encode($cart['items']),
                'total' => $cart['subtotal'],
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                // Atualizar
                $db->table('cart_abandonment')
                    ->where('id', $existing['id'])
                    ->update($data);
            } else {
                // Inserir novo
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['email_sent'] = 0;
                $data['recovered'] = 0;
                $db->table('cart_abandonment')->insert($data);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao salvar carrinho abandonado: ' . $e->getMessage());
        }
    }

    /**
     * Marcar carrinho como recuperado (chamado apos finalizar compra)
     */
    public static function marcarCarrinhoRecuperado(int $customerId)
    {
        try {
            $db = \Config\Database::connect();
            $db->table('cart_abandonment')
                ->where('customer_id', $customerId)
                ->where('recovered', 0)
                ->update(['recovered' => 1]);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao marcar carrinho recuperado: ' . $e->getMessage());
        }
    }
}
