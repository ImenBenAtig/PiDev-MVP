<?php

namespace App\Controller;
use App\Entity\Membre;
use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\MembreRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\RememberMe\PersistentToken;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\Form\FormError;

#[Route('/evenement')]
class EvenementController extends AbstractController
{
    #[Route('/back', name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index_back.html.twig', [
            'evenements' => $evenementRepository->findAll(),
            
        ]);
    }

    #[Route('/front', name: 'app_front_evenement_index', methods: ['GET'])]
    public function indexFront(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index_front.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EvenementRepository $evenementRepository): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenementRepository->save($evenement, true);

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/back/{id}', name: 'app_back_evenement_show', methods: ['GET', 'POST'])]
    public function show(Evenement $evenement,ReservationRepository $reservationRepository): Response
    {
        return $this->render('evenement/show_back.html.twig', [
            'evenement' => $evenement,
            'reservations' => $reservationRepository->findByid_evenement($evenement->getId()),
        ]);
    }

    #[Route('/front/{id}', name: 'app_front_evenement_show', methods: ['GET', 'POST'])]
    public function showFront(Evenement $evenement, MembreRepository $membreRepository, ReservationRepository $reservationRepository, Request $request): Response
    {
        $membre = $membreRepository->findOneBy(['id' => 1]);
        $existingReservation = $reservationRepository->findOneBy(['id_evenement' => $evenement, 'id_membre' => $membre]);
        $reservation = new Reservation();
        $reservation->setIdEvenement($evenement);
        $reservation->setIdMembre($membre);
        $form = $this->createForm(ReservationType::class, $reservation, [
            'action' => $this->generateUrl('app_front_evenement_show', ['id' => $evenement->getId()]),
        ]);
        $form->remove('id_evenement');
        $form->remove('id_membre');

        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid() && !$existingReservation) {
            $reservationRepository->save($reservation, true);
    
            // Decrement evenement capacity by 1
            $evenement->setCapacite($evenement->getCapacite() - 1);
            $this->getDoctrine()->getManager()->flush();
    
            return $this->redirectToRoute('app_front_evenement_index', [], Response::HTTP_SEE_OTHER);
        }
    
        // Check if the current user has already reserved this event
        if ($existingReservation) {
            $form->addError(new FormError('Vous avez déjà réservé cet événement.'));
        }
    
        // Check if event capacity is zero
        if ($evenement->getCapacite() == 0) {
            $form->addError(new FormError('La capacité de cet événement est pleine.'));
        }
    
        return $this->render('evenement/show_front.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EvenementRepository $evenementRepository): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenementRepository->save($evenement, true);

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EvenementRepository $evenementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $evenementRepository->remove($evenement, true);
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
