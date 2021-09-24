<?php

namespace App\Maison;

use App\Models\User;
use Illuminate\Http\Request; // Important pour accéder à Request!
use DB;
use Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Log;

/**
 * Assure la compatibilité des transferts entre Siel (exports csv) et Thesix
 *
 * @author lsd
 */
class Siel {
    
    public $formats = array(
        "spec" => "AA_SCOL_ID;NS_ETAB_ID;NS_IMPL_ID;NS_ENFT_ID;NB_ENFT_CD_ID;CO_SEXE;DT_NAIS;TE_NOM_PAYS_NAISSANCE;TE_LIEU_NAISSANCE;CO_ETCV;TE_NOM;TE_PRENOM;TE_PRENOM_AUTR;TE_ADRS_RUE;NO_ADRS;NO_ADRS_BTE;CO_ADRS_CP;TE_NOM_COM;TE_NOM_PAYS;TE_NATIONALITE;NO_REGNAT;CO_UNI_ID;CO_AA_ETUDE;DT_INSCRIPTION;DT_PRISE_EFFET;DT_PRESENCE_PHYSIQUE;TE_CLASSE; ;TE_RESP1_NOM;TE_RESP1_PRENOM;TE_RESP1_TITRE;TE_RESP1_TEL1;TE_RESP1_TEL2;TE_RESP1_TEL3;TE_RESP1_EMAIL;TE_RESP2_NOM;TE_RESP2_PRENOM;TE_RESP2_TITRE;TE_RESP2_TEL1;TE_RESP2_TEL2;TE_RESP2_TEL3;TE_RESP2_EMAIL;CO_INDEX;N/A;CO_COURS_PHILO;SW_EXEMPTION_PHILO;N/A;DT_EXEMPTION_LMI;CO_RAISON_EXEMPTION_LMI;CO_LMI;CO_LMII;CO_LMIII;CO_LMI_OBG;CO_LMII_OBG;CO_LMIII_OBG;SW_INTERNE;NS_ETAB_INTERNE;DT_EXCLUSION;NS_ETAB_EXCLUSION;N/A;DT_INTEGRATION;CO_INTEGRATION;N/A;N/A;CO_AA_ETUDE_CEB;N/A;DT_ANJ;SW_EQI;DT_EQI_DEMANDE;DT_EQI_OCTROI;SW_DOSSIER_INCOMPLET;DT_ADM_EN_ORDRE;SW_DIS_A_PAYER;DT_DIS_PAIEMENT;SW_DIS_EXEMPTION;DT_DIS_EXEMPTION;N/A;SW_CEFA_DEPUIS_3009;SW_ART45;SW_ART49;DT_DEBUT_MOD_PREP;DT_FIN_MOD_PREP;DT_DEBUT_CEFA;DT_FIN_CEFA;NS_ETAB_CEFA;CO_HEBERGE;CO_PEDADAPT;CO_IMMERSION;NB_PER_IMMERSION;SW_ATTEST_CPMS;DT_REC_ATTEST_CPMS;SW_EAD;CO_SSAS;DT_DEBUT_SAS;DT_FIN_SAS;DT_CE2D;DT_CQ;DT_CQ2;DT_CESS;CO_FIN",
        "ordi" => "FASE ecole;FASE implantation;Eleve;Nom;Prenom;Sexe;Date nais(AAAMMJJ);Date nais(JJ/MM/AAAA);Lieu de naissance;Pays de naissance;Nationalite;N° RN;Adresse legale;N°;Code postal;Commune;Date d'inscription;Annee d'etude;Classe;Cours de langue;Cours philosophique;Titre responsable 1;Nom responsable 1;Prenom responsable 1;Sexe responsable 1;Pays de naissance responsable 1;Tel 1 responsable 1;Tel 2 responsable 1;E-mail responsable 1;Adresse legale resp 1;N° resp 1;Code postal resp 1;Commune resp 1;Titre responsable 2;Nom responsable 2;Prenom responsable 2;Sexe responsable 2;Pays de naissance responsable 2;Tel 1 responsable 2;Tel 2 responsable 2;E-mail responsable 2;Adresse legale resp 2;N° resp 2;Code postal resp 2;Commune resp 2;Langue maternelle;Date entrée territoire;Date 1ère insc CF;Nationalité 1ère Insc CF;Réfugié;Interne;FASE internat;Immersion;Langue immersion;Périodes immersion;Inscrit suite exclusion;Date exclusion;FASE école exclusion;Intégration;Type intégration;Date intégration;8 demi-jours prés;Maintiren maternelle;Elève placé;Elève décompté;Motif décompte;9 demi-jours absence;Date 9 demi-jours absence;Maintien P8;Maintien P9;Avancement;Coef ALE;DASPA;#",
        "secordi" => "AA_SCOL_ID;NS_ETAB_ID;NS_IMPL_ID;NS_ENFT_ID;NB_ENFT_CD_ID;CO_SEXE;DT_NAIS;PAYS_NAISSANCE;LIEU_NAISSANCE;CO_ETCV;TE_NOM;TE_PRENOM;TE_PRENOM_AUTR;TE_ADRS_RUE;NO_ADRS;NO_ADRS_BTE;CO_ADRS_CP;TE_NOM_COM;TE_NOM_PAYS;TE_NATIONALITE;NO_REGNAT;CO_UNI_ID;CO_ANNEE_ETUDE;DATE_INSCRIPTION;DATE_PRISE_EFFET;DATE_PRESENCE_PHYSIQUE;TE_CLASSE;TE_SOUSCLASSE;RESP1_NOM;RESP1_PRENOM;RESP1_TITRE;RESP1_TEL1;RESP1_TEL2;RESP1_TEL3;RESP1_EMAIL;RESP2_NOM;RESP2_PRENOM;RESP2_TITRE;RESP2_TEL1;RESP2_TEL2;RESP2_TEL3;RESP2_EMAIL;PRIMO_ARRIVANT;DATE_ARRIVEE;DATE_PREM_INSCR;NATIONALITE_ARRIVEE;REFUGIE;GRILLE;DATE_CHANGEMENT_GRILLE;DEROGATION_CHANGEMENT_TARDIF;COURS_PHILO;DATE_CHANGEMENT_COURS_PHILO;EXEMPTION_LMI;DATE_EXEMPTION_LMI;RAISON_EXEMPTION_LMI;LMI;LMII;LMIII;LMI_OBG;LMII_OBG;LMIII_OBG;INTERNE;DATE_ENTREE_INTERNAT;NUMERO_FASE_INTERNAT;INSCRIT_SUITE_A_EXCLUSION;DATE_EXCLUSION;NUMERO_FASE_ETAB_EXCLUSION;INTEGRATION_DU_SPECIALISE;DATE_INTEGRATION_DU_SPECILIALISE;TYPE_INTEGRATION_DU_SPCECIALISE;OBTENU_CEB;ANNEE_ETUDE_CEB;ANNE_SCOLAIRE_CEB;ISSU_ANNEE_DIFFERENCIE;ANNEE_ETUDE_DIFFERENCIE;TRANSPORT_SCOLAIRE;SOCIETE_TRANSPORT;REPAS;PRIMO_ARRIVANT;DATE_ADMISSIBILITE;ANNE_ETUDE_ADMISSIBILITE;CE2D;DELIVRANCE_CE2D;CQ;DELIVRANCE_CQ;CQ2;DELIVRANCE_CQ2;CESS;DELIVRANCE_CESS;ESI;DATE_DEBUT_ESI;ECHANGE_AUTORISE;DATE_DEROGATION_562;ITA;DATE_ARRIVEE;DATE_DEMANDE_DEROGATION;DATE_OCTROI_DEROGATION;ANJ;DATE_PERTE_STATUT_REGULIER;DATE_DEMANDE_RECOUVREMENT;DATE_OCTROI_STATUT;EQI;DATE_DEMANDE_EQUIVALENCE;DATE_OCTROI_EQUIVALENCE;DOSSER_ADMISSION_PAS_EN_ORDRE;DATE_DOSSIER_EN_ORDRE;DROIT_SPECIFIQUE_A_PAYER;DATE_PAIEMENT;EXEMPTION_PAIEMENT;DATE_EXEMPTION;ESPOIR_SPORTIF;JEUNE_TALENT;CEFA;CEFA_DEPUIS_PREMIER_OCTOBRE;CEFA_PDT_SES_18_ANS;TYPE_CONTRAT_CEFA;NB_PER_FORMATION;NB_HEURES_STAGE;DATE_DEBUT_STAGE;DATE_FIN_STAGE;RESP1_TE_ADRS_RUE;RESP1_NO_ADRS;RESP1_NO_ADRS_BTE;RESP1_CO_ADRS_CP;RESP1_TE_NOM_COM;RESP1_TE_NOM_PAYS;RESP2_TE_ADRS_RUE;RESP2_NO_ADRS;RESP2_NO_ADRS_BTE;RESP2_CO_ADRS_CP;RESP2_TE_NOM_COM;RESP2_TE_NOM_PAYS",
        "secordi190914" => "AA_SCOL_ID;NS_ETAB_ID;NS_IMPL_ID;NS_ENFT_ID;NB_ENFT_CD_ID;CO_SEXE;DT_NAIS;PAYS_NAISSANCE;LIEU_NAISSANCE;CO_ETCV;TE_NOM;TE_PRENOM;TE_PRENOM_AUTR;TE_ADRS_RUE;NO_ADRS;NO_ADRS_BTE;CO_ADRS_CP;TE_NOM_COM;TE_NOM_PAYS;TE_NATIONALITE;NO_REGNAT;CO_UNI_ID;CO_ANNEE_ETUDE;DATE_INSCRIPTION;DATE_PRISE_EFFET;DATE_PRESENCE_PHYSIQUE;TE_CLASSE;TE_SOUSCLASSE;RESP1_NOM;RESP1_PRENOM;RESP1_TITRE;RESP1_TEL1;RESP1_TEL2;RESP1_TEL3;RESP1_EMAIL;RESP2_NOM;RESP2_PRENOM;RESP2_TITRE;RESP2_TEL1;RESP2_TEL2;RESP2_TEL3;RESP2_EMAIL;PRIMO_ARRIVANT;DATE_ARRIVEE;DATE_PREM_INSCR;NATIONALITE_ARRIVEE;REFUGIE;GRILLE;DATE_CHANGEMENT_GRILLE;DEROGATION_CHANGEMENT_TARDIF;COURS_PHILO;DATE_CHANGEMENT_COURS_PHILO;EXEMPTION_LMI;DATE_EXEMPTION_LMI;RAISON_EXEMPTION_LMI;LMI;LMII;LMIII;LMI_OBG;LMII_OBG;LMIII_OBG;INTERNE;DATE_ENTREE_INTERNAT;NUMERO_FASE_INTERNAT;INSCRIT_SUITE_A_EXCLUSION;DATE_EXCLUSION;NUMERO_FASE_ETAB_EXCLUSION;INTEGRATION_DU_SPECIALISE;DATE_INTEGRATION_DU_SPECILIALISE;TYPE_INTEGRATION_DU_SPCECIALISE;OBTENU_CEB;ANNEE_ETUDE_CEB;ANNE_SCOLAIRE_CEB;ISSU_ANNEE_DIFFERENCIE;ANNEE_ETUDE_DIFFERENCIE;TRANSPORT_SCOLAIRE;SOCIETE_TRANSPORT;REPAS;PRIMO_ARRIVANT;DATE_ADMISSIBILITE;ANNE_ETUDE_ADMISSIBILITE;CE2D;DELIVRANCE_CE2D;CQ;DELIVRANCE_CQ;CQ2;DELIVRANCE_CQ2;CESS;DELIVRANCE_CESS;ESI;DATE_DEBUT_ESI;ECHANGE_AUTORISE;DATE_DEROGATION_562;ITA;DATE_ARRIVEE;DATE_DEMANDE_DEROGATION;DATE_OCTROI_DEROGATION;ANJ;DATE_PERTE_STATUT_REGULIER;DATE_DEMANDE_RECOUVREMENT;DATE_OCTROI_STATUT;EQI;DATE_DEMANDE_EQUIVALENCE;DATE_OCTROI_EQUIVALENCE;DOSSER_ADMISSION_PAS_EN_ORDRE;DATE_DOSSIER_EN_ORDRE;DROIT_SPECIFIQUE_A_PAYER;DATE_PAIEMENT;EXEMPTION_PAIEMENT;DATE_EXEMPTION;ESPOIR_SPORTIF;JEUNE_TALENT;CEFA;CEFA_DEPUIS_PREMIER_OCTOBRE;CEFA_PDT_SES_18_ANS;TYPE_CONTRAT_CEFA;NB_PER_FORMATION;NB_HEURES_STAGE;DATE_DEBUT_STAGE;DATE_FIN_STAGE;RESP1_TE_ADRS_RUE;RESP1_NO_ADRS;RESP1_NO_ADRS_BTE;RESP1_CO_ADRS_CP;RESP1_TE_NOM_COM;RESP1_TE_NOM_PAYS;RESP2_TE_ADRS_RUE;RESP2_NO_ADRS;RESP2_NO_ADRS_BTE;RESP2_CO_ADRS_CP;RESP2_TE_NOM_COM;RESP2_TE_NOM_PAYS;SW_DASPA;CO_SOUTIEN_FR;DT_PRIMO_ARVT1;DT_PREM_INSCR1;SW_REFUGIE1;SW_BELGE_ADOPTION;DT_PASSATION_TEST;CO_PASSATION_TEST",
        "archenee" => "AA_SCOL_ID;NS_ETAB_ID;NS_IMPL_ID;NS_ENFT_ID;NB_ENFT_CD_ID;CO_SEXE;DT_NAIS;PAYS_NAISSANCE;LIEU_NAISSANCE;CO_ETCV;TE_NOM;TE_PRENOM;TE_PRENOM_AUTR;TE_ADRS_RUE;NO_ADRS;NO_ADRS_BTE;CO_ADRS_CP;TE_NOM_COM;TE_NOM_PAYS;TE_NATIONALITE;NO_REGNAT;CO_UNI_ID;CO_ANNEE_ETUDE;DATE_INSCRIPTION;DATE_PRISE_EFFET;DATE_PRESENCE_PHYSIQUE;TE_CLASSE;TE_SOUSCLASSE;RESP1_NOM;RESP1_PRENOM;RESP1_TITRE;RESP1_TEL1;RESP1_TEL2;RESP1_TEL3;RESP1_EMAIL;RESP2_NOM;RESP2_PRENOM;RESP2_TITRE;RESP2_TEL1;RESP2_TEL2;RESP2_TEL3;RESP2_EMAIL;PRIMO_ARRIVANT;DATE_ARRIVEE;DATE_PREM_INSCR;NATIONALITE_ARRIVEE;REFUGIE;GRILLE;DATE_CHANGEMENT_GRILLE;DEROGATION_CHANGEMENT_TARDIF;COURS_PHILO;DATE_CHANGEMENT_COURS_PHILO;EXEMPTION_LMI;DATE_EXEMPTION_LMI;RAISON_EXEMPTION_LMI;LMI;LMII;LMIII;LMI_OBG;LMII_OBG;LMIII_OBG;INTERNE;DATE_ENTREE_INTERNAT;NUMERO_FASE_INTERNAT;INSCRIT_SUITE_A_EXCLUSION;DATE_EXCLUSION;NUMERO_FASE_ETAB_EXCLUSION;INTEGRATION_DU_SPECIALISE;DATE_INTEGRATION_DU_SPECILIALISE;TYPE_INTEGRATION_DU_SPCECIALISE;OBTENU_CEB;ANNEE_ETUDE_CEB;ANNE_SCOLAIRE_CEB;ISSU_ANNEE_DIFFERENCIE;ANNEE_ETUDE_DIFFERENCIE;TRANSPORT_SCOLAIRE;SOCIETE_TRANSPORT;REPAS;PRIMO_ARRIVANT;DATE_ADMISSIBILITE;ANNE_ETUDE_ADMISSIBILITE;CE2D;DELIVRANCE_CE2D;CQ;DELIVRANCE_CQ;CQ2;DELIVRANCE_CQ2;CESS;DELIVRANCE_CESS;ESI;DATE_DEBUT_ESI;ECHANGE_AUTORISE;DATE_DEROGATION_562;ITA;DATE_ARRIVEE;DATE_DEMANDE_DEROGATION;DATE_OCTROI_DEROGATION;ANJ;DATE_PERTE_STATUT_REGULIER;DATE_DEMANDE_RECOUVREMENT;DATE_OCTROI_STATUT;EQI;DATE_DEMANDE_EQUIVALENCE;DATE_OCTROI_EQUIVALENCE;DOSSER_ADMISSION_PAS_EN_ORDRE;DATE_DOSSIER_EN_ORDRE;DROIT_SPECIFIQUE_A_PAYER;DATE_PAIEMENT;EXEMPTION_PAIEMENT;DATE_EXEMPTION;ESPOIR_SPORTIF;JEUNE_TALENT;CEFA;CEFA_DEPUIS_PREMIER_OCTOBRE;CEFA_PDT_SES_18_ANS;TYPE_CONTRAT_CEFA;NB_PER_FORMATION;NB_HEURES_STAGE;DATE_DEBUT_STAGE;DATE_FIN_STAGE;RESP1_TE_ADRS_RUE;RESP1_NO_ADRS;RESP1_NO_ADRS_BTE;RESP1_CO_ADRS_CP;RESP1_TE_NOM_COM;RESP1_TE_NOM_PAYS;RESP2_TE_ADRS_RUE;RESP2_NO_ADRS;RESP2_NO_ADRS_BTE;RESP2_CO_ADRS_CP;RESP2_TE_NOM_COM;RESP2_TE_NOM_PAYS;SW_DASPA;CO_SOUTIEN_FR;DT_PRIMO_ARVT1;DT_PREM_INSCR1;SW_REFUGIE1;SW_BELGE_ADOPTION;DT_PASSATION_TEST;CO_PASSATION_TEST;CE1D;DELIVRANCE_CE1D;CE6P;DELIVRANCE_CE6P",
        "ordi2019" => "FASE ecole;FASE implantation;Eleve;Nom;Prenom;Sexe;Date nais(AAAMMJJ);Date nais(JJ/MM/AAAA);Lieu de naissance;Pays de naissance;Nationalite;N° RN;Adresse legale;N°;Code postal;Commune;Date d'inscription;Annee d'etude;Classe;Cours de langue;Cours philosophique;Titre responsable 1;Nom responsable 1;Prenom responsable 1;Sexe responsable 1;Pays de naissance responsable 1;Tel 1 responsable 1;Tel 2 responsable 1;E-mail responsable 1;Adresse legale resp 1;N° resp 1;Code postal resp 1;Commune resp 1;Titre responsable 2;Nom responsable 2;Prenom responsable 2;Sexe responsable 2;Pays de naissance responsable 2;Tel 1 responsable 2;Tel 2 responsable 2;E-mail responsable 2;Adresse legale resp 2;N° resp 2;Code postal resp 2;Commune resp 2;Langue maternelle;Obsolete;Obsolete;Obsolete;Obsolete;Interne;FASE internat;Immersion;Langue immersion;Périodes immersion;Inscrit suite exclusion;Date exclusion;FASE école exclusion;Intégration;Type intégration;Date intégration;8 demi-jours prés;Maintien maternelle;Elève placé;Elève décompté;Motif décompte;9 demi-jours absence;Date 9 demi-jours absence;Maintien P8;Maintien P9;Avancement;Obsolete;DASPA;Soutien FR;Date entrée sur le territoire;Date 1ère inscription CF(ADMIN);Réfugié;Belge par adoption;Date passage test FR;Résultat Test;#",
    );
        
