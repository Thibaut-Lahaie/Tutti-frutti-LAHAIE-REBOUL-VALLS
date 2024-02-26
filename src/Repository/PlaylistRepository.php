<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 *
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    private function array_keys_exists(array $keys, array $arr) {
        return !array_diff_key(array_flip($keys), $arr);
    }

    private function array_keys_empty(array $keys, array $arr){
        foreach($keys as $key){
            if($arr[$key] !== ''){
                return false;
            }
        }
        return true;
    }

    private function equal_prevent_empty($a, $b){
        return $b == '' or $a == $b;
    }

    /**
     * @return Playlist[] Returns an array of Playlist objects
     */
    public function findByFilters($user, $filters){
        $res = array();
        $all = $this->findBy(['utilisateur' => $user]);
        $keys = array('annee', 'nom_groupe', 'label', 'genre', 'format', 'fruit');

        if(!$this->array_keys_exists($keys, $filters) or $this->array_keys_empty($keys, $filters)){
            return $all;
        }

        foreach ($all as $playlist){
            if($this->equal_prevent_empty($playlist->getMusique()->getAnnee(), $filters['annee'])
                and $this->equal_prevent_empty($playlist->getMusique()->getNomDeGroupe(), $filters['nom_groupe'])
                    and $this->equal_prevent_empty($playlist->getMusique()->getLabel(), $filters['label'])
                        and $this->equal_prevent_empty($playlist->getMusique()->getGenre(), $filters['genre'])
                            and $this->equal_prevent_empty($playlist->getMusique()->getFormat(), $filters['format'])
                                and $this->equal_prevent_empty($playlist->getMusique()->getFruit(), $filters['fruit'])){
                $res[] = $playlist;
            }
        }
        return $res;
    }

//    /**
//     * @return Playlist[] Returns an array of Playlist objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Playlist
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
