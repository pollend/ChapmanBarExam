<?php


namespace App\Quiz;

use Symfony\Component\Serializer\Annotation\DiscriminatorMap;


/**
 * @DiscriminatorMap(typeProperty="type", mapping={
 *    "github"="App\Quiz\MultipleChoice"
 * })
 */
interface QuestionInterface
{

}