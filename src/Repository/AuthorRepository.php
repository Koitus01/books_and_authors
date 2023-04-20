<?php

namespace App\Repository;

use App\DTO\AuthorDTO;
use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function save(Author $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Author $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws EntityNotFoundException
     */
    public function findOrThrow($id, $lockMode = null, $lockVersion = null): Author
    {
        if (!$entity = $this->find($id, $lockMode, $lockVersion)) {
            throw new EntityNotFoundException("Author with id $id does not exist");
        }

        return $entity;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function findOneByName(AuthorDTO $DTO): Author
    {
        $criteria = [
            'first_name' => $DTO->first_name,
            'second_name' => $DTO->second_name
        ];
        if ($DTO->third_name) {
            $criteria['third_name'] = $DTO->third_name;
        }

        $entity = $this->findOneBy($criteria);
        if (!$entity) {
            $error = 'Author' . $DTO->second_name . ' ' . $DTO->first_name . ' ' . $DTO->third_name . ' does not exist';
            throw new EntityNotFoundException($error);
        }

        return $entity;
    }

//    /**
//     * @return Author[] Returns an array of Author objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Author
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
