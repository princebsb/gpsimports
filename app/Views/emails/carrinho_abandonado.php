<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voce esqueceu algo!</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">
                                <?= setting('store_name') ?? 'GPS Imports' ?>
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #1e293b; margin: 0 0 20px 0; font-size: 22px;">
                                Ola, <?= esc($nome) ?>! 👋
                            </h2>

                            <p style="color: #475569; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Percebemos que voce deixou alguns itens no seu carrinho. Eles ainda estao esperando por voce!
                            </p>

                            <!-- Itens do carrinho -->
                            <?php if (!empty($itens)): ?>
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <tr>
                                    <td style="background-color: #f8fafc; padding: 15px; font-weight: bold; color: #1e293b;">
                                        Seus itens:
                                    </td>
                                </tr>
                                <?php foreach ($itens as $item): ?>
                                <tr>
                                    <td style="padding: 15px; border-top: 1px solid #e2e8f0;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="color: #1e293b; font-size: 14px;">
                                                    <?= esc($item['name'] ?? 'Produto') ?>
                                                    <br>
                                                    <span style="color: #64748b; font-size: 12px;">
                                                        Qtd: <?= $item['quantity'] ?? 1 ?>
                                                    </span>
                                                </td>
                                                <td align="right" style="color: #2563eb; font-weight: bold;">
                                                    R$ <?= number_format($item['price'] ?? 0, 2, ',', '.') ?>
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
                                                <td style="color: #1e293b; font-weight: bold; font-size: 16px;">
                                                    Total:
                                                </td>
                                                <td align="right" style="color: #2563eb; font-weight: bold; font-size: 18px;">
                                                    R$ <?= number_format($total ?? 0, 2, ',', '.') ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <p style="color: #475569; font-size: 16px; line-height: 1.6; margin: 20px 0;">
                                Nao deixe escapar! Complete sua compra agora e receba seus produtos em casa.
                            </p>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?= $link ?>" style="display: inline-block; background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 8px; font-weight: bold; font-size: 16px;">
                                            Finalizar Compra
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 20px 0 0 0;">
                                Se tiver alguma duvida, estamos aqui para ajudar!
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #64748b; font-size: 12px; margin: 0;">
                                <?= setting('store_name') ?? 'GPS Imports' ?><br>
                                <?= setting('store_address') ?? '' ?><br>
                                <?= setting('store_phone') ?? '' ?>
                            </p>
                            <p style="color: #94a3b8; font-size: 11px; margin: 10px 0 0 0;">
                                Voce recebeu este email porque tem itens no carrinho em nossa loja.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
