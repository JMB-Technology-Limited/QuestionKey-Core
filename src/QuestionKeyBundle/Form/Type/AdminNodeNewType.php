<?php

namespace QuestionKeyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormBuilderInterface;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminNodeNewType extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder->add('titleAdmin', 'text', array(
            'required' => true,
            'label'=>'Title (For Admins)'
        ));

        $builder->add('title', 'text', array(
            'required' => true,
            'label'=>'Title'
        ));

        $builder->add('titlePreviousAnswers', 'text', array(
            'required' => false,
            'label'=>'Title (For Previous Answers)'
        ));


        $builder->add('body_text', 'textarea', array(
            'required' => false,
            'label'=>'Body (Text)'
        ));

        $builder->add('body_html', 'textarea', array(
            'required' => false,
            'label'=>'Body (HTML)'
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
