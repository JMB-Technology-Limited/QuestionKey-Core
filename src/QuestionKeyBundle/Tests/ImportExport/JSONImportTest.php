<?php

namespace QuestionKeyBundle\Tests\ImportExport;

use QuestionKeyBundle\Entity\User;
use QuestionKeyBundle\Entity\Tree;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeOption;
use QuestionKeyBundle\Entity\TreeVersionStartingNode;
use QuestionKeyBundle\GetStackTracesForNode;

use QuestionKeyBundle\GetUnreachableBitsOfTree;
use QuestionKeyBundle\ImportExport\ExportTreeVersionJSON;
use QuestionKeyBundle\ImportExport\ImportTreeVersionJSON;
use QuestionKeyBundle\Tests\BaseTestWithDataBase;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class JSONImportTest extends BaseTestWithDataBase
{


    function testNoData1()
    {

        $user = new User();
        $user->setEmail("test@example.com");
        $user->setUsername("test");
        $user->setPassword("ouhosu");

        $tree = new Tree();
        $tree->setTitleAdmin('Tree IMPORTED');
        $tree->setPublicId('tree_imported');
        $tree->setOwner($user);

        $treeVersion = new TreeVersion();
        $treeVersion->setTree($tree);
        $treeVersion->setPublicId('version');

        $importJSON = new ImportTreeVersionJSON($this->em, $treeVersion, '');
        $this->assertFalse($importJSON->hasData());

    }

    function test1()
    {

        $user = new User();
        $user->setEmail("test@example.com");
        $user->setUsername("test");
        $user->setPassword("ouhosu");
        $this->em->persist($user);

        $tree = new Tree();
        $tree->setTitleAdmin('Tree IMPORTED');
        $tree->setPublicId('tree_imported');
        $tree->setOwner($user);
        $this->em->persist($tree);

        $treeVersion = new TreeVersion();
        $treeVersion->setTree($tree);
        $treeVersion->setPublicId('version');
        $this->em->persist($treeVersion);

        $this->em->flush();

        $importJSON = new ImportTreeVersionJSON($this->em, $treeVersion, file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'json_import_1.json'));
        $this->assertTrue($importJSON->hasData());
        $importJSON->process();
        $this->em->flush();


        ///////////////////////////////////////////////////////////////////// TEST IMPORTED TREE


        $nodeRepo = $this->em->getRepository('QuestionKeyBundle:Node');

        $startNode = $nodeRepo->findOneBy(array('treeVersion'=>$treeVersion, 'publicId'=>'start'));
        $this->assertNotNull($startNode);
        $this->assertEquals('Start Here', $startNode->getTitle());


        $endNode = $nodeRepo->findOneBy(array('treeVersion'=>$treeVersion, 'publicId'=>'end'));
        $this->assertNotNull($endNode);
        $this->assertEquals('End Here', $endNode->getTitle());


        $nodeOptionRepo = $this->em->getRepository('QuestionKeyBundle:NodeOption');

        $nodeOption = $nodeOptionRepo->findOneBy(array('treeVersion'=>$treeVersion, 'publicId'=>'option'));
        $this->assertNotNull($nodeOption);
        $this->assertEquals('Click Here',$nodeOption->getTitle());
        $this->assertEquals('start', $nodeOption->getNode()->getPublicId());
        $this->assertEquals('end', $nodeOption->getDestinationNode()->getPublicId());

        $tvsnRepo = $this->em->getRepository('QuestionKeyBundle:TreeVersionStartingNode');

        $tvsn = $tvsnRepo->findOneBy(array('treeVersion'=>$treeVersion));
        $this->assertNotNull($tvsn);
        $this->assertEquals('start', $tvsn->getNode()->getPublicId());


    }


}

