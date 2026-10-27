# Modele de classes - avecLesFemmes

Application de gestion de calabasses participatives (epargne, tontine et prets).

## Diagramme

```mermaid
classDiagram
    class Groupement {
        +int id
        +string nom
    }

    class Role {
        +int id
        +string nom
    }

    class Membre {
        +int id
        +string nomComplet
        +string telephone
        +string adresse
        +string login
        +string password
    }

    class TypeCampagne {
        +int id
        +string nom
    }

    class Campagne {
        +int id
        +string nom
        +DateTime dateDebutAt
        +DateTime dateFinAt
        +int montant
        +DateTimeImmutable createAt
        +DateTime updateAt
    }

    class ModeCotisation {
        +int id
        +bool isLibre
    }

    class Seance {
        +int id
        +DateTime dateSeance
        +string frequence
    }

    class Cotisation {
        +int id
        +float montant
        +DateTime dateCotisation
    }

    class StatutPret {
        +int id
        +string nom
    }

    class Pret {
        +int id
        +float montant
        +float tauxInteret
        +DateTime dateDebut
        +DateTime dateFin
        +string motif
        +DateTimeImmutable createdAt
        +DateTime updatedAt
    }

    class Remboursement {
        +int id
        +float montant
        +DateTime dateRemboursement
        +DateTimeImmutable createdAt
    }

    Groupement "1" --> "*" Membre
    Role "1" --> "*" Membre
    Membre "1" --> "*" ModeCotisation
    Campagne "1" --> "*" ModeCotisation
    TypeCampagne "1" --> "*" Campagne
    Campagne "1" --> "*" Seance
    Seance "1" --> "*" Cotisation
    Membre "1" --> "*" Cotisation
    StatutPret "1" --> "*" Pret
    Membre "1" --> "*" Pret
    Campagne "1" --> "*" Pret
    Pret "1" --> "*" Remboursement
```

## Regles metier

- `ModeCotisation.isLibre` vaut `false` lorsque le membre doit cotiser le montant fixe de la campagne, et `true` lorsqu'il cotise librement.
- La relation `Membre` - `Campagne` est une many-to-many avec attribut : elle est donc representee par `ModeCotisation`.
- Un `Pret` est porte par un `Membre` et peut etre rattache a une `Campagne`.
- Un `Pret` possede un `StatutPret` (en attente, approuve, en cours, rembourse, rejete).
- Les `Remboursement` sont rattaches a un `Pret` et permettent de suivre le capital restant du.

## Captures de la modelisation

Deposer dans ce dossier les photos du tableau et les captures du diagramme de classes
(`modele-classes.png` ou `modele-classes.jpg`).
