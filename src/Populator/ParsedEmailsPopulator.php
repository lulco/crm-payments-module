<?php

namespace Crm\PaymentsModule\Populator;

use Crm\ApplicationModule\Populator\AbstractPopulator;
use Crm\PaymentsModule\MailConfirmation\ParsedMailLogsRepository;

class ParsedEmailsPopulator extends AbstractPopulator
{
    /**
     * @param \Symfony\Component\Console\Helper\ProgressBar $progressBar
     */
    public function seed($progressBar)
    {
        $parsedMailLogs = $this->database->table('parsed_mail_logs');

        for ($i = 0; $i < $this->count; $i++) {
            $payment = null;
            if (rand(1, 3) != 2) {
                $payment = $this->getRecord('payments');
            }

            $data = [
                'variable_symbol' => $payment ? $payment->variable_symbol : $this->faker->randomNumber(10),
                'delivered_at' => $this->faker->dateTimeBetween('-1 years'),
                'created_at' => $this->faker->dateTimeBetween('-1 years'),
                'amount' => $this->faker->randomNumber(4),
                'state' => $this->getState(),
                'message' => implode(' ', $this->faker->words),
                'payment_id' => $payment ? $payment->id : null,
            ];

            $parsedMailLogs->insert($data);

            $progressBar->advance();
        }
    }

    private function getState()
    {
        $types = [
            ParsedMailLogsRepository::STATE_WITHOUT_VS,
            ParsedMailLogsRepository::STATE_ALREADY_PAID,
            ParsedMailLogsRepository::STATE_CHANGED_TO_PAID,
            ParsedMailLogsRepository::STATE_PAYMENT_NOT_FOUND,
            ParsedMailLogsRepository::STATE_DIFFERENT_AMOUNT,
            ParsedMailLogsRepository::STATE_AUTO_NEW_PAYMENT,
        ];

        return $types[ rand(0, count($types) - 1) ];
    }
}
