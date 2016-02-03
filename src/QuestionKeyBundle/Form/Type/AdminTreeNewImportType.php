<?php

namespace QuestionKeyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormBuilderInterface;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeNewImportType extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder->add('titleAdmin', 'text', array(
            'required' => true,
            'label'=>'Title (For Admins)'
        ));

        // TODO enforce slug like!
        // TODO A value is required here when it shouldn't be!
        $builder->add('publicId', 'text', array(
            'required' => false,
            'label'=>'Key'
        ));


        // TODO enforce slug like!
        // TODO A value is required here when it shouldn't be!
        $builder->add('data', 'textarea', array(
            'required' => false,
            'label'=>'Import Data',
            'mapped'=>false,
        ));



    }

    public function getName() {
        return 'tree';
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'QuestionKeyBundle\Entity\Tree',
        );
    }
}