    public $schema, $conversion;
    
    public function __construct() {
        $this->verifieLeSchemaEnCache();
        $this->schema = Cache::get('schema_eleves');
        $this->conversion = Cache::get('conversion_eleves');
    }
    
    public function verifieLeSchemaEnCache() {
        $agedejson = Storage::lastModified('schema_eleves.json');
        $ageducache = Cache::get('agedejson');
        if ($agedejson != $ageducache) {
            $r = $this->chargeschema();
            Cache::forever('schema_eleves', $r['schema']);
            Cache::forever('conversion_eleves', $r['conversion']);
            Cache::forever('agedejson', $agedejson);
        }
    }
    
    /**
     * Lit le fichier /storage/app/schema_eleves.json converti d'un tableau excel
     * via le site : http://www.convertcsv.com/csv-to-json.htm
     * et retourne les données complètes (schema) et sous forme de table
     * de conversion pour importer les fichiers Siel (conversion)
     * @return array['schema', 'conversion']
     */
    public function chargeschema() {
        $json = Storage::get('schema_eleves.json');
        $schema = json_decode($json, true);
        foreach ($schema as $champ => $e) {
            foreach ($this->formats as $format => $v) {
                if (isset($e[$format])) {
                    $conversion[$format][$champ] = ['brol' => $e['brol'], 'idcsv' => $e[$format]];
                }
            }
        }
        return ['schema'=>$schema, 'conversion'=>$conversion];
    }

