<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Service\ApiPunkService;

class SaveFromApiCommand extends Command
{
    // the name of the command (the part after "bin/console")
    private $apiPunkService;

    protected static $defaultName = 'app:save-from-api';

    public function __construct(APIPunkService $apiPunkService)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->apiPunkService = $apiPunkService;

        parent::__construct();
    }

    protected function configure(): void
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command.

        // return this if there was no problem running the command
        $a = $this->apiPunkService->saveDatabaseFromApi();
        var_dump($a);
        return 0;
        // or return this if some error happened during the execution
        // return 1;
    }
}