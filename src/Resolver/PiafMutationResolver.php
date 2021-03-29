<?php

namespace App\Resolver;

use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\Piaf;
use Doctrine\ORM\EntityManagerInterface;

final class PiafMutationResolver implements MutationResolverInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Piaf|null $item
     *
     * @return Piaf
     */
    public function __invoke($item, array $context)
    {
        $piaffeur = $item->getPiaffeur();

        $oldCreneau =  $this->em->getRepository('App:Creneau')->find($item->getCreneau()->getId());
        $oldPiaf =  $this->em->getRepository('App:Piaf')->find($item->getId());

        foreach ($oldCreneau->getPiafs() as $piaf){
            if($piaf->getPiaffeur()->getId() == $piaffeur->getId()){
                return null;
            }
        }
        // Mutation input arguments are in $context['args']['input'].

        // Do something with the book.
        // Or fetch the book if it has not been retrieved.

        // The returned item will pe persisted.

        //mutation REGISTRATION($idPiaf: ID!, $idPiaffeur: String) {
        //    updateaaPiaf(input: { id: $idPiaf, piaffeur: $idPiaffeur, creneau: "/api/creneaus/1947" }) {
        //      piaf {
        //        id
        //        piaffeur {
        //          id
        //        }
        //      }
        //    }
        //  }
        //

        //{
        //  "idPiaf": "api/piafs/19024",
        //  "idPiaffeur": "api/users/1675"
        //}
        return $item;
    }
}