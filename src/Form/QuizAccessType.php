<?php

namespace App\Form;

use App\Entity\Quiz;
use App\Entity\QuizAccess;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizAccessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('is_hidden', CheckboxType::class);
        $builder->add('open_date', DateTimeType::class, ['widget' => 'single_text']);
        $builder->add('close_date', DateTimeType::class, ['widget' => 'single_text']);
        $builder->add('num_attempts', IntegerType::class);
        $builder->add('quiz', EntityType::class, [
            'class' => Quiz::class,
            'choice_label' => 'id',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuizAccess::class,
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'quiz_access';
    }
}
