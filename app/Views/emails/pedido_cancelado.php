<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Cancelado</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">
                                Pedido Cancelado
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #1e293b; margin: 0 0 20px 0; font-size: 22px;">
                                Ola, <?= esc($nome) ?>
                            </h2>

                            <p style="color: #475569; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Infelizmente, seu pedido <strong>#<?= esc($pedido['order_number']) ?></strong> foi cancelado automaticamente porque nao identificamos o pagamento no prazo de 2 dias.
                            </p>

                            <!-- Info box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
                                <tr>
                                    <td style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; border-radius: 0 8px 8px 0;">
                                        <p style="color: #991b1b; font-size: 14px; margin: 0;">
                                            O pedido foi cancelado e os itens foram devolvidos ao estoque.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Resumo do pedido -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <tr>
                                    <td style="background-color: #f8fafc; padding: 15px; font-weight: bold; color: #1e293b;">
                                        Pedido Cancelado #<?= esc($pedido['order_number']) ?>
                                    </td>
                                </tr>
                                <?php foreach ($itens as $item): ?>
                                <tr>
                                    <td style="padding: 15px; border-top: 1px solid #e2e8f0;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #64748b; font-size: 14px; text-decoration: line-through;">
                                                    <?= esc($item['name'] ?? 'Produto') ?> (Qtd: <?= $item['quantity'] ?? 1 ?>)
                                                </td>
                                                <td align="right" style="color: #64748b; text-decoration: line-through;">
                                                    R$ <?= number_format($item['subtotal'] ?? ($item['price'] * $item['quantity']), 2, ',', '.') ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td style="padding: 15px; border-top: 2px solid #e2e8f0; background-color: #f8fafc;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #64748b; font-weight: bold; font-size: 16px;">
                                                    Total:
                                                </td>
                                                <td align="right" style="color: #64748b; font-weight: bold; font-size: 18px; text-decoration: line-through;">
                                                    R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #475569; font-size: 16px; line-height: 1.6; margin: 20px 0;">
                                Se voce ainda deseja adquirir esses produtos, basta fazer um novo pedido em nossa loja. Ficaremos felizes em atende-lo!
                            </p>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?= base_url() ?>" style="display: inline-block; background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 8px; font-weight: bold; font-size: 16px;">
                                            Visitar a Loja
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 20px 0 0 0;">
                                Se voce acredita que isso foi um erro ou ja efetuou o pagamento, entre em contato conosco.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #64748b; font-size: 12px; margin: 0;">
                                <?= setting('store_name') ?? 'GPS Imports' ?><br>
                                <?= setting('store_phone') ?? '' ?><br>
                                <?= setting('store_email') ?? '' ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
