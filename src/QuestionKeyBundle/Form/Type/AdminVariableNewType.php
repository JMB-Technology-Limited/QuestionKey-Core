<?php

namespace QuestionKeyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormBuilderInterface;


/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminVariableNewType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('name', 'text', array(
            'required' => true,
            'label'=>'Name'
        ));

        $builder->add('type', 'choice', array(
            'choices'  => array(
                'Integer' => 'integer',
            ),
            'choices_as_values' => true,
        ));

    }

    public function getName() {
        return 'variable';
    }

    public function getDefaultOptions(array $options) {
        return array(
        );
    }
}
