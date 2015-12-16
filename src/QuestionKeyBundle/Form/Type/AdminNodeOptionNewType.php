<?php

namespace QuestionKeyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormBuilderInterface;
use QuestionKeyBundle\Entity\TreeVersion;
use QuestionKeyBundle\Entity\Node;
use Doctrine\ORM\EntityRepository;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminNodeOptionNewType extends AbstractType {




    /** @var Node **/
    protected $fromNode;

    public function __construct(Node $fromNode) {
        $this->fromNode = $fromNode;
    }



    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder->add('title', 'text', array(
            'required' => true,
            'label'=>'Title'
        ));


        $builder->add('body_text', 'textarea', array(
            'required' => false,
            'label'=>'Body (Text)'
        ));

        $builder->add('body_html', 'textarea', array(
            'required' => false,
            'label'=>'Body (HTML)'
        ));


        $builder->add('sort', 'text', array(
            'required' => true,
            'label'=>'Sort'
        ));


        $builder->add('destination_node', 'entity', array(
            'required' => true,
            'label'=>'Destination Node',
            'class' => 'QuestionKeyBundle:Node',
            'expanded'=>true,
            'multiple'=>false,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                ->where('u.treeVersion = :tree_version')
                ->setParameter('tree_version', $this->fromNode->getTreeVersion())
                ->orderBy('u.title', 'ASC');
            },
        ));


    }

    public function getName() {
        return 'node';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'QuestionKeyBundle\Entity\NodeOption',
        );
    }
}
