<?php
// lib/mailer.php
// Order confirmation email helper using PHPMailer.

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

if (!function_exists('bytebuy_mailer_send_order_confirmation')) {

    /**
     * Load SMTP configuration (prefers config/mail.local.php over mail.php).
     */
    function bytebuy_mailer_config(): array
    {
        $base = __DIR__ . '/../config/';
        $local = $base . 'mail.local.php';
        if (file_exists($local)) {
            return require $local;
        }
        return require $base . 'mail.php';
    }

    /**
     * Send order confirmation email using PHPMailer.
     */
    function bytebuy_mailer_send_order_confirmation(PDO $pdo, int $orderId, string $recipientEmail, string $recipientName = ''): bool
    {
        if (!$recipientEmail) {
            return false;
        }

        $config = bytebuy_mailer_config();

        $orderStmt = $pdo->prepare('SELECT order_code, guest_email, total, discount_total, coupon_code, created_at FROM orders WHERE id = ? LIMIT 1');
        $orderStmt->execute([$orderId]);
        $order = $orderStmt->fetch();
        if (!$order) {
            return false;
        }

        $itemsStmt = $pdo->prepare('SELECT name, quantity, price FROM order_items WHERE order_id = ?');
        $itemsStmt->execute([$orderId]);
        $items = $itemsStmt->fetchAll();

        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += (float)$item['price'] * (int)$item['quantity'];
        }

        $discount = isset($order['discount_total']) ? (float)$order['discount_total'] : 0.0;
        $total = isset($order['total']) ? (float)$order['total'] : max(0.0, $subtotal - $discount);

        $itemsHtml = '';
        foreach ($items as $item) {
            $qty = (int)$item['quantity'];
            $price = (float)$item['price'];
            $line = $qty * $price;
            $itemsHtml .= sprintf(
                '<tr><td style="padding:6px 12px;border-bottom:1px solid #eee;">%s<br><small>Qty: %d</small></td><td style="padding:6px 12px;border-bottom:1px solid #eee;text-align:right;">$%0.2f</td></tr>',
                htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'),
                $qty,
                $line
            );
        }
        if ($itemsHtml === '') {
            $itemsHtml = '<tr><td colspan="2" style="padding:12px;">No items found.</td></tr>';
        }

        $orderCode = $order['order_code'] ?: ('Order #' . $orderId);
        $createdAt = $order['created_at'] ? (new DateTime($order['created_at']))->format('F j, Y g:i A') : date('F j, Y g:i A');

        $htmlBody = sprintf(
            '<p>Hello %s,</p>
            <p>Thank you for your purchase at ByteBuy. Your order <strong>%s</strong> was received on %s.</p>
            <table style="width:100%%;border-collapse:collapse;margin:16px 0;">%s</table>
            <p style="text-align:right;margin:0 0 4px;"><strong>Subtotal:</strong> $%0.2f</p>
            %s
            <p style="text-align:right;margin:0 0 4px;"><strong>Total:</strong> $%0.2f</p>
            <p>You can track your order status anytime at <a href="%s">%s</a>.</p>
            <p>If you have questions, simply reply to this email.</p>
            <p>Cheers,<br>ByteBuy Team</p>',
            htmlspecialchars($recipientName ?: $recipientEmail, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($orderCode, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8'),
            $itemsHtml,
            $subtotal,
            $discount > 0 ? sprintf('<p style="text-align:right;margin:0 0 4px;"><strong>Discounts:</strong> -$%0.2f%s</p>', $discount, $order['coupon_code'] ? ' (Code: ' . htmlspecialchars($order['coupon_code'], ENT_QUOTES, 'UTF-8') . ')' : '') : '',
            $total,
            bytebuy_mailer_order_status_url($order['order_code']),
            bytebuy_mailer_order_status_url($order['order_code'])
        );

        $plainBodyLines = [
            'Hello ' . ($recipientName ?: $recipientEmail) . ',',
            '',
            'Thank you for your purchase at ByteBuy.',
            'Order: ' . $orderCode,
            'Placed: ' . $createdAt,
            '',
            'Items:',
        ];
        foreach ($items as $item) {
            $plainBodyLines[] = sprintf('- %s (Qty %d): $%0.2f', $item['name'], (int)$item['quantity'], (float)$item['price'] * (int)$item['quantity']);
        }
        $plainBodyLines[] = sprintf('Subtotal: $%0.2f', $subtotal);
        if ($discount > 0) {
            $plainBodyLines[] = sprintf('Discounts: -$%0.2f', $discount);
        }
        $plainBodyLines[] = sprintf('Total: $%0.2f', $total);
        $plainBodyLines[] = '';
        $plainBodyLines[] = 'Track your order: ' . bytebuy_mailer_order_status_url($order['order_code']);
        $plainBodyLines[] = '';
        $plainBodyLines[] = 'If you have questions, reply to this message.';
        $plainBodyLines[] = 'ByteBuy Team';
        $plainBody = implode("\n", $plainBodyLines);

        $autoloader = __DIR__ . '/../vendor/autoload.php';
        if (!file_exists($autoloader)) {
            throw new RuntimeException('PHPMailer autoloader not found. Run composer require phpmailer/phpmailer.');
        }
        require_once $autoloader;
        if (!class_exists(PHPMailer::class)) {
            throw new RuntimeException('PHPMailer is not installed. Run composer require phpmailer/phpmailer.');
        }

        $mailer = new PHPMailer(true);
        try {
            if (($config['driver'] ?? 'smtp') === 'smtp') {
                $mailer->isSMTP();
                $mailer->Host = $config['host'] ?? '';
                $mailer->Port = $config['port'] ?? 587;
                $mailer->SMTPAuth = true;
                $mailer->Username = $config['username'] ?? '';
                $mailer->Password = $config['password'] ?? '';
                if (!empty($config['encryption'])) {
                    $mailer->SMTPSecure = $config['encryption'];
                }
            }

            $mailer->setFrom($config['from_email'] ?? 'no-reply@example.com', $config['from_name'] ?? 'ByteBuy Store');
            $mailer->addAddress($recipientEmail, $recipientName ?: $recipientEmail);
            $mailer->addReplyTo($config['from_email'] ?? 'no-reply@example.com');

            $mailer->isHTML(true);
            $mailer->Subject = sprintf('Your ByteBuy order %s confirmation', $orderCode);
            $mailer->Body = $htmlBody;
            $mailer->AltBody = $plainBody;

            $mailer->send();
            return true;
        } catch (PHPMailerException $mailEx) {
            error_log('Order email failed: ' . $mailEx->getMessage());
        } catch (Throwable $generic) {
            error_log('Order email failed: ' . $generic->getMessage());
        }

        return false;
    }

    function bytebuy_mailer_order_status_url(?string $orderCode): string
    {
        $schemeHost = '';
        if (isset($_SERVER['HTTP_HOST'])) {
            $schemeHost = (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        }
        $self = $_SERVER['PHP_SELF'] ?? '';
        $dir = str_replace('\\', '/', dirname($self));
        if ($dir === '.' || $dir === '/') {
            $dir = '';
        }
        $path = rtrim($schemeHost . $dir, '/') . '/order-status.php';
        if ($orderCode) {
            return $path . '?code=' . urlencode($orderCode);
        }
        return $path;
    }
}
