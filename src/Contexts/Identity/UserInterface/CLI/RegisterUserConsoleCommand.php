<?php

declare(strict_types=1);

namespace App\Contexts\Identity\UserInterface\CLI;

use App\Contexts\Identity\Application\IdentityApplicationService;
use DomainException;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'identity:user:register',
    description: 'Register a user in the Identity bounded context (dispatches RegisterUserCommand).',
)]
final class RegisterUserConsoleCommand extends Command
{
    public function __construct(
        private readonly IdentityApplicationService $identityApplication,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = (string) $input->getArgument('email');
        $password = (string) $input->getArgument('password');

        try {
            $this->identityApplication->registerUser($email, $password);
        } catch (InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        } catch (DomainException $e) {
            $io->warning($e->getMessage());

            return Command::FAILURE;
        }

        $io->success('User registered.');

        return Command::SUCCESS;
    }
}
