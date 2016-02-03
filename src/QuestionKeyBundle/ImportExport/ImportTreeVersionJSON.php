<?php

namespace QuestionKeyBundle\ImportExport;

use Doctrine\ORM\EntityRepository;
use QuestionKeyBundle\Entity\Node;
use QuestionKeyBundle\Entity\NodeOption;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\TreeVersionStartingNode;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class ImportTreeVersionJSON
{

    protected $doctrine;
    protected $treeVersion;
    protected $data;

    function __construct($doctrine, TreeVersion $version, $data) {
        $this->doctrine = $doctrine;
        $this->treeVersion = $version;
        $this->data = json_decode($data);
    }

    public function hasData() {
        return (boolean)$this->data;
    }

    public function process() {

        $nodes = array();

        foreach($this->data->nodes as $publicId=>$nodeData) {

            $nodes[$publicId] = new Node();
            $nodes[$publicId]->setPublicId($publicId);
            $nodes[$publicId]->setTreeVersion($this->treeVersion);
            $nodes[$publicId]->setTitle($nodeData->title);
            $nodes[$publicId]->setTitleAdmin($nodeData->title_admin);
            $nodes[$publicId]->setTitlePreviousAnswers($nodeData->title_previous_answers);
            $nodes[$publicId]->setBodyHTML($nodeData->body_html);
            $nodes[$publicId]->setBodyText($nodeData->body_text);

            $this->doctrine->persist($nodes[$publicId]);

        }

        foreach($this->data->nodeOptions as $publicId=>$nodeOptionData) {

            $nodeOption = new NodeOption();
            $nodeOption->setPublicId($publicId);
            $nodeOption->setTreeVersion($this->treeVersion);
            $nodeOption->setTitle($nodeOptionData->title);
            $nodeOption->setBodyHTML($nodeOptionData->body_html);
            $nodeOption->setBodyText($nodeOptionData->body_text);
            $nodeOption->setSort($nodeOptionData->sort);
            $nodeOption->setNode($nodes[$nodeOptionData->node->id]);
            $nodeOption->setDestinationNode($nodes[$nodeOptionData->destination_node->id]);

            $this->doctrine->persist($nodeOption);

        }

        // If this is removed the tests will pass ... but the web app will crash.
        $this->doctrine->flush();

        if ($this->data->start_node && $this->data->start_node->id) {

            $startingNode = new TreeVersionStartingNode();
            $startingNode->setTreeVersion($this->treeVersion);
            $startingNode->setNode($nodes[$this->data->start_node->id]);

            $this->doctrine->persist($startingNode);
        }

        // Seeing as we have to flush in this function, also flush at end to make sure all written.
        $this->doctrine->flush();

    }

}
