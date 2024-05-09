<?php

namespace Base\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SetAdmin extends Command
{
    protected $serviceManager;
    protected $console;

    /** @var \Doctrine\Orm\EntityManager $_em */
    protected $_em;

    protected function configure()
    {
        $this
            ->addArgument('username', InputArgument::OPTIONAL, '指定帳號')
            ->addArgument('password', InputArgument::OPTIONAL, '新密碼')
            ->setDescription('重新設定使用者密碼')
            ->setHelp(<<<'EOF'
            username:
                <info>指定帳號</info>

            --password=:
                <info>新密碼</info>

            使用方式:
                <info>base:set-admin test1 s123456</info>
            EOF
            )
        ;
    }

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->serviceManager = $container;

        /** @var \Doctrine\Orm\EntityManager */
        $this->_em = $this->serviceManager->get('doctrine.entitymanager.orm_default');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->console = $output;

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $output->writeln('更改資料中, 請稍待!!');

        $sql = "SELECT * FROM users WHERE username='$username'";
        $res = $this->_em->getConnection()->fetchAssociative($sql);
        if (!$res) {
            $output->writeln("$username 不存在");
            return 0;
        }
        $name = $res['display_name'];

        if ($password) {
            $newPassword = \Laminas\Ldap\Attribute::createPassword($password);
            $this->_em->getConnection()->update('users', ['password' => $newPassword], ['username' => $username]);
            $output->writeln("$name 重設密碼為 $password");
        }

        $output->writeln('<info>' . $username . ' 已經是管理者了!!</info>! ');
        return 0;
    }
}
