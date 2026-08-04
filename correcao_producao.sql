-- ================================================================
-- CORREÇÃO DE PREÇOS - GPS IMPORTS PRODUÇÃO
-- Data: 2026-08-04
-- Execute este SQL no phpMyAdmin da Hostinger
-- ================================================================

START TRANSACTION;

-- Primeira rodada - correções baseadas em USD da fonte
UPDATE products SET price = 1716.00, cost_price = 1320.00 WHERE id = 1837;
UPDATE products SET price = 356.79, cost_price = 274.45 WHERE id = 2401;
UPDATE products SET price = 1859.00, cost_price = 1430.00 WHERE id = 2680;
UPDATE products SET price = 877.80, cost_price = 731.50 WHERE id = 5742;
UPDATE products SET price = 3181.75, cost_price = 2447.50 WHERE id = 6265;
UPDATE products SET price = 6935.50, cost_price = 5335.00 WHERE id = 6321;
UPDATE products SET price = 965.25, cost_price = 742.50 WHERE id = 6329;
UPDATE products SET price = 1115.40, cost_price = 929.50 WHERE id = 6382;
UPDATE products SET price = 89.38, cost_price = 68.75 WHERE id = 6918;
UPDATE products SET price = 106.54, cost_price = 81.95 WHERE id = 6924;
UPDATE products SET price = 89.38, cost_price = 68.75 WHERE id = 6932;
UPDATE products SET price = 314.60, cost_price = 242.00 WHERE id = 6946;
UPDATE products SET price = 39.33, cost_price = 30.25 WHERE id = 6949;
UPDATE products SET price = 765.05, cost_price = 588.50 WHERE id = 6950;
UPDATE products SET price = 164.45, cost_price = 126.50 WHERE id = 6975;
UPDATE products SET price = 464.75, cost_price = 357.50 WHERE id = 7062;
UPDATE products SET price = 2369.40, cost_price = 1974.50 WHERE id = 7590;
UPDATE products SET price = 1312.03, cost_price = 1009.25 WHERE id = 7597;
UPDATE products SET price = 1082.40, cost_price = 902.00 WHERE id = 7615;
UPDATE products SET price = 1221.00, cost_price = 1017.50 WHERE id = 7616;
UPDATE products SET price = 1221.00, cost_price = 1017.50 WHERE id = 7617;
UPDATE products SET price = 772.20, cost_price = 643.50 WHERE id = 7626;
UPDATE products SET price = 693.00, cost_price = 577.50 WHERE id = 7630;
UPDATE products SET price = 679.80, cost_price = 566.50 WHERE id = 7640;
UPDATE products SET price = 508.20, cost_price = 423.50 WHERE id = 7642;
UPDATE products SET price = 547.80, cost_price = 456.50 WHERE id = 7645;
UPDATE products SET price = 943.80, cost_price = 786.50 WHERE id = 7647;
UPDATE products SET price = 1518.00, cost_price = 1265.00 WHERE id = 7648;
UPDATE products SET price = 2508.00, cost_price = 2090.00 WHERE id = 7652;
UPDATE products SET price = 2508.00, cost_price = 2090.00 WHERE id = 7653;
UPDATE products SET price = 534.60, cost_price = 445.50 WHERE id = 7660;
UPDATE products SET price = 8243.40, cost_price = 6869.50 WHERE id = 7664;
UPDATE products SET price = 13120.80, cost_price = 10934.00 WHERE id = 7737;
UPDATE products SET price = 4481.40, cost_price = 3734.50 WHERE id = 7747;
UPDATE products SET price = 4270.20, cost_price = 3558.50 WHERE id = 7750;
UPDATE products SET price = 10263.00, cost_price = 8552.50 WHERE id = 7755;
UPDATE products SET price = 15312.00, cost_price = 12760.00 WHERE id = 7769;
UPDATE products SET price = 23034.00, cost_price = 19195.00 WHERE id = 7771;
UPDATE products SET price = 19206.00, cost_price = 16005.00 WHERE id = 7772;
UPDATE products SET price = 23034.00, cost_price = 19195.00 WHERE id = 7773;
UPDATE products SET price = 16170.00, cost_price = 13475.00 WHERE id = 7774;
UPDATE products SET price = 16170.00, cost_price = 13475.00 WHERE id = 7775;
UPDATE products SET price = 114.40, cost_price = 88.00 WHERE id = 7835;
UPDATE products SET price = 114.40, cost_price = 88.00 WHERE id = 7836;

-- Segunda rodada - celulares com preços errados
UPDATE products SET price = 924.00, cost_price = 770.00 WHERE id = 7622;
UPDATE products SET price = 1219.36, cost_price = 1016.13 WHERE id = 7628;
UPDATE products SET price = 1219.36, cost_price = 1016.13 WHERE id = 7629;
UPDATE products SET price = 1518.00, cost_price = 1265.00 WHERE id = 7633;
UPDATE products SET price = 877.80, cost_price = 731.50 WHERE id = 7623;
UPDATE products SET price = 877.80, cost_price = 731.50 WHERE id = 7624;
UPDATE products SET price = 877.80, cost_price = 731.50 WHERE id = 7635;
UPDATE products SET price = 943.80, cost_price = 786.50 WHERE id = 7636;
UPDATE products SET price = 1056.00, cost_price = 880.00 WHERE id = 7637;
UPDATE products SET price = 1056.00, cost_price = 880.00 WHERE id = 7638;
UPDATE products SET price = 943.80, cost_price = 786.50 WHERE id = 7639;
UPDATE products SET price = 877.80, cost_price = 731.50 WHERE id = 7646;
UPDATE products SET price = 679.80, cost_price = 566.50 WHERE id = 7594;
UPDATE products SET price = 1221.00, cost_price = 1017.50 WHERE id = 7618;
UPDATE products SET price = 1219.36, cost_price = 1016.13 WHERE id = 7634;
UPDATE products SET price = 1980.00, cost_price = 1650.00 WHERE id = 7632;
UPDATE products SET price = 2310.00, cost_price = 1925.00 WHERE id = 7625;
UPDATE products SET price = 2970.00, cost_price = 2475.00 WHERE id = 7644;
UPDATE products SET price = 3960.00, cost_price = 3300.00 WHERE id = 7651;
UPDATE products SET price = 924.00, cost_price = 770.00 WHERE id = 7659;
UPDATE products SET price = 2310.00, cost_price = 1925.00 WHERE id = 7724;

COMMIT;

-- Verificação (execute separadamente para conferir)
-- SELECT id, name, price FROM products WHERE id IN (7615, 7616, 7625, 7632, 7644, 7651) ORDER BY price;
