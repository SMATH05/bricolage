<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use App\Entity\Candidature;
use App\Entity\Chercheur;
use App\Entity\Recruteur;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // --- Create Recruiters ---
        $recruteurs = [];
        for ($i = 1; $i <= 3; $i++) {
            $user = new User();
            $user->setEmail("recruteur$i@example.com");
            $user->setRoles(['ROLE_RECRUTEUR']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setIsVerified(true);
            $manager->persist($user);

            $recruteur = new Recruteur();
            $recruteur->setUser($user);
            $recruteur->setNom("Entreprise $i");
            $recruteur->setEmail($user->getEmail());
            $recruteur->setTelephone("060000000$i");
            $recruteur->setPassword("dummy_pass");
            $manager->persist($recruteur);
            $recruteurs[] = $recruteur;
        }

        // --- Create Researchers ---
        $chercheurs = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail("chercheur$i@example.com");
            $user->setRoles(['ROLE_CHERCHEUR']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setIsVerified(true);
            $manager->persist($user);

            $chercheur = new Chercheur();
            $chercheur->setUser($user);
            $chercheur->setNom("Nom$i");
            $chercheur->setPrenom("Prenom$i");
            $chercheur->setEmail($user->getEmail());
            $chercheur->setIdChercheur("CH_" . uniqid());
            $chercheur->setDescription("Je suis un bricoleur passionné numéro $i.");
            $chercheur->setDisponibilite("Temps plein");
            $chercheur->setMotDePasse("dummy_pass");
            $manager->persist($chercheur);
            $chercheurs[] = $chercheur;
        }

        // --- Create Ads ---
        $annonces = [];
        $titles = ['Réparation de fuite', 'Peinture salon', 'Montage meuble', 'Installation électrique', 'Jardinage'];
        foreach ($recruteurs as $index => $recruteur) {
            for ($j = 1; $j <= 2; $j++) {
                $annonce = new Annonce();
                $annonce->setTitre($titles[array_rand($titles)] . " #" . ($index * 2 + $j));
                $annonce->setDescription("Description détaillée pour l'annonce de " . $recruteur->getNom());
                $annonce->setBudget(50 * $j);
                $annonce->setDatePublication(new \DateTime());
                $annonce->setRecrutId($recruteur);
                $manager->persist($annonce);
                $annonces[] = $annonce;
            }
        }

        // --- Create candidatures ---
        foreach ($annonces as $annonce) {
            $numCandidatures = rand(0, 2);
            if ($numCandidatures > 0) {
                $randomChercheursKeys = (array) array_rand($chercheurs, $numCandidatures);
                foreach ((array) $randomChercheursKeys as $chercheurIndex) {
                    $chercheur = $chercheurs[$chercheurIndex];
                    $candidature = new Candidature();
                    $candidature->setChercheurId($chercheur);
                    $candidature->setAnnonceId($annonce);
                    $candidature->setDatePro(new \DateTime());
                    $candidature->setStatut('En attente');
                    $candidature->setIdCandidature((string) rand(1000, 9999));
                    $manager->persist($candidature);
                }
            }
        }

        $manager->flush();
    }
}
