<?php

namespace App\DataFixtures;

use App\Entity\Campagne;
use App\Entity\Cotisation;
use App\Entity\Groupement;
use App\Entity\Membre;
use App\Entity\ModeCotisation;
use App\Entity\Pret;
use App\Entity\Remboursement;
use App\Entity\Role;
use App\Entity\Seance;
use App\Entity\StatutPret;
use App\Entity\TypeCampagne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $groupementDakar = (new Groupement())->setNom('Calabasse des femmes de Dakar');
        $groupementThies = (new Groupement())->setNom('Calabasse des femmes de Thies');
        $manager->persist($groupementDakar);
        $manager->persist($groupementThies);

        $roleAdmin = (new Role())->setNom('Admin');
        $roleMembre = (new Role())->setNom('Membre simple');
        $roleTresorier = (new Role())->setNom('Tresorier');
        $roleSecretaire = (new Role())->setNom('Secretaire');
        $manager->persist($roleAdmin);
        $manager->persist($roleMembre);
        $manager->persist($roleTresorier);
        $manager->persist($roleSecretaire);

        $statutEnAttente = (new StatutPret())->setNom('En attente');
        $statutApprouve = (new StatutPret())->setNom('Approuve');
        $statutEnCours = (new StatutPret())->setNom('En cours');
        $statutRembourse = (new StatutPret())->setNom('Rembourse');
        $statutRejete = (new StatutPret())->setNom('Rejete');
        $manager->persist($statutEnAttente);
        $manager->persist($statutApprouve);
        $manager->persist($statutEnCours);
        $manager->persist($statutRembourse);
        $manager->persist($statutRejete);

        $membres = [];
        $membres[] = $this->creerMembre('Aminata Diallo', 'aminata', '77 123 45 67', 'Dakar', $roleAdmin, $groupementDakar);
        $membres[] = $this->creerMembre('Fatou Ndiaye', 'fatou', '76 234 56 78', 'Dakar', $roleTresorier, $groupementDakar);
        $membres[] = $this->creerMembre('Awa Sow', 'awa', '78 345 67 89', 'Thies', $roleSecretaire, $groupementThies);
        $membres[] = $this->creerMembre('Mariama Ba', 'mariama', '70 456 78 90', 'Dakar', $roleMembre, $groupementDakar);
        $membres[] = $this->creerMembre('Khadija Fall', 'khadija', '75 567 89 01', 'Thies', $roleMembre, $groupementThies);
        foreach ($membres as $membre) {
            $manager->persist($membre);
        }

        $typeEpargne = (new TypeCampagne())->setNom('Epargne');
        $typeTontine = (new TypeCampagne())->setNom('Tontine');
        $manager->persist($typeEpargne);
        $manager->persist($typeTontine);

        $epargne = (new Campagne())
            ->setNom('Epargne 2026')
            ->setDateDebutAt(new \DateTime('2026-01-05'))
            ->setDateFinAt(new \DateTime('2026-12-28'))
            ->setMontant(25000)
            ->setCreateAt(new \DateTimeImmutable('2025-12-20'))
            ->setTypeCampagne($typeEpargne);
        $tontine = (new Campagne())
            ->setNom('Tontine 2026')
            ->setDateDebutAt(new \DateTime('2026-02-02'))
            ->setDateFinAt(new \DateTime('2026-11-30'))
            ->setMontant(50000)
            ->setCreateAt(new \DateTimeImmutable('2026-01-15'))
            ->setTypeCampagne($typeTontine);
        $manager->persist($epargne);
        $manager->persist($tontine);

        foreach ($membres as $index => $membre) {
            $modeEpargne = (new ModeCotisation())
                ->setIsLibre(0 === $index % 2)
                ->setCampagne($epargne)
                ->setMembre($membre);
            $modeTontine = (new ModeCotisation())
                ->setIsLibre(false)
                ->setCampagne($tontine)
                ->setMembre($membre);
            $manager->persist($modeEpargne);
            $manager->persist($modeTontine);
        }

        $seances = [];
        $seances[] = (new Seance())
            ->setDateSeance(new \DateTime('2026-01-12'))
            ->setFrequence('hebdomadaire')
            ->setCampagne($epargne);
        $seances[] = (new Seance())
            ->setDateSeance(new \DateTime('2026-01-19'))
            ->setFrequence('hebdomadaire')
            ->setCampagne($epargne);
        $seances[] = (new Seance())
            ->setDateSeance(new \DateTime('2026-02-06'))
            ->setFrequence('mensuelle')
            ->setCampagne($tontine);
        foreach ($seances as $seance) {
            $manager->persist($seance);
        }

        foreach ($seances as $seance) {
            foreach ($membres as $index => $membre) {
                $cotisation = (new Cotisation())
                    ->setMontant((0 === $index % 2) ? 25000.0 : 10000.0)
                    ->setDateCotisation(clone $seance->getDateSeance())
                    ->setSeance($seance)
                    ->setMembre($membre);
                $manager->persist($cotisation);
            }
        }

        $pretEnCours = (new Pret())
            ->setMontant(150000.0)
            ->setTauxInteret(5.0)
            ->setDateDebut(new \DateTime('2026-03-01'))
            ->setDateFin(new \DateTime('2026-08-01'))
            ->setMotif('Achat de marchandises pour le commerce')
            ->setStatut($statutEnCours)
            ->setMembre($membres[1])
            ->setCampagne($epargne);
        $pretEnCours->addRemboursement(
            (new Remboursement())
                ->setMontant(50000.0)
                ->setDateRemboursement(new \DateTime('2026-04-01'))
        );
        $pretEnCours->addRemboursement(
            (new Remboursement())
                ->setMontant(25000.0)
                ->setDateRemboursement(new \DateTime('2026-05-01'))
        );
        $manager->persist($pretEnCours);

        $pretRembourse = (new Pret())
            ->setMontant(100000.0)
            ->setTauxInteret(3.0)
            ->setDateDebut(new \DateTime('2025-06-01'))
            ->setDateFin(new \DateTime('2025-12-01'))
            ->setMotif('Frais de scolarite')
            ->setStatut($statutRembourse)
            ->setMembre($membres[2])
            ->setCampagne($epargne);
        $pretRembourse->addRemboursement(
            (new Remboursement())
                ->setMontant(100000.0)
                ->setDateRemboursement(new \DateTime('2025-11-15'))
        );
        $manager->persist($pretRembourse);

        $pretEnAttente = (new Pret())
            ->setMontant(75000.0)
            ->setTauxInteret(5.0)
            ->setDateDebut(new \DateTime('2026-06-01'))
            ->setMotif('Reparation du toit')
            ->setStatut($statutEnAttente)
            ->setMembre($membres[3])
            ->setCampagne($tontine);
        $manager->persist($pretEnAttente);

        $pretRejete = (new Pret())
            ->setMontant(200000.0)
            ->setTauxInteret(5.0)
            ->setDateDebut(new \DateTime('2026-06-15'))
            ->setMotif('Projet non justifie')
            ->setStatut($statutRejete)
            ->setMembre($membres[4])
            ->setCampagne($tontine);
        $manager->persist($pretRejete);

        $manager->flush();
    }

    private function creerMembre(string $nomComplet, string $login, string $telephone, string $adresse, Role $role, Groupement $groupement): Membre
    {
        return (new Membre())
            ->setNomComplet($nomComplet)
            ->setLogin($login)
            ->setPassword(password_hash('password', PASSWORD_BCRYPT))
            ->setTelephone($telephone)
            ->setAdresse($adresse)
            ->setRole($role)
            ->setGroupement($groupement);
    }
}