    /**
     * Méthode temporaire avant interface d'upload
     * @return type
     */
    public function testget() {
        $csv = Storage::get('public/test.csv');
        return $csv;
    }
    
    /**
     * Rapatrie le fichier csv
     * @param type $path
     * @param bool $effacefichierapresusage par défaut
     * @return string (csv)
     */
    public function get($path, $effacefichierapresusage = true) {
        $csv = Storage::get($path);
        if ($effacefichierapresusage) {
            Storage::delete($path);
        }
        return $csv;
    }
    
    /**
     * Détecte le type de csv population SIEL (primaire ordinaire, spécialisé, secondaire)
     * en fonction de l'entête
     * @param type $entete (première ligne du fichier csv)
     * @return type d'export siel (ordi/spec/secordi) ou FALSE si inconnu
     */
    public function detecte($entete) {
        $formats = $this->formats;
        foreach ($formats as $cle => $val) {
            if (utf8_encode($entete) == $val) {
                return $cle;
            }
        }
        return FALSE;
    }
    
    /**
     * Convertit le csv en collection (tableau associatif amélioré [objet])
     * @param type $csv
     * @return $tableau avec les champs référencés dans $this->conversion ou FALSE
     */
    public function importe($csv) {
        $separateur = "\r\n";
        $entete =  strtok($csv, $separateur);
        $format = $this->detecte($entete);
        if (!$format) {
            info('Format Siel non reconnu');
            return FALSE;
        }
        $classe = new \App\Models\Classe();
        $tableau = collect();
        $ligne = strtok($separateur);
        while ($ligne !== FALSE) {
            $ligne = str_getcsv(utf8_encode($ligne), ';');
            $eleve = [];
            foreach ($this->conversion[$format] as $champ => $v) {
                $pos = $v["idcsv"];
                if ($v['brol']) {
                    $eleve['brol'][$champ] = $ligne[$pos];
                } else {
                    $eleve[$champ] = $ligne[$pos];
                }
            }
            $eleve['classe_ref'] = $this->siel2ref($eleve);
            $tableau->push($eleve);
            $ligne = strtok($separateur);
        }
        return $tableau;
    }
    
