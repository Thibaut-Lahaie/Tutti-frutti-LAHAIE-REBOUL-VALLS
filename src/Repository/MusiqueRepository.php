<?php

namespace App\Repository;

use App\Entity\Musique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Musique>
 *
 * @method Musique|null find($id, $lockMode = null, $lockVersion = null)
 * @method Musique|null findOneBy(array $criteria, array $orderBy = null)
 * @method Musique[]    findAll()
 * @method Musique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MusiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Musique::class);
    }

    public function search(mixed $titre, mixed $nomDeGroupe, mixed $label, mixed $fruit): array
    {
        $qb = $this->createQueryBuilder('m');

        if ($titre) {
            $qb->andWhere('m.reference LIKE :titre')
                ->setParameter('titre', "%$titre%");
        }

        if ($nomDeGroupe) {
            $qb->andWhere('m.nomDeGroupe LIKE :nomDeGroupe')
                ->setParameter('nomDeGroupe', "%$nomDeGroupe%");
        }

        if ($label) {
            $qb->andWhere('m.label LIKE :label')
                ->setParameter('label', "%$label%");
        }

        if ($fruit) {
            $qb->join('m.fruit', 'f')
                ->andWhere('f.nomFr = :fruit')
                ->setParameter('fruit', $fruit);
        }

        return $qb->getQuery()->getResult();
    }
}
