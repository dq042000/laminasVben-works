<?php

declare(strict_types=1);

namespace Application\Controller;

use Base\Controller\BaseController;
use Laminas\ApiTools\Admin\Module as AdminModule;
use Laminas\Http\Response;
use Laminas\View\Model\ViewModel;

use function class_exists;

class IndexController extends BaseController
{
    /**
     * @return Response|ViewModel
     */
    public function indexAction()
    {
        if (class_exists(AdminModule::class, false)) {
            return $this->redirect()->toRoute('api-tools/ui');
        }
        return new ViewModel();
    }
}
