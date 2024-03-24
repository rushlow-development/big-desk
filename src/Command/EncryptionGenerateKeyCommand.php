<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:encryption:generate-key',
    description: 'Generate an encryption key for ENCRYPTION_KEY',
)]
class EncryptionGenerateKeyCommand extends Command
{
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        (new SymfonyStyle($input, $output))
            ->text([
                'Keep this key safe. Set it as the ENCRYPTION_KEY env var.',
                'Encryption key:',
                sodium_bin2hex(sodium_crypto_aead_xchacha20poly1305_ietf_keygen()),
            ]);

        return Command::SUCCESS;
    }
}
