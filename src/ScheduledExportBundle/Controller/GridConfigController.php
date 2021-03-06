<?php

namespace Divante\ScheduledExportBundle\Controller;

use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Pimcore\Bundle\AdminBundle\Security\User\User;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\GridConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GridConfigController
 *
 * @package Divante\ScheduledExportBundle\Controller
 */
class GridConfigController extends AdminController
{
    /** @var string True indicator for share globally */
    const SHARE_GLOBALLY_TRUE = "1";

    /**
     * @param Request $request
     * @return \Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse
     * @throws \Exception
     *
     * @Route("/admin/scheduled-export/grid-config/get-list")
     */
    public function getListAction(Request $request) : JsonResponse
    {
        $user = $this->getAdminUser();
        $gridConfigs = new GridConfig\Listing();
        $gridConfigs->setCondition("ownerId = ? or shareGlobally = ?", [$user->getId(), self::SHARE_GLOBALLY_TRUE]);
        $gridConfigs->load();
        $result = [];


        /** @var GridConfig $gridConfig */
        foreach ($gridConfigs->getGridConfigs() as $gridConfig) {
            $classDefinition = ClassDefinition::getById($gridConfig->getClassId());
            $user = \Pimcore\Model\User::getById($gridConfig->getOwnerId());
            if ($user) {
                $userName = $user->getName();
            } else {
                $userName = "unknown";
            }
            $result[] = [
                "id"    => $gridConfig->getId(),
                "name"  => "[" . $gridConfig->getId() . "] " . $classDefinition->getName() . ": " .
                    $gridConfig->getName() . " (" . $userName . ")"
            ];
        }

        return $this->adminJson(["success" => true, "result" => $result]);
    }
}
