<?php

namespace QuestionKeyBundle\Form\Type;

use QuestionKeyBundle\Entity\Node;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminNodeEditAddLibraryContentIfVariable extends AbstractType {



    /** @var Node **/
    protected $onNode;

    public function __construct(Node $onNode) {
        $this->onNode = $onNode;
    }


    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('variable', 'entity', array(
            'required' => true,
            'label'=>'Variable',
            'class' => 'QuestionKeyBundle:Variable',
            'expanded'=>true,
            'multiple'=>false,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.treeVersion = :tree_version')
                    ->setParameter('tree_version', $this->onNode->getTreeVersion())
                    ->orderBy('u.name', 'ASC');
            },
        ));

        $builder->add('action', 'choice', array(
            'choices'  => array(
                '==' => '==',
                '>' => '>',
            ),
            'choices_as_values'=>true,
            'required' => true,
        ));

        $builder->add('value', 'text', array(
            'required' => true,
            'label'=>'Value'
        ));


    }

    public function getName() {
        return 'node';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'QuestionKeyBundle\Entity\NodeHasLibraryContentIfVariable',
        );
    }
}
