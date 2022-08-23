<?php

namespace TeyvatPS\managers;

use CombatInvocationsNotify;
use CombatTypeArgument;
use EntityMoveInfo;
use EvtBeingHitInfo;
use Google\Protobuf\Internal\CodedInputStream;
use MotionState;
use TeyvatPS\game\entity\Avatar;
use TeyvatPS\game\entity\Entity;
use TeyvatPS\math\Vector3;
use TeyvatPS\network\NetworkServer;
use TeyvatPS\network\protocol\DataPacket;
use TeyvatPS\network\protocol\ProtocolDictonary;
use TeyvatPS\network\Session;
use TeyvatPS\utils\Logger;
use UnionCmdNotify;

class UnionCmdManager
{
    public static function init(): void
    {
        NetworkServer::registerProcessor(
            UnionCmdNotify::class,
            function (Session $session, UnionCmdNotify $request): void {
                foreach ($request->getCmdList() as $cmd) {
                    if (class_exists(
                        ProtocolDictonary::getProtocolNameFromId(
                            $cmd->getMessageId()
                        )
                    )
                    ) {
                        $unionPacket = new DataPacket($cmd->getMessageId());
                        $unionPacket->data->parseFromStream(
                            new CodedInputStream($cmd->getBody())
                        );
                        NetworkServer::process($session, $unionPacket);
                    } else {
                        Logger::error(
                            "Unknown packet: "
                            . ProtocolDictonary::getProtocolNameFromId(
                                $cmd->getMessageId()
                            )
                        );
                    }
                }
            }
        );
        NetworkServer::registerProcessor(
            CombatInvocationsNotify::class,
            function (
                Session $session,
                CombatInvocationsNotify $request
            ): CombatInvocationsNotify {
                foreach ($request->getInvokeList() as $invoke) {
                    switch ($invoke->getArgumentType()) {
                        case CombatTypeArgument::COMBAT_TYPE_ARGUMENT_EVT_BEING_HIT:
                            $hitInfo = (new EvtBeingHitInfo);
                            $hitInfo->parseFromStream(
                                new CodedInputStream($invoke->getCombatData())
                            );
                            break;
                        case CombatTypeArgument::COMBAT_TYPE_ARGUMENT_ENTITY_MOVE:
                            $moveInfo = (new EntityMoveInfo);
                            $moveInfo->parseFromStream(
                                new CodedInputStream($invoke->getCombatData())
                            );
                            $entity = $session->getWorld()->getEntityById(
                                $moveInfo->getEntityId()
                            );
                            if (!$entity instanceof Entity) {
                                break;
                            }
                            if ($moveInfo->getMotionInfo()->getPos() === null) {
                                break;
                            }
                            if ($moveInfo->getMotionInfo()->getState()
                                === MotionState::MOTION_STATE_STANDBY
                            ) {
                                $entity->setState(
                                    MotionState::MOTION_STATE_STANDBY
                                );

                                break;
                            }
                            $entity->setState(
                                $moveInfo->getMotionInfo()->getState()
                            );
                            $rotation = $moveInfo->getMotionInfo()->getRot();
                            if($rotation === null){
                                $rotation = (new \Vector())->setX(0)->setY(0)->setZ(0);
                            }
                            $entity->setRotation(
                                new Vector3(
                                    $rotation->getX(),
                                    $rotation->getY(),
                                    $rotation->getZ()
                                )
                            );
                            $speed = $moveInfo->getMotionInfo()->getSpeed();
                            if($speed === null){
                                $speed = (new \Vector())->setX(0)->setY(0)->setZ(0);
                            }
                            $entity->setSpeed(
                                new Vector3(
                                    $speed->getX(),
                                    $speed->getY(),
                                    $speed->getZ()
                                )
                            );
                            $motion = $moveInfo->getMotionInfo()->getPos();
                            $entity->setMotion(
                                new Vector3(
                                    $motion->getX(),
                                    $motion->getY(),
                                    $motion->getZ()
                                )
                            );
                            if ($entity instanceof Avatar) {
                                $session->getPlayer()->setPosition(
                                    new Vector3(
                                        $motion->getX(),
                                        $motion->getY(),
                                        $motion->getZ()
                                    )
                                );
                            }

                            break;
                        default:
                            break;
                    }
                }
                return $request;
            }
        );
    }
}