    /**
     * Compare et trie les élèves SIEL vs DB
     * @param type $tableau
     * @return array [nouveaux[], modifs[], deletes[]]
     */
    public function old_parcourt($tableau) {
        $nouveaux = collect();
        $modifs = collect();
        $deletes = collect();
        // Parcourt le tableau importé
        foreach ($tableau as $tab_eleve) {
            // Élève existant ?
            if ($db_eleve = \App\Models\Eleve::where('matricule', $tab_eleve['matricule'])->first()) {
                // Classe modifiée ?
                if ($db_eleve->classe()->withTrashed()->first()->ref !== $tab_eleve['classe_ref']) {
                    $modifs->push($tab_eleve);
                }
            } else {
                // Élève inexistant
                $nouveaux->push($tab_eleve);
            }
        }
        // Parcourt les élèves en base de données (sauf les élèves déjà supprimés, avec statut & 8)
        foreach (\App\Models\Eleve::whereRaw('NOT statut & 8')->orWhere('statut', null)->get() as $db_eleve) {
            // L'élève n'est plus dans le tableau importé?
            if (! $tableau->where('matricule', $db_eleve->matricule)->first()) {
                $db_eleve->classe_ref = $db_eleve->classe()->withTrashed()->first()->ref;
                $deletes->push($db_eleve);
            }
        }
        return array(
            'nouveaux' => $nouveaux,
            'modifs' => $modifs,
            'deletes' => $deletes
        );
    }
    
    
    /**
     * Compare et trie les élèves SIEL vs DB
     * @param type $tableau
     * @return array [nouveaux[], modifs[], deletes[]]
     */
    public function parcourt($tableau) {
        $nouveaux = collect();
        $modifs = collect();
        $deletes = collect();
        $matricules = $tableau->pluck('matricule');
        $dbmatricules = \App\Models\Eleve::all()->pluck('matricule');
        $nouveauxmatricules = $matricules->diff($dbmatricules);
        $anciensmatricules = $dbmatricules->diff($matricules);
        $intermatricules = $dbmatricules->intersect($matricules);
        $nouveaux = $tableau->whereIn('matricule', $nouveauxmatricules)->values();
        $deletes = \App\Models\Eleve::whereIn('matricule', $anciensmatricules)->get();
        $dbacomparer = \App\Models\Eleve::whereIn('matricule', $intermatricules)->with('classe')->withTrashed()->get();
        $tabacomparer = $tableau->whereIn('matricule', $intermatricules);
        // classe.ref indexé par matricule, p. ex.: 5890826 => '1 D2 3 G ="H"'
        $dbclassesacomparer = $dbacomparer->pluck('classe.ref', 'matricule');
        $tabclassesacomparer = $tabacomparer->pluck('classe_ref', 'matricule');
        $classesacomparer = $dbclassesacomparer->diffAssoc($tabclassesacomparer);
        $modifs = $tabacomparer->whereIn('matricule', $classesacomparer->keys())->values();
        return array(
            'nouveaux' => $nouveaux,
            'modifs' => $modifs,
            'deletes' => $deletes
        );
    }
    
