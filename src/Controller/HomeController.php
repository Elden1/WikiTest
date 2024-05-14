<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
#[Route('/', name: 'home')]
public function index (Request $request): Response {
    return $this->render('home/index.html.twig');
}

#[Route('/creer-voiture', name: 'creer_voiture')]
public function creerVoiture(Request $request, EntityManagerInterface $entityManager): Response
{
    // Récupérer les données du formulaire
    $marque = $request->request->get('marque');
    $modele = $request->request->get('modele');
    $prix = $request->request->get('prix');
    $dateDebutString = $request->request->get('date_debut');
    $dateFinString = $request->request->get('date_fin');
    $statut = $request->request->get('statut');

    // Convertir les chaînes de date en objets DateTime
    $dateDebut = \DateTime::createFromFormat('Y-m-d', $dateDebutString);
    $dateFin = \DateTime::createFromFormat('Y-m-d', $dateFinString);

    // Enregistrer les données dans la base de données
    $voiture = new Voiture();
    $voiture->setMarque($marque);
    $voiture->setModele($modele);
    $voiture->setPrix($prix);
    $voiture->setDateDebut($dateDebut);
    $voiture->setDateFin($dateFin);
    $voiture->setStatut($statut);

    $entityManager->persist($voiture);
    $entityManager->flush();

    // Redirection 
    return $this->redirectToRoute('page_succes');
    }


    #[Route('/page_succes', name: 'page_succes')]
    public function pageSucces(): Response
    {
        return $this->render('voiture/page_succes.html.twig');
    }

    #[Route('/voiture-filtre', name: 'voiture_filtre')]
    public function voitureFiltre(VoitureRepository $voitureRepository): Response
    {
        // Récupérer toutes les entités Voiture
        $voitures = $voitureRepository->findAll();

        return $this->render('voiture/voiture_filtre.html.twig', [
            'voitures' => $voitures,
        ]);
    }

    #[Route('/modifier_voiture/{id}', name: 'modifier_voiture')]
    public function modifierVoiture(Request $request, EntityManagerInterface $entityManager, VoitureRepository $voitureRepository, int $id): Response
    {
        $voiture = $voitureRepository->find($id);

        if (!$voiture) {
            throw $this->createNotFoundException('Voiture non trouvée');
        }

        // Créé le formulaire et envoyer les données au HTML
        return $this->render('voiture/modifier_voiture.html.twig', [
            'voiture' => $voiture,
        ]);
    }

    #[Route('/modifier_voiture/{id}', name: 'modifier_voiture_submit', methods: ['POST'])]
    public function modifierVoitureSubmit(Request $request, EntityManagerInterface $entityManager, VoitureRepository $voitureRepository, int $id): Response
    {
        $voiture = $voitureRepository->find($id);

        if (!$voiture) {
            throw $this->createNotFoundException('Voiture non trouvée');
        }

        // Traitement du formulaire

        $voiture->setMarque($request->request->get('marque'));
        $voiture->setModele($request->request->get('modele'));
        $voiture->setPrix($request->request->get('prix'));

        // Enregistrez les modifications dans la base de données
        $entityManager->flush();

        return $this->redirectToRoute('page_succes');
    }

    #[Route('/enregistrer_modification/{id}', name: 'enregistrer_modification')]
    public function enregistrerModif (Request $request, EntityManagerInterface $entityManager, Voiture $voiture): Response
    {
        // Récupérer les données modifiées du formulaire
        $marque = $request->request->get('marque');
        $modele = $request->request->get('modele');
        $prix = $request->request->get('prix');
        $dateDebut = $request->request->get('date_debut');
        $dateFin = $request->request->get('date_fin');
        $statut = $request->request->get('statut');
    
        // Mettre à jour les propriétés de la voiture
        $voiture->setMarque($marque);
        $voiture->setModele($modele);
        $voiture->setPrix($prix);
        $voiture->setDateDebut(new \DateTime($dateDebut));
        $voiture->setDateFin(new \DateTime($dateFin));
        $voiture->setStatut($statut);
    
        // Enregistrer les modifications dans la base de données
        $entityManager->flush();
    
        return $this->redirectToRoute('voiture_filtre');
    }

    #[Route('/supprimer_voiture/{id}', name: 'supprimer_voiture')]
    public function supprimerVoiture($id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer la voiture à supprimer
        $voiture = $entityManager->getRepository(Voiture::class)->find($id);

        if (!$voiture) {
            throw $this->createNotFoundException('La voiture avec l\'identifiant '.$id.' n\'existe pas.');
        }

        // Supprimer la voiture
        $entityManager->remove($voiture);
        $entityManager->flush();

        // Rediriger vers une page de confirmation ou une autre page appropriée
        return $this->redirectToRoute('voiture_filtre');
    }

    #[Route('/recherche-voitures', name: 'recherche_voitures')]
    public function rechercheVoitures(Request $request, VoitureRepository $voitureRepository): Response
    {
        // Récupérer les dates de début et de fin depuis le formulaire
        $dateDebut = new \DateTime($request->request->get('date_debut'));
        $dateFin = new \DateTime($request->request->get('date_fin'));
        $prixMax = $request->request->get('prix_max');

        // Cas ou prix max n'est pas indiqué
        if(empty($prixMax)){
            $prixMax = 99999;
        }

        // Rechercher les voitures disponibles entre les dates spécifiées
        $voituresDisponibles = $voitureRepository->findVoituresDisponiblesEntreDates($dateDebut, $dateFin, $prixMax);

        // Si vide, ajouter diponibilités à +/- 1 jours
        if (empty($voituresDisponibles)) {
            $dateDebut = (clone $dateDebut)->modify('-1 day');
            $dateFin = (clone $dateFin)->modify('+1 day');
            $voituresDisponibles = $voitureRepository->findVoituresDisponiblesEntreDates($dateDebut, $dateFin, $prixMax);
        }

        // Passer les variables au HTML
        return $this->render('voiture/voitures_disponibles.html.twig', [
            'voitures' => $voituresDisponibles,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'prix_max' => $prixMax
        ]);
    }
}