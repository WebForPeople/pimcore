<?php
declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\ApplicationLoggerBundle\Maintenance;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Pimcore\Bundle\ApplicationLoggerBundle\Enum\LogLevel;
use Pimcore\Bundle\ApplicationLoggerBundle\Handler\ApplicationLoggerDb;
use Pimcore\Config;
use Pimcore\Maintenance\TaskInterface;
use Symfony\Component\Mime\Address;

/**
 * @internal
 */
class LogMailMaintenanceTask implements TaskInterface
{
    private Connection $db;

    private Config $config;

    public function __construct(Connection $db, Config $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * @throws Exception
     */
    public function execute(): void
    {

        if (!empty($this->config['applicationlog']['mail_notification']['send_log_summary'])) {
            $receivers = preg_split('/,|;/', $this->config['applicationlog']['mail_notification']['mail_receiver']);

            array_walk($receivers, static function (&$value) {
                $value = trim($value);
            });

            $logLevel = ($this->config['applicationlog']['mail_notification']['filter_priority'] ?? null);
            $logLevel = $logLevel === null ? LogLevel::Debug : LogLevel::getLogLevel($logLevel);

            $query = 'SELECT * FROM '
                .ApplicationLoggerDb::TABLE_NAME
                .' WHERE maintenanceChecked IS NULL AND priority <= ' . $logLevel->value . ' ORDER BY id DESC';

            $rows = $this->db->fetchAllAssociative($query);
            $limit = 100;
            $rowsProcessed = 0;

            $rowCount = count($rows);
            if ($rowCount) {
                while ($rowsProcessed < $rowCount) {
                    $entries = [];

                    if ($rowCount <= $limit) {
                        $entries = $rows;
                    } else {
                        for ($i = $rowsProcessed; $i < $rowCount && count($entries) < $limit; $i++) {
                            $entries[] = $rows[$i];
                        }
                    }

                    $rowsProcessed += count($entries);

                    $html = var_export($entries, true);
                    $html = "<pre>$html</pre>";
                    $mail = new \Pimcore\Mail();
                    $mail->setIgnoreDebugMode(true);
                    $mail->html($html);
                    foreach ($receivers as $receiver) {
                        $mail->addTo(new Address($receiver, $receiver));
                    }
                    $mail->subject('Error Log '.\Pimcore\Tool::getHostUrl());
                    $mail->send();
                }
            }
        }

        // flag them as checked, regardless if email notifications are enabled or not
        // otherwise, when activating email notifications, you'll receive all log-messages from the past and not
        // since the point when you enabled the notifications
        $this->db->executeQuery(
            'UPDATE '
            . ApplicationLoggerDb::TABLE_NAME
            . ' SET maintenanceChecked = 1 '
            . 'WHERE maintenanceChecked != 1 '
            . 'OR maintenanceChecked IS NULL'
        );
    }
}