    /**
     * Assemble anneedetudes et te_classe du csv SIEL pour former la référence interne
     * @param type $eleve
     * @return string $ref
     */
    public function siel2ref($eleve) {
        return $eleve['brol']['anneedetudes'].' '.$eleve['brol']['te_classe'];
    }
    
    /**
     * Retourne classe où classe->ref correspond à [anneedetudes + ' ' + te_classe] de SIEL
     * @param type $eleve
     * @param type $format (spec/ordi/secordi) RÉSERVÉ À UN ÉVENTUEL USAGE FUTUR
     * @return \App\Models\Classe
     */
    public function siel2classe($eleve, $format='ordi') {
        $ref = $this->siel2ref($eleve);
        $classe = new \App\Models\Classe();
        $classe->findByRef($ref);
        return $classe;
    }
    
    /**
     * Liste toutes les classes->ref possibles d'après un csv SIEL
     * @param collection $tableau produit par $this->importe
     * @return array
     */
    public function classesuniques($tableau) {
        $refs = [];
        foreach ($tableau as $eleve) {
            if (!in_array($eleve['classe_ref'], $refs)) {
                $refs[] = $eleve['classe_ref'];
            }
        }
        sort($refs);
        return $refs;
    }
    
    /**
     * Compare et trie les classes SIEL vs DB
     * @param array $refs Liste des classes produite par classesuniques
     * @return array[nouvelles[], anciennes[], stables[]]
     */
    public function concordanceclasses($refs) {
        $classes = \App\Models\Classe::all();
        $tmpclasse = new \App\Models\Classe();
        $nouvelles = [];
        $anciennes = [];
        $stables = [];
        // Compare la liste SIEL à la DB (nouvelles classes)
        foreach ($refs as $ref) {
            if (! $c = $tmpclasse->findByRef($ref)) {
                $c = new \App\Models\Classe();
                $c->ref = $ref;
                $nouvelles[] = $c;
            } else {
                $stables[] = $c;
            }
        }
        // Compare la DB à la liste SIEL (anciennes classes inutilisées)
        foreach ($classes as $classe) {
            if (!in_array($classe->ref, $refs)) {
                $anciennes[] = $classe;
            }
        }
        return array(
            "nouvelles" => $nouvelles,
            "anciennes" => $anciennes,
            "stables" => $stables
        );
    }
    
