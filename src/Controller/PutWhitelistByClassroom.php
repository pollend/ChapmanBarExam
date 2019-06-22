<?php


namespace App\Controller;


use App\Entity\Classroom;
use App\Entity\UserWhitelist;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PutWhitelistByClassroom
{
    private $entityManager;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager,ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function __invoke(Classroom $classroom,Request $request)
    {
        $classEmails = Collection::make($classroom->getEmailWhitelist())->keyBy(function ($e) {
            return $e->getEmail();
        });

        $constraints = new Assert\Collection([
            'emails' => [new Assert\Type('array')]
        ]);
        $data = json_decode($request->getContent(), true);
        $errors = $this->validator->validate($data, $constraints);
        if (count($errors) > 0)
            return $errors;

        $emails = Collection::make($data['emails'])->map(function ($value) {
            return trim($value);
        })->unique();
        $joint = $classEmails->keys()->intersect($emails);
        $classEmails->each(function ($value,$key) use($joint){
            if(!$joint->contains($key)){
                $this->entityManager->remove($value);
            }
        });
        foreach($emails as $email){
            if(!$joint->contains($email)){
                $userEmail = new UserWhitelist();
                $userEmail->setEmail($email);
                $userEmail->setClassroom($classroom);
                $this->entityManager->persist($userEmail);
            }
        }


        $this->entityManager->flush();
        $this->entityManager->refresh($classroom);
        $result = [];
        /** @var UserWhitelist $whitelist */
        foreach ($classroom->getEmailWhitelist() as $whitelist){
            $result[] = $whitelist->getEmail();
        }
        return $result;
    }
}