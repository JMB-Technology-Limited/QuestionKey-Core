<?php

namespace QuestionKeyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormBuilderInterface;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeNewVersionType extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('title', 'text', array(
            'required' => true,
            'label'=>'Title'
        ));

    }

    public function getName() {
        return 'version';
    }

    public function getDefaultOptions(array $options) {
        return array(
            // we should be able to do this, but when we do we get errors with fields in the entity not completed!
            //  'data_class' => 'QuestionKeyBundle\Entity\TreeVersion',
        );
    }

}