    /**
     * Ajoute un champ ['checked' => $etat] à chaque ligne du tableau $table
     * @param array $table
     * @param boolean $etat
     * @return array $table
     */
    public function checkboxing($table, $etat=true) {
        foreach ($table as $cle => $valeur) {
            $table[$cle]['checked']=$etat;
        }
        return $table;
    }
    
    /**
     * Fabrique Eleve à partir d'un tableau $brut en s'assurant que seuls les champs
     * référencés dans le schéma sont enregistrés, et qu'ils existent bien
     * dans $brut. Sert par exemple à nettoyer les «checked» des formulaires.
     * Serialize brol, même si $brut était plat, et passe Carbon sur les dates.
     * @param array $brut
     * @return Eleve
     */
    public function dbilise($brut) {
        $e = new \App\Models\Eleve();
        $brol = [];
        foreach ($this->schema as $champ => $v) {
            if ($v['save']==1) {
                if ($v['brol']==1) {
                    if (isset($brut[$champ])) { // brol aplati dans brut
                        $brol[$champ]=$brut[$champ];
                    }
                    if (isset($brut['brol'][$champ])) { // brol déjà brolisé dans brut
                        $brol[$champ] = $brut['brol'][$champ];
                    }
                } else {
                    if (isset($brut[$champ])) {
                        if ($v['type'] == 'date d/m/Y') {
                            // ATTENTION DateTime attend une date américaine si elle contient des slashes (m/d/Y)
                            // Je ne sais pas comment j'ai importé d'autres dates avant sans avoir d'erreurs
                            //$this->$champ = \Carbon\Carbon::parse($brut[$champ]);
                            // Il faut spécifier le format à Carbon (qui appelle DateTime::__construct)
                            $this->$champ = \Carbon\Carbon::createFromFormat('d/m/Y', $brut[$champ]);
                        } else {
                            $e->$champ=$brut[$champ];
                        }
                    }
                }
            }
        }
        //$e->brol = serialize($brol);
        $e->brol = $brol;
        return $e;
    }
    
