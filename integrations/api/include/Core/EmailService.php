<?php

namespace Core;
use PHPMailer\PHPMailer\PHPMailer;
use Database\DB;

class EmailService
{
    public static function sendYouRegisteredOn($email, $worldId, $username, $password, $gameWorldUrl)
    {
        global $twig;
        $params = [
            'DIRECTION' => Translator::getDirection(),
            'PLAYER_NAME' => $username,
            'PASSWORD' => $password,
            'GAME_WORLD_URL' => $gameWorldUrl,
            'WORLD_ID' => $worldId,
        ];
        $subject = sprintf(T("Thank you for registering on %s"), $worldId);
        $content = $twig->render('mail/registrationComplete.twig', $params);
        return self::sendMail($email, $subject, $content);
    }

    public static function sendActivationMail($email, $serverId, $worldId, $username, $activationCode)
    {
        global $twig;
        $params = [
            'DIRECTION' => Translator::getDirection(),
            'PLAYER_NAME' => $username,
            'ACTIVATE_URL' => WebService::getIndexUrl() . Translator::getHrefLang() . '?activationCode=' . $activationCode . '&server=' . $worldId . '#activation',
            'ACTIVATION_CODE' => $activationCode,
            'WORLD_ID' => $worldId,
        ];
        $subject = sprintf(T("Thank you for registering on %s"), $worldId);
        $content = $twig->render('mail/activation.twig', $params);
        return self::sendMail($email, $subject, $content);
    }

    public static function sendMail($to, $subject, $html, $priority = 0)
    {
        // Yes, globals are gross. No, I'm not rewriting it.
        global $globalConfig;

        $cfg = $globalConfig['mailer'] ?? [];
        $driver = strtolower($cfg['driver'] ?? 'local');

        if (empty($to)) {
            error_log('sendMail: $to is empty. Congratulations on emailing nobody.');
            return false;
        }
        if ($subject === null) $subject = '';
        if ($html === null) $html = '';

        // Normalize recipients
        $recipients = [];
        if (is_string($to)) {
            $parts = array_map('trim', preg_split('/\s*,\s*/', $to, -1, PREG_SPLIT_NO_EMPTY));
            $recipients = $parts;
        } elseif (is_array($to)) {
            $recipients = $to;
        } else {
            error_log('sendMail: $to must be string or array.');
            return false;
        }

        // Helper: parse "Name <email@host>" or plain email
        $addRecipient = function (PHPMailer $m, $rcpt) {
            // Array form: ['email' => 'name'] or ['email', 'email2']
            if (is_array($rcpt)) {
                foreach ($rcpt as $k => $v) {
                    if (is_int($k)) {
                        $m->addAddress($v);
                    } else {
                        $m->addAddress($k, $v);
                    }
                }
                return;
            }
            if (preg_match('/^(.*)<\s*([^>]+)\s*>$/u', $rcpt, $mm)) {
                $name = trim($mm[1], '"\' ');
                $email = trim($mm[2]);
                $m->addAddress($email, $name);
            } else {
                $m->addAddress($rcpt);
            }
        };

        // Priority normalization
        $p = (int)$priority;
        if ($p < 1 || $p > 5) $p = 3;

        // From details with sensible defaults
        $fromEmail = $cfg['from_email'] ?? '';
        $fromName  = $cfg['from_name']  ?? '';
        if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
            $fromEmail = 'no-reply@' . preg_replace('/^www\./i', '', $host);
        }
        if ($fromName === '') {
            $fromName = preg_replace('/\..*$/', '', $_SERVER['HTTP_HOST'] ?? 'Mailer');
        }

        // Generate a plain-text AltBody from HTML
        $alt = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($alt === '') $alt = $subject;

        try {
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->setFrom($fromEmail, $fromName);

            // Driver selection
            if ($driver === 'smtp') {
                $mail->isSMTP();
                $mail->Host = (string)($cfg['smtp_host'] ?? '');
                $mail->Port = (int)($cfg['smtp_port'] ?? 587);
                $mail->SMTPAuth = (bool)($cfg['smtp_auth'] ?? true);
                $mail->Username = (string)($cfg['smtp_user'] ?? '');
                $mail->Password = (string)($cfg['smtp_pass'] ?? '');

                $enc = strtolower((string)($cfg['smtp_encryption'] ?? 'tls'));
                if ($enc === 'ssl') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                } elseif ($enc === 'tls') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                } else {
                    // none
                    $mail->SMTPSecure = false;
                }

                // Allow self-signed when you’re living dangerously
                if (!empty($cfg['smtp_allow_self_signed'])) {
                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer'       => false,
                            'verify_peer_name'  => false,
                            'allow_self_signed' => true,
                        ],
                    ];
                }
            } else {
                // Local. PHPMailer will use mail() by default; isMail() for clarity
                $mail->isMail();
            }

            // Recipients
            foreach ($recipients as $rcpt) {
                $addRecipient($mail, $rcpt);
            }

            // Headers
            $mail->Priority = $p;
            $importance = ($p <= 2) ? 'High' : (($p === 3) ? 'Normal' : 'Low');
            $msPriority = ($p <= 2) ? 'High' : (($p === 3) ? 'Normal' : 'Low');
            $mail->addCustomHeader('X-Priority', (string)$p);
            $mail->addCustomHeader('X-MSMail-Priority', $msPriority);
            $mail->addCustomHeader('Importance', $importance);

            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $html;
            $mail->AltBody = $alt;

            // Sensible timeouts just in case it hangs
            $mail->Timeout = 20;
            $mail->SMTPKeepAlive = false;

            return $mail->send();
        } catch (Exception $e) {
            // Can modify this to work with notification system later if we want..
            error_log('sendMail PHPMailer error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendForgottenAccounts($email, $gameWorlds)
    {
        global $twig;
        $params = [
            'DIRECTION' => Translator::getDirection(),
            'gameWorlds' => $gameWorlds,
        ];
        $subject = T("You have requested a search for your game world");
        $content = $twig->render('mail/forgottenWorlds.twig', $params);
        return self::sendMail($email, $subject, $content);
    }

    public static function sendPasswordForgotten($email, $worldUniqueId, $worldId, $uid, $recoveryCode)
    {
        global $twig;
        $params = [
            'DIRECTION' => Translator::getDirection(),
            'WORLD_ID' => $worldId,
            'CHANGE_PASSWORD_URL' => WebService::getIndexUrl() . Translator::getHrefLang() . '?recoveryCode=' . $recoveryCode . '&uid=' . $uid . '&server=' . $worldId . '&serverId=' . $worldUniqueId . '#recovery',
        ];
        $subject = sprintf(T("You have requested a new password for %s"), $worldId);
        $content = $twig->render('mail/requestNewPassword.twig', $params);
        return self::sendMail($email, $subject, $content);
    }
}
