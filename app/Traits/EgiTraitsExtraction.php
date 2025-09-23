<?php

namespace App\Traits;

use App\Models\Egi;

/**
 * Trait per l'estrazione centralizzata dei metadati dai traits EGI
 *
 * Questo trait fornisce metodi unificati per estrarre informazioni
 * specifiche dai traits degli EGI, utilizzando la tabella egi_traits
 */
trait EgiTraitsExtraction {
    /**
     * Estrae il valore dell'autore dai traits
     *
     * @param Egi $egi
     * @return string
     */
    protected function extractAuthorFromTraits(Egi $egi): string {
        // Ordine di priorità per i tipi di trait che identificano l'autore
        $authorTraitTypes = ['Autore', 'Author', 'Artist', 'Artista', 'Creatore', 'Creator'];

        // Cerca nei traits dell'EGI
        foreach ($authorTraitTypes as $traitType) {
            $trait = $egi->traits()
                ->whereHas('traitType', function ($query) use ($traitType) {
                    $query->where('name', $traitType);
                })
                ->first();

            if ($trait && $trait->value) {
                return $trait->value;
            }
        }

        // Se non trovato nei traits, usa il nome del proprietario come fallback
        return $egi->user->name ?? 'Autore Sconosciuto';
    }

    /**
     * Estrae l'anno dai traits
     *
     * @param Egi $egi
     * @return string|null
     */
    protected function extractYearFromTraits(Egi $egi): ?string {
        $yearTraitTypes = ['Anno', 'Year', 'Data', 'Date', 'Anno di creazione', 'Creation year'];

        foreach ($yearTraitTypes as $traitType) {
            $trait = $egi->traits()
                ->whereHas('traitType', function ($query) use ($traitType) {
                    $query->where('name', $traitType);
                })
                ->first();

            if ($trait && $trait->value) {
                return $trait->value;
            }
        }

        return null;
    }

    /**
     * Estrae la tecnica dai traits
     *
     * @param Egi $egi
     * @return string|null
     */
    protected function extractTechniqueFromTraits(Egi $egi): ?string {
        $techniqueTraitTypes = ['Tecnica', 'Technique', 'Materiale', 'Material', 'Medium'];

        foreach ($techniqueTraitTypes as $traitType) {
            $trait = $egi->traits()
                ->whereHas('traitType', function ($query) use ($traitType) {
                    $query->where('name', $traitType);
                })
                ->first();

            if ($trait && $trait->value) {
                return $trait->value;
            }
        }

        return null;
    }

    /**
     * Estrae il supporto dai traits
     *
     * @param Egi $egi
     * @return string|null
     */
    protected function extractSupportFromTraits(Egi $egi): ?string {
        $supportTraitTypes = ['Supporto', 'Support', 'Base', 'Substrate', 'Su'];

        foreach ($supportTraitTypes as $traitType) {
            $trait = $egi->traits()
                ->whereHas('traitType', function ($query) use ($traitType) {
                    $query->where('name', $traitType);
                })
                ->first();

            if ($trait && $trait->value) {
                return $trait->value;
            }
        }

        return null;
    }

    /**
     * Estrae le dimensioni dai traits
     *
     * @param Egi $egi
     * @return string|null
     */
    protected function extractDimensionsFromTraits(Egi $egi): ?string {
        $dimensionTraitTypes = ['Dimensioni', 'Dimensions', 'Size', 'Misure', 'Formato', 'Format'];

        foreach ($dimensionTraitTypes as $traitType) {
            $trait = $egi->traits()
                ->whereHas('traitType', function ($query) use ($traitType) {
                    $query->where('name', $traitType);
                })
                ->first();

            if ($trait && $trait->value) {
                return $trait->value;
            }
        }

        return null;
    }

    /**
     * Estrae l'edizione dai traits
     *
     * @param Egi $egi
     * @return string|null
     */
    protected function extractEditionFromTraits(Egi $egi): ?string {
        $editionTraitTypes = ['Edizione', 'Edition', 'Tiratura', 'Print run', 'Numero', 'Number'];

        foreach ($editionTraitTypes as $traitType) {
            $trait = $egi->traits()
                ->whereHas('traitType', function ($query) use ($traitType) {
                    $query->where('name', $traitType);
                })
                ->first();

            if ($trait && $trait->value) {
                return $trait->value;
            }
        }

        return null;
    }

