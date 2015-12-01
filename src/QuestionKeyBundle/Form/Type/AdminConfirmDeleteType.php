<?php

namespace QuestionKeyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormBuilderInterface;

/**
 *  @license 3-clause BSD
 *  @link https://github.com/QuestionKey/QuestionKey-Core
 */
class AdminConfirmDeleteType extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options) {



    }

    public function getName() {
        return 'node';
    }

    public function getDefaultOptions(array $options) {
        return array(
        );
    }
}
