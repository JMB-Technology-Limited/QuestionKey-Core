<?php

namespace QuestionKeyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormBuilderInterface;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeVersionEditType extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder->add('title_admin', 'text', array(
            'required' => true,
            'label'=>'Title (Admin)'
        ));


        $builder->add('feature_variables', 'checkbox', array(
            'required' => false,
            'label'=>'Feature Variables'
        ));


        $builder->add('feature_library_content', 'checkbox', array(
            'required' => false,
            'label'=>'Feature Library Content'
        ));


    }

    public function getName() {
        return 'node';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'QuestionKeyBundle\Entity\TreeVersion',
        );
    }
}