    /**
     * Estrae informazioni sulla relazione dai traits
     *
     * @param Egi $egi
     * @return string|null
     */
    protected function extractRelationshipFromTraits(Egi $egi): ?string {
        $relationshipTraitTypes = [
            'Relazione',
            'Relationship',
            'Rapporto con autore',
            'Relation to author',
            'Ruolo',
            'Role',
            'Posizione',
            'Position'
        ];

        foreach ($relationshipTraitTypes as $traitType) {
            $trait = $egi->traits()
                ->whereHas('traitType', function ($query) use ($traitType) {
                    $query->where('name', $traitType);
                })
                ->first();

            if ($trait && $trait->value) {
                return $trait->value;
            }
        }

        return null;
    }

    /**
     * Controlla se ci sono autorizzazioni nei traits
     *
     * @param Egi $egi
     * @return bool
     */
    protected function hasAuthorizationInTraits(Egi $egi): bool {
        $authTraitTypes = [
            'Autorizzazione',
            'Authorization',
            'Delega',
            'Mandate',
            'Documento autorizzazione',
            'Authorization document',
            'Procura',
            'Power of attorney',
            'Contratto',
            'Contract',
            'Rappresentanza',
            'Representation'
        ];

        foreach ($authTraitTypes as $traitType) {
            $trait = $egi->traits()
                ->whereHas('traitType', function ($query) use ($traitType) {
                    $query->where('name', $traitType);
                })
                ->first();

            if ($trait) {
                return true;
            }
        }

        return false;
    }

    /**
     * Estrae tutti i metadati dell'artwork in formato strutturato
     *
     * @param Egi $egi
     * @return array
     */
    protected function extractAllArtworkMetadata(Egi $egi): array {
        $allTraits = [];

        // Carica tutti i traits con le relazioni
        $traits = $egi->traits()->with('traitType')->get();

        foreach ($traits as $trait) {
            if ($trait->value && trim($trait->value) !== '') {
                $allTraits[] = [
                    'trait_type' => $trait->traitType->name ?? 'Unknown',
                    'value' => $trait->value,
                    'display_value' => $trait->display_value ?? $trait->value,
                    'rarity_percentage' => $trait->rarity_percentage
                ];
            }
        }

        return $allTraits;
    }

    /**
     * Estrae un valore specifico dai traits per nomi multipli
     *
     * @param Egi $egi
     * @param array $traitNames
     * @return string|null
     */
    protected function extractTraitValue(Egi $egi, array $traitNames): ?string {
        foreach ($traitNames as $traitName) {
            $trait = $egi->traits()
                ->whereHas('traitType', function ($query) use ($traitName) {
                    $query->where('name', $traitName);
                })
                ->first();

            if ($trait && $trait->value) {
                return $trait->value;
            }
        }

        return null;
    }

    /**
     * Estrae la località di emissione dai dati personali dell'utente
     *
     * @param \App\Models\User $user
     * @return string|null
     */
    protected function extractIssueLocation(\App\Models\User $user): ?string {
        // Carica la relazione con i dati personali se non già caricata
        if (!$user->relationLoaded('personalData')) {
            $user->load('personalData');
        }

        $personalData = $user->personalData;

        if (!$personalData) {
            return null;
        }

        // Costruisci la località combinando città, regione e paese
        $locationParts = [];

        if (!empty($personalData->city)) {
            $locationParts[] = $personalData->city;
        }

        if (!empty($personalData->region) && $personalData->region !== $personalData->city) {
            $locationParts[] = $personalData->region;
        }

        if (!empty($personalData->country)) {
            // Se il paese è un codice a 2 lettere, convertilo in nome leggibile
            $countryName = $this->getCountryName($personalData->country);
            if ($countryName && !in_array($countryName, $locationParts)) {
                $locationParts[] = $countryName;
            }
        }

        return !empty($locationParts) ? implode(', ', $locationParts) : null;
    }

    /**
     * Converte un codice paese ISO in nome leggibile
     *
     * @param string $countryCode
     * @return string|null
     */
    private function getCountryName(string $countryCode): ?string {
        $countries = [
            'IT' => 'Italia',
            'US' => 'USA',
            'GB' => 'Regno Unito',
            'FR' => 'Francia',
            'DE' => 'Germania',
            'ES' => 'Spagna',
            'CH' => 'Svizzera',
            'AT' => 'Austria',
            'NL' => 'Paesi Bassi',
            'BE' => 'Belgio',
            'PT' => 'Portogallo',
            'GR' => 'Grecia',
            // Aggiungi altri paesi secondo necessità
        ];

        return $countries[strtoupper($countryCode)] ?? $countryCode;
    }
}
