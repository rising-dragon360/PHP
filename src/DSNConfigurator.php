<?php

/**
 * PHPMailer - PHP email creation and transport class.
 * PHP Version 5.5.
 *
 * @see https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 *
 * @author    Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author    Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author    Brent R. Matzelle (original founder)
 * @copyright 2012 - 2020 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace PHPMailer\PHPMailer;

/**
 * Configure PHPMailer via DSN.
 *
 * @author Oleg Voronkovich (voronkovich) <oleg-voronkovich@yandex.ru>
 */
class DSNConfigurator
{
    /**
     * Configure PHPMailer via DSN.
     *
     * @param PHPMailer $mailer PHPMailer instance
     * @param string    $dsn    DSN
     *
     * @return PHPMailer
     */
    public function configure(PHPMailer $mailer, $dsn)
    {
        $config = $this->parseDSN($dsn);

        $this->applyConfig($mailer, $config);

        return $mailer;
    }

    /**
     * Parse DSN.
     *
     * @param string $dsn DSN
     *
     * @throws Exception If DSN is mailformed
     *
     * @return array configruration
     */
    private function parseDSN($dsn)
    {
        $config = parse_url($dsn);

        if (false === $config || !isset($config['scheme']) || !isset($config['host'])) {
            throw new Exception(
                sprintf('Mailformed DSN: "%s".', $dsn)
            );
        }

        if (isset($config['query'])) {
            parse_str($config['query'], $config['query']);
        }

        return $config;
    }

    /**
     * Apply config to mailer.
     *
     * @param PHPMailer $mailer PHPMailer instance
     * @param array     $config Configuration
     *
     * @throws Exception If scheme is invalid
     *
     * @return PHPMailer
     */
    private function applyConfig(PHPMailer $mailer, $config)
    {
        switch ($config['scheme']) {
            case 'mail':
                $mailer->isMail();
                break;
            case 'sendmail':
                $mailer->isSendmail();
                break;
            case 'qmail':
                $mailer->isQmail();
                break;
            case 'smtp':
            case 'smtps':
                $mailer->isSMTP();
                $this->configureSMTP($mailer, $config);
                break;
            default:
                throw new Exception(
                    sprintf(
                        'Invalid scheme: "%s". Allowed values: "mail", "sendmail", "qmail", "smtp", "smtps".',
                        $config['scheme'],
                    )
                );
        }

        return $mailer;
    }

    /**
     * Configure SMTP.
     *
     * @param PHPMailer $mailer PHPMailer instance
     * @param array     $config Configuration
     *
     * @return PHPMailer
     */
    private function configureSMTP($mailer, $config)
    {
        $isSMTPS = 'smtps' === $config['scheme'];

        if ($isSMTPS) {
            $mailer->SMTPSecure = 'tls';
        }

        $mailer->Host = $config['host'];

        if (isset($config['port'])) {
            $mailer->Port = $config['port'];
        } elseif ($isSMTPS) {
            $mailer->Port = SMTP::DEFAULT_SECURE_PORT;
        }

        $mailer->SMTPAuth = isset($config['user']) || isset($config['pass']);

        if (isset($config['user'])) {
            $mailer->Username = $config['user'];
        }

        if (isset($config['pass'])) {
            $mailer->Password = $config['pass'];
        }

        return $mailer;
    }
}
