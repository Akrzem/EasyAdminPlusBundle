<?php

namespace Akrzem\EasyAdminPlusBundle\Auth\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Akrzem\EasyAdminPlusBundle\Entity\User;
use Akrzem\EasyAdminPlusBundle\Auth\Event\EasyAdminPlusAuthEvents;

class UserEnableCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('wandi:easy-admin-plus:user:enable')
            ->setDescription('Enable an admin')
            ->setDefinition(
                [
                    new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $dispatcher = $container->get('event_dispatcher');

        $username = $input->getArgument('username');

        /** @var User $user */
        if (null === ($user = $em->getRepository(User::class)->findOneByUsername($username))) {
            $output->writeln(sprintf('<error>User %s was not found</error>', $username));
        }

        $dispatcher->dispatch(EasyAdminPlusAuthEvents::USER_PRE_ENABLE, new GenericEvent($user));

        $user->setEnabled(true);
        $em->flush();

        $dispatcher->dispatch(EasyAdminPlusAuthEvents::USER_POST_ENABLE, new GenericEvent($user));

        $output->writeln(sprintf('User <comment>%s</comment> enabled', $username));
    }
}
