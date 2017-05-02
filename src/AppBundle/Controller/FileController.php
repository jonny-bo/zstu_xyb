<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class FileController extends BaseController
{
    public function uploadAction(Request $request)
    {
        $file = $request->files->get('file');
        $code = $request->request->get('token');
        $group = $this->getFileGroupService()->getFileGroupByCode($code);
        if (empty($group)) {
            $code = 'default';
        }
        $res = $this->getFileService()->uploadFile($code, $file);
        $res['url'] = $this->get('app.helper.twig_extension')->getFilePath($res['uri']);

        return $this->createJsonResponse($res);
    }

    protected function getFileService()
    {
        return $this->biz->service('File:FileService');
    }

    protected function getFileGroupService()
    {
        return $this->biz->service('File:FileGroupService');
    }
}
