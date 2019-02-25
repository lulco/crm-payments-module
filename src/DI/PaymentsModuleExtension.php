<?php

namespace Crm\PaymentsModule\DI;

use Kdyby\Translation\DI\ITranslationProvider;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

final class PaymentsModuleExtension extends CompilerExtension implements ITranslationProvider
{
    private $defaults = [
        'donationVatRate' => 0,
    ];

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        // set default values if user didn't define them
        $this->config = $this->validateConfig($this->defaults);
        // set extension parameters
        if (!isset($builder->parameters['donationVatRate'])) {
            $builder->parameters['donationVatRate'] = $this->config['donationVatRate'];
        }

        // load services from config and register them to Nette\DI Container
        Compiler::loadDefinitions(
            $builder,
            $this->loadFromFile(__DIR__.'/../config/config.neon')['services']
        );
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();
        // load presenters from extension to Nette
        $builder->getDefinition($builder->getByType(\Nette\Application\IPresenterFactory::class))
            ->addSetup('setMapping', [['Payments' => 'Crm\PaymentsModule\Presenters\*Presenter']]);
    }

    /**
     * Return array of directories, that contain resources for translator.
     * @return string[]
     */
    public function getTranslationResources()
    {
        return [__DIR__ . '/../lang/'];
    }
}
