<?php

namespace bayzet\HealthCheckBundle\Command;

use bayzet\HealthCheckBundle\Entity\HealthDataInterface;
use bayzet\HealthCheckBundle\Service\HealthInterface;
use bayzet\HealthCheckBundle\Service\HealthSenderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class SendDataCommand extends Command
{
    public const COMMAND_NAME = 'health:send-info';

    private array $senders;
    /** @var HealthInterface[] */
    private array $healthServices;
    /** @var SymfonyStyle */
    private SymfonyStyle $io;

    public function __construct(HealthSenderInterface... $senders)
    {
        parent::__construct(self::COMMAND_NAME);

        $this->senders = $senders;
    }

    public function addHealthService(HealthInterface $healthService)
    {
        $this->healthServices[] = $healthService;
    }

    protected function configure()
    {
        parent::configure();
        $this->setDescription('Send health data by senders');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->title('Sending health info');

        try {
            $data = array_map(function (HealthInterface $service): HealthDataInterface {
                return $service->getHealthInfo();
            }, $this->healthServices);

            foreach ($this->senders as $sender) {
                $this->outputInfo($sender);
                $sender->send($data);
            }
            $this->io->success('Data is sent by all senders');
        } catch (Throwable $exception) {
            $this->io->error('Exception occurred: ' . $exception->getMessage());
            $this->io->text($exception->getTraceAsString());
        }
    }

    private function outputInfo(HealthSenderInterface $sender)
    {
        if ($name = $sender->getName()) {
            $this->io->writeln($name);
        }
        if ($description = $sender->getDescription()) {
            $this->io->writeln($description);
        }
    }
}