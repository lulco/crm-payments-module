<?php

namespace Crm\PaymentsModule\Components;

use Crm\ApplicationModule\Widget\BaseWidget;
use Crm\ApplicationModule\Widget\WidgetManager;
use Crm\PaymentsModule\Report\NoRecurrentChargeReport;
use Crm\PaymentsModule\Report\PaidNextSubscriptionReport;
use Crm\PaymentsModule\Report\RecurrentWithoutProfileReport;
use Crm\PaymentsModule\Report\StoppedOnFirstSubscriptionReport;
use Crm\PaymentsModule\Report\TotalPaidSubscriptionsReport;
use Crm\PaymentsModule\Report\TotalRecurrentSubscriptionsReport;
use Crm\SubscriptionsModule\Report\ReportGroup;
use Crm\SubscriptionsModule\Report\ReportTable;
use Crm\SubscriptionsModule\Repository\SubscriptionTypesRepository;

class SubscriptionTypeReports extends BaseWidget
{
    private $templateName = 'subscription_type_reports.latte';

    private $subscriptionTypesRepository;

    public function __construct(
        WidgetManager $widgetManager,
        SubscriptionTypesRepository $subscriptionTypesRepository
    ) {
        parent::__construct($widgetManager);
        $this->subscriptionTypesRepository = $subscriptionTypesRepository;
    }

    public function identifier()
    {
        return 'subscriptiontypesreports';
    }

    public function render($subscriptionTypeId)
    {
        $reportTable = new ReportTable(
            ['subscription_type_id' => $subscriptionTypeId],
            $this->subscriptionTypesRepository->getDatabase(),
            new ReportGroup('users.source')
        );
        $reportTable
            ->addReport(new TotalPaidSubscriptionsReport(''))
            ->addReport(new TotalRecurrentSubscriptionsReport(''))
            ->addReport(new NoRecurrentChargeReport(''), [\Crm\PaymentsModule\Report\TotalRecurrentSubscriptionsReport::class])
            ->addReport(new StoppedOnFirstSubscriptionReport(''), [\Crm\PaymentsModule\Report\TotalRecurrentSubscriptionsReport::class])
            ->addReport(new PaidNextSubscriptionReport('', 1), [\Crm\PaymentsModule\Report\TotalRecurrentSubscriptionsReport::class])
            ->addReport(new PaidNextSubscriptionReport('', 2))
            ->addReport(new PaidNextSubscriptionReport('', 3))
            ->addReport(new RecurrentWithoutProfileReport(''), [\Crm\PaymentsModule\Report\TotalRecurrentSubscriptionsReport::class])
        ;
        $this->template->reportTables = [
            'Zdroj pouzivatelov' => $reportTable->getData(),
        ];

        $this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . $this->templateName);
        $this->template->render();
    }
}
