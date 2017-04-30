<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;

class CrudCreateCommand extends Command
{
    protected function configure()
    {
        $this
            ->addArgument(
                'moduleName',
                InputArgument::OPTIONAL,
                '模块名称?'
            )
            ->addArgument(
                'className',
                InputArgument::OPTIONAL,
                '类名称?',
                ''
            )
            ->setName('biz:generate:crud')
            ->setDescription('Generates a CRUD Service and Dao on Biz (第一个参数为模块名, 第二个参数为类名。类名默认为模块名 eg: User UserInfo)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleName = $input->getArgument('moduleName');
        $className  = $input->getArgument('className');
        $className  = $className ? $className : $moduleName;

        if (!$moduleName) {
            throw new \RuntimeException("模块名称不能为空！");
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', $moduleName)) {
            throw new \RuntimeException("模块名称只能为英文！");
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', $className)) {
            throw new \RuntimeException("类名称只能为英文！");
        }

        $filesystem = new Filesystem();

        $moduleName = ucfirst($moduleName);
        $className = ucfirst($className);

        $dir  = $this->getContainer()->getParameter('kernel.root_dir')."/../src/Biz";

        $output->writeln('<info>开始创建...</info>');

        $filesystem->mkdir($dir."/".$moduleName);
        $filesystem->mkdir($dir."/".$moduleName."/Service");
        $filesystem->mkdir($dir."/".$moduleName."/Dao");
        $filesystem->mkdir($dir."/".$moduleName."/Service/Impl");
        $filesystem->mkdir($dir."/".$moduleName."/Dao/Impl");

        $serviceFile      = $dir."/".$moduleName."/Service/".$className."Service.php";
        $serviceImplFile = $dir."/".$moduleName."/Service/Impl/".$className."ServiceImpl.php";
        $daoFile         = $dir."/".$moduleName."/Dao/".$className."Dao.php";
        $daoImplFile     = $dir."/".$moduleName."/Dao/Impl/".$className."DaoImpl.php";

        if ($filesystem->exists($serviceFile)) {
            throw new \RuntimeException("{$className}Service 已经存在！");
        }
        $this->createServiceFile($className, $moduleName, $serviceFile);
        $output->writeln("<info>{$className}Service 创建成功!</info>");

        if ($filesystem->exists($serviceImplFile)) {
            throw new \RuntimeException("{$className}ServiceImpl 已经存在！");
        }
        $this->createServiceImplFile($className, $moduleName, $serviceImplFile);
        $output->writeln("<info>{$className}ServiceImpl 创建成功!</info>");

        if ($filesystem->exists($daoFile)) {
            throw new \RuntimeException("{$className}Dao 已经存在！");
        }
        $this->createDaoFile($className, $moduleName, $daoFile);
        $output->writeln("<info>{$className}Dao 创建成功!</info>");

        if ($filesystem->exists($daoImplFile)) {
            throw new \RuntimeException("{$className}DaoImpl 已经存在！");
        }
        $this->createDaoImplFile($className, $moduleName, $daoImplFile);
        $output->writeln("<info>{$className}DaoImpl 创建成功!</info>");
    }

    protected function createServiceFile($className, $moduleName, $serviceFile)
    {
        $name = lcfirst($className);
        $serviceText = '<?php

namespace Biz\\'.$moduleName.'\\Service;

interface '.$className.'Service
{
    public function get'.$className.'($'.$name.'Id);

    public function search'.$className.'($conditions, $orderBy, $start, $limit);

    public function search'.$className.'Count($conditions);

    public function create'.$className.'($fields);

    public function update'.$className.'($'.$name.'Id, $fields);

    public function delete'.$className.'($'.$name.'Id);
}
';
        file_put_contents($serviceFile, $serviceText);
    }

    protected function createServiceImplFile($className, $moduleName, $serviceImplFile)
    {
        $name = lcfirst($className);
        $serviceImplText = '<?php

namespace Biz\\'.$moduleName.'\\Service\Impl;

use Biz\Common\BaseService;
use Biz\\'.$moduleName.'\\Service\\'.$className.'Service;

class '.$className.'ServiceImpl extends BaseService implements '.$className.'Service
{
    public function get'.$className.'($'.$name.'Id)
    {
        return $this->get'.$className.'Dao()->get($'.$name.'Id);
    }

    public function search'.$className.'($conditions, $orderBy, $start, $limit)
    {
        return $this->get'.$className.'Dao()->search($conditions, $orderBy, $start, $limit);
    }

    public function search'.$className.'Count($conditions)
    {
        return $this->get'.$className.'Dao()->count($conditions);
    }

    public function create'.$className.'($fields)
    {
        return $this->get'.$className.'Dao()->create($fields);
    }

    public function update'.$className.'($'.$name.'Id, $fields)
    {
        return $this->get'.$className.'Dao()->update($'.$name.'Id, $fields);
    }

    public function delete'.$className.'($'.$name.'Id)
    {
        return $this->get'.$className.'Dao()->delete($'.$name.'Id);
    }

    protected function get'.$className.'Dao()
    {
        return $this->biz->dao(\''.$moduleName.':'.$className.'Dao\');
    }
}
';
        file_put_contents($serviceImplFile, $serviceImplText);
    }

    protected function createDaoFile($className, $moduleName, $daoFile)
    {
        $daoText = '<?php

namespace Biz\\'.$moduleName.'\\Dao;

use Codeages\Biz\Framework\Dao\GeneralDaoInterface;

interface '.$className.'Dao extends GeneralDaoInterface
{
}
';
        file_put_contents($daoFile, $daoText);
    }

    protected function createDaoImplFile($className, $moduleName, $daoImplFile)
    {
        $daoImplText = '<?php

namespace Biz\\'.$moduleName.'\\Dao\Impl;

use Codeages\Biz\Framework\Dao\GeneralDaoImpl;
use Biz\\'.$moduleName.'\\Dao\\'.$className.'Dao;

class '.$className.'DaoImpl extends GeneralDaoImpl implements '.$className.'Dao
{
    protected $table = \'\';

    public function declares()
    {
        return array(
            \'timestamps\' => array(),
            \'serializes\' => array(),
            \'orderbys\' => array(),
            \'conditions\' => array(),
        );
    }
}
';
        file_put_contents($daoImplFile, $daoImplText);
    }

    protected function getContainer()
    {
        return $this->getApplication()->getKernel()->getContainer();
    }
}
