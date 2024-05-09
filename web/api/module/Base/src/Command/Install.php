<?php

namespace Base\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Install extends Command
{
    protected $serviceManager;
    protected $console;

    /** @var \Doctrine\Orm\EntityManager $_em */
    protected $_em;

    protected function configure()
    {
        $this
            ->addArgument('re-create-database', InputArgument::OPTIONAL, '重建資料庫')
            ->setDescription('安裝系統')
            ->setHelp(<<<'EOF'
            安裝系統:
                <info>base:install</info>

            重建資料庫:
                <info>base:install re-create-database</info>
            EOF
            )
        ;
    }

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->serviceManager = $container;

        /** @var   \Doctrine\Orm\EntityManager */
        $this->_em = $this->serviceManager->get('doctrine.entitymanager.orm_default');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->console = $output;

        $recreateDatabase = $input->getArgument('re-create-database');

        /** @var   \Doctrine\Orm\EntityManager */
        $this->_em = $this->serviceManager->get('doctrine.entitymanager.orm_default');

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->_em);
        $output->writeln('建立資料表中, 請稍待!!');

        $classes = $this->_em->getMetadataFactory()->getAllMetadata();

        // 重建資料庫
        if ($recreateDatabase === 're-create-database') {
            $schemaTool->dropSchema($classes);
        }
        $schemaTool->createSchema($classes);

        // 設定角色
        $this->setRole();

        // 設定保險類型
        $this->setInsuranceTypes();

        // 設定系統管理員帳號
        $this->setAdmin();

        $output->writeln('<info>建立完成</info>! ');
        return 0;
    }

    /**
     * 設定角色
     * @throws \Doctrine\DBAL\DBALException
     */
    private function setRole()
    {
        // 設定預設角色
        $sql = "INSERT INTO `auth_role` (`id`, `name`) VALUES
            (NULL, '開發端系統管理員'),
            (NULL, '系統管理員'),
            (NULL, '宏揚保代'),
            (NULL, '旅行社'),
            (NULL, '保險公司');";
        $this->_em->getConnection()->exec($sql);
        $this->_em->getConnection()->exec('commit');
    }

    /**
     * 設定保險類型
     * @throws \Doctrine\DBAL\DBALException
     */
    private function setInsuranceTypes()
    {
        // 設定預設保險類型
        $sql = "INSERT INTO `insurance_types` (`id`, `name`) VALUES
            (NULL, '旅責險'),
            (NULL, '旅平險');";
        $this->_em->getConnection()->exec($sql);
        $this->_em->getConnection()->exec('commit');
    }

    /**
     * 設定系統管理員帳號
     * @throws \Doctrine\DBAL\DBALException
     */
    private function setAdmin()
    {
        // 設定預設管理者
        $rawPassword = \Laminas\Math\Rand::getString(8, null, true);
        $password = \Laminas\Ldap\Attribute::createPassword($rawPassword);
        $user = new \Base\Entity\Users();
        $user->setUsername('admin');
        $user->setPassword($password);
        $user->setDisplayName('開發端系統管理員');
        $user->setAuthRole($this->_em->getReference('Base\Entity\AuthRole', 1));
        $user->setIsEnable(1); // 狀態(1:啟用, 0:不啟用)
        $user->setCreatedAt(new \DateTime());
        $this->_em->persist($user);
        $this->_em->flush();

        // 顯示預設帳號密碼
        $this->console->writeln(sprintf('<info>預設帳號: %s , 預設密碼: %s</info>', 'admin', $rawPassword));
        $this->_em->getConnection()->exec('commit');
    }
}
