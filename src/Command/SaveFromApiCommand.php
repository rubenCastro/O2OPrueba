<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Service\ApiPunkService;

class SaveFromApiCommand extends Command
{
    private $apiPunkService;

    protected static $defaultName = 'app:save-from-api';

    public function __construct(APIPunkService $apiPunkService)
    {
        $this->apiPunkService = $apiPunkService;

        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->apiPunkService->saveDatabaseFromApi();
        
        return 0;
    }
}