    /**
     * Fouille la base de données élèves et extrait toutes les adresses e-mail
     * des responsables 1 et 2. Associe chaque adresse avec le(s) élève(s) fils/fille(s)
     * @return array $emails
     */
    public function fouilleEmailResp() {
        $emails = [];
        $eleves = \App\Models\Eleve::all();
        foreach ($eleves as $e) {
            if (isset($e->email_r1)) {
                $emails[$e->email_r1]["eleve_id"][]=$e->id;
            }
            if (isset($e->email_r2)) {
                $emails[$e->email_r2]["eleve_id"][]=$e->id;
            }
        }
        return $emails;
    }
    
    /**
     * Passe en revue toutes les adresses e-mail des responsables des nouveaux élèves
     * et vérifie s'ils sont déjà inscrits, pour les avertir, confirmer leur parenté...
     * @param array $ids_nouveaux
     * @return array ids parents à notifier/confirmer
     */
    public function parentsNouveaux($ids_nouveaux) {
        $parentsAConfirmer = [];
        $emails = Cache::get('emails_responsables');
        foreach ($ids_nouveaux as $e_id) {
            foreach ($emails as $email => $tableaudelevesid) {
                foreach ($tableaudelevesid['eleve_id'] as $teid) {
                    if ($teid == $e_id) {
                        if ($user = \App\Models\User::findByEmail($email)) {
                            $parentsAConfirmer[]=$user->id;
                        }
                    }
                }
            }
        }
        return isset($parentsAConfirmer[0]) ? $parentsAConfirmer : null;
    }
    
    /**
     * Convertit tous les élèves brols sérialisés (php) en json
     * ATTENTION! Ne pas exécuter avec l'attribut $cast activé dans Eleve.php
     */
    public function jsonisebrol() {
        foreach (\App\Models\Eleve::withTrashed()->get() as $eleve) {
            $eleve->brol = json_encode(unserialize($eleve->brol));
            $eleve->save();
        }
    }
    
    /**
     * Réalise l'inverse de jsonisebrol
     */
    public function dejsonisebrol() {
        foreach (\App\Models\Eleve::withTrashed()->get() as $eleve) {
            $eleve->brol = serialize(json_decode($eleve->brol));
            $eleve->save();
        }
    }
    
    
}
