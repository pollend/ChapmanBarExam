<?php


namespace App\Form;


use App\Entity\Classroom;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassroomType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',TextType::class,array());
        $builder->add('description',TextareaType::class,[ 'required'   => false]);
        $builder->add('course_number',TextType::class,[ 'required'   => false]);

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Classroom::class,
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return 'classroom';
    }
}