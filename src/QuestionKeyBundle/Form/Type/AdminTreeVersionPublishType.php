<?php

namespace QuestionKeyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormBuilderInterface;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminTreeVersionPublishType extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder->add('comment_admin', 'textarea', array(
            'required' => false,
            'label'=>'Comment (Admin)'
        ));

    }

    public function getName() {
        return 'node';
    }

    public function getDefaultOptions(array $options) {
        return array(
        );
    }
}
