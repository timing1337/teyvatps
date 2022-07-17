<?php

namespace TeyvatPS\managers;

use GetAllUnlockNameCardReq;
use GetAllUnlockNameCardRsp;
use SetWidgetSlotReq;
use SetWidgetSlotRsp;
use TeyvatPS\data\ExcelManager;
use TeyvatPS\network\NetworkServer;
use TeyvatPS\network\Session;
use UseItemReq;
use UseItemRsp;
use UseWidgetCreateGadgetReq;
use UseWidgetCreateGadgetRsp;
use WidgetSlotChangeNotify;
use WidgetSlotData;
use WidgetSlotOp;
use WidgetUseAttachAbilityGroupChangeNotify;

class InventoryManager
{
    public static function init(): void
    {
        NetworkServer::registerProcessor(
            GetAllUnlockNameCardReq::class,
            function (
                Session $session,
                GetAllUnlockNameCardReq $request
            ): GetAllUnlockNameCardRsp {
                $rsp = new GetAllUnlockNameCardRsp();
                $rsp->setNameCardList(ExcelManager::getNamecards());

                return $rsp;
            }
        );

        NetworkServer::registerProcessor(
            UseItemReq::class,
            function (Session $session, UseItemReq $request): UseItemRsp {
                $rsp = new UseItemRsp();
                $rsp->setGuid($request->getGuid());
                $rsp->setOptionIdx($request->getOptionIdx());
                $rsp->setTargetGuid($request->getTargetGuid());

                return $rsp;
            }
        );

        NetworkServer::registerProcessor(
            UseWidgetCreateGadgetReq::class,
            function (
                Session $session,
                UseWidgetCreateGadgetReq $request
            ): UseWidgetCreateGadgetRsp {
                $rsp = new UseWidgetCreateGadgetRsp();
                $rsp->setMaterialId($request->getMaterialId());

                return $rsp;
            }
        );

        NetworkServer::registerProcessor(
            SetWidgetSlotReq::class,
            function (Session $session, SetWidgetSlotReq $request): array {
                $UseWidgetCreateGadgetRsp = new UseWidgetCreateGadgetRsp();
                $UseWidgetCreateGadgetRsp->setMaterialId(
                    $request->getMaterialId()
                );
                $widgetUseAttachAbilityGroupChangeNotify
                    = new WidgetUseAttachAbilityGroupChangeNotify();
                $widgetUseAttachAbilityGroupChangeNotify->setMaterialId(
                    $request->getMaterialId()
                );
                $widgetUseAttachAbilityGroupChangeNotify->setIsAttach(true);

                $widgetSlotNotify = new WidgetSlotChangeNotify();
                $rsp = new SetWidgetSlotRsp();
                $rsp->setMaterialId($request->getMaterialId());
                $rsp->setOp($request->getOp());
                $rsp->setTagList($request->getTagList());

                switch ($request->getOp()) {
                    case WidgetSlotOp::WIDGET_SLOT_OP_DETACH:
                        $session->getPlayer()->setWidgetId(-1);
                        $widgetSlotNotify->setOp(
                            WidgetSlotOp::WIDGET_SLOT_OP_DETACH
                        );
                        break;
                    case WidgetSlotOp::WIDGET_SLOT_OP_ATTACH:
                        $session->getPlayer()->setWidgetId(
                            $request->getMaterialId()
                        );
                        $widgetSlotNotify->setOp(
                            WidgetSlotOp::WIDGET_SLOT_OP_ATTACH
                        );
                        $widgetSlotNotify->setSlot(
                            (new WidgetSlotData())->setMaterialId(
                                $request->getMaterialId()
                            )->setIsActive(true)
                        );
                        break;
                }

                return [
                    $widgetSlotNotify,
                    $widgetUseAttachAbilityGroupChangeNotify,
                    $UseWidgetCreateGadgetRsp,
                    $rsp,
                ];
            }
        );
    }
}
