<?php

namespace QuestionKeyBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use QuestionKeyBundle\Entity\LibraryContent;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeOption;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\VisitorSession;
use QuestionKeyBundle\Entity\VisitorSessionRanTreeVersion;
use QuestionKeyBundle\Entity\TreeVersionPreviewcode;

class PrePersistEventListener  {


    const MIN_LENGTH = 10;
    const MIN_LENGTH_BIG = 100;
    const MAX_LENGTH = 250;
    const LENGTH_STEP = 1;

    function PrePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if ($entity instanceof Node) {
            if (!$entity->getPublicId()) {
                $manager = $args->getEntityManager()->getRepository('QuestionKeyBundle\Entity\Node');
                $idLen = self::MIN_LENGTH;
                $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                while($manager->doesPublicIdExist($id, $entity->getTreeVersion())) {
                    if ($idLen < self::MAX_LENGTH) {
                        $idLen = $idLen + self::LENGTH_STEP;
                    }
                    $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                }
                $entity->setPublicId($id);
            }
        } elseif ($entity instanceof NodeOption) {
            if (!$entity->getPublicId()) {
                $manager = $args->getEntityManager()->getRepository('QuestionKeyBundle\Entity\NodeOption');
                $idLen = self::MIN_LENGTH;
                $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                while($manager->doesPublicIdExist($id, $entity->getTreeVersion())) {
                    if ($idLen < self::MAX_LENGTH) {
                        $idLen = $idLen + self::LENGTH_STEP;
                    }
                    $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                }
                $entity->setPublicId($id);
            }
        } elseif ($entity instanceof TreeVersion) {
            if (!$entity->getPublicId()) {
                $manager = $args->getEntityManager()->getRepository('QuestionKeyBundle\Entity\TreeVersion');
                $idLen = self::MIN_LENGTH;
                $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                while($manager->doesPublicIdExist($id, $entity->getTree())) {
                    if ($idLen < self::MAX_LENGTH) {
                        $idLen = $idLen + self::LENGTH_STEP;
                    }
                    $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                }
                $entity->setPublicId($id);
            }
        } elseif ($entity instanceof VisitorSession) {
            if (!$entity->getPublicId()) {
                $manager = $args->getEntityManager()->getRepository('QuestionKeyBundle\Entity\VisitorSession');
                $idLen = self::MIN_LENGTH_BIG;
                $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                while($manager->doesPublicIdExist($id)) {
                    if ($idLen < self::MAX_LENGTH) {
                        $idLen = $idLen + self::LENGTH_STEP;
                    }
                    $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                }
                $entity->setPublicId($id);
            }
        } elseif ($entity instanceof VisitorSessionRanTreeVersion) {
            if (!$entity->getPublicId()) {
                $manager = $args->getEntityManager()->getRepository('QuestionKeyBundle\Entity\VisitorSessionRanTreeVersion');
                $idLen = self::MIN_LENGTH;
                $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                while($manager->doesPublicIdExist($id, $entity->getVisitorSession())) {
                    if ($idLen < self::MAX_LENGTH) {
                        $idLen = $idLen + self::LENGTH_STEP;
                    }
                    $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                }
                $entity->setPublicId($id);
            }
        } elseif ($entity instanceof TreeVersionPreviewCode) {
            if (!$entity->getCode()) {
                $manager = $args->getEntityManager()->getRepository('QuestionKeyBundle\Entity\TreeVersionPreviewCode');
                $idLen = self::MIN_LENGTH_BIG;
                $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                while($manager->doesCodeExist($id, $entity->getTreeVersion())) {
                    if ($idLen < self::MAX_LENGTH) {
                        $idLen = $idLen + self::LENGTH_STEP;
                    }
                    $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                }
                $entity->setCode($id);
            }
        } elseif ($entity instanceof LibraryContent) {
            if (!$entity->getPublicId()) {
                $manager = $args->getEntityManager()->getRepository('QuestionKeyBundle\Entity\LibraryContent');
                $idLen = self::MIN_LENGTH;
                $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                while($manager->doesPublicIdExist($id, $entity->getTreeVersion())) {
                    if ($idLen < self::MAX_LENGTH) {
                        $idLen = $idLen + self::LENGTH_STEP;
                    }
                    $id =  \QuestionKeyBundle\QuestionKeyBundle::createKey(1,$idLen);
                }
                $entity->setPublicId($id);
            }
        }

    }

}
