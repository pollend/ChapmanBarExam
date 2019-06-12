<?php


namespace App\Security;


use App\Entity\Classroom;
use App\Entity\QuizAccess;
use App\Entity\QuizSession;
use App\Entity\User;
use App\Repository\ClassroomRepository;
use App\Repository\QuizAccessRepository;
use App\Repository\QuizSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class QuizAccessVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const START = 'start';

    private $security;
    private $accessDecisionManager;
    private $entityManager;

    public function __construct(Security $security, AccessDecisionManagerInterface $accessDecisionManager,EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->entityManager = $entityManager;

    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::START])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof QuizAccess) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        /** make sure user is logged in */
        if(!$user instanceof User){
            return false;
        }


        /** @var QuizAccess $access */
        $access =  $subject;

        switch ($attribute) {
            case self::START:
                return $this->canStart($access, $user);
        }
        throw new \LogicException('This code should not be reached!');
    }

    private function canStart(QuizAccess $access, User $user){
        if($access->isOpen($user) && $access->getClassroom()->isUserRegistered($user)){
            return true;
        }
        return false;
    }
}