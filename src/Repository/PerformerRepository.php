<?php

namespace App\Repository;

use App\Entity\Performer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function get_class;

/**
 * @method Performer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Performer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Performer[]    findAll()
 * @method Performer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PerformerRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Performer::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof Performer) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param $data
     * @return QueryBuilder Returns an array of Performers objects
     */
    public function filterPerformers($data)
    {
        $query = $this->createQueryBuilder('p');

        $topics = $data->get('topic');
        $summeryTypes = $data->get('summeryTypes');
        if ($summeryTypes) {
            $summeryTypes = implode(', ', $summeryTypes);
        }

        $experience = $data->get('experience');
        if ($experience) {
            if ($experience[0] === 'To 1') {
                $from = 0;
            } else if ($experience[0] === '1-2') {
                $from = 1;
            } else if ($experience[0] === '2-5') {
                $from = 2;
            } else if ($experience[0] === '5-10') {
                $from = 5;
            } else if ($experience[0] === 'From 10') {
                $from = 10;
            }

            if (end($experience) === 'From 10') {
                $to = 100;
            } else if (end($experience) === '5-10') {
                $to = 10;
            } else if (end($experience) === '2-5') {
                $to = 5;
            } else if (end($experience) === '1-2') {
                $to = 2;
            } else if (end($experience) === 'To 1') {
                $to = 1;
            }

            $query
                ->where('p.experience BETWEEN :from AND :to')
                ->setParameter('from', $from)
                ->setParameter('to', $to);
        }

        $rating = $data->get('rating');
        if ($rating) {
            if ($rating[0] === '0-1') {
                $rateFrom = 0;
            } else if ($rating[0] === '1-2') {
                $rateFrom = 1;
            } else if ($rating[0] === '2-3') {
                $rateFrom = 2;
            } else if ($rating[0] === '3-4') {
                $rateFrom = 3;
            } else if ($rating[0] === '4-5') {
                $rateFrom = 4;
            }

            if (end($rating) === '4-5') {
                $rateTo = 5;
            } else if (end($rating) === '3-4') {
                $rateTo = 4;
            } else if (end($rating) === '2-3') {
                $rateTo = 3;
            } else if (end($rating) === '1-2') {
                $rateTo = 2;
            } else if (end($rating) === '0-1') {
                $rateTo = 1;
            }

            $query
                ->andWhere('p.rating BETWEEN :rateFrom AND :rateTo')
                ->setParameter('rateFrom', $rateFrom)
                ->setParameter('rateTo', $rateTo);
        }

        if ($topics) {
            $query
                ->leftJoin('p.categories', 'c')
                ->andWhere('c.id IN (:topics)')
                ->setParameter('topics', $topics);
        }

        if ($summeryTypes) {
            $query
                ->andWhere('p.summeryTypes LIKE :summeryTypes')
                ->setParameter('summeryTypes', '%' . $summeryTypes . '%');
        }

        if ($data->get('sortBy') === 'Most popular') {
            $query
                ->select('COUNT(o) AS HIDDEN orders', 'p')
                ->leftJoin('p.orders', 'o')
                ->orderBy('orders', 'DESC')
                ->groupBy('p');
        } else if ($data->get('sortBy') === 'Highest rating') {
            $query->orderBy('p.rating', 'DESC');
        } else if ($data->get('sortBy') === 'More votes') {
            $query
                ->select('COUNT(r) AS HIDDEN reviews', 'p')
                ->leftJoin('p.reviews', 'r')
                ->orderBy('reviews', 'DESC')
                ->groupBy('p');
        }

        if ($data->get('performer')) {
            $query
                ->leftJoin('p.user', 'u')
                ->andWhere('u.firstName LIKE :search OR u.lastName LIKE :search')
                ->setParameter('search', '%' . $data->get('performer') . '%');
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param $data
     * @return QueryBuilder Returns an array of Performers objects
     */
    public function searchPerformers($data)
    {
        $query = $this->createQueryBuilder('p');
        if (!empty($data['performer'])) {
            $query
                ->leftJoin('p.user', 'u')
                ->where('u.firstName LIKE :search OR u.lastName LIKE :search')
                ->setParameter('search', '%' . $data['performer'] . '%');
        }

        if (!empty($data['category'])) {
            $query
                ->leftJoin('p.categories', 'c')
                ->andWhere('c.topic = :topic')
                ->setParameter('topic', $data['category']);
        }

        return $query->getQuery()->getResult();
    }
}
