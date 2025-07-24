<?php


use classes\DecorationDAO;
use classes\DignitaireDAO;
use classes\VilleDAO;

require_once __DIR__ . '/../config/database.php';
$pdo = getDatabaseConnection();

require_once __DIR__ . '/../classes/DignitaireDAO.class.php';
require_once __DIR__ . '/../classes/Dignitaire.class.php';
require_once __DIR__ . '/../classes/Dignitaire.class.php';


class DignitaireController
{
    public function index(): void
    {
        $this->afficherListe();

    }

    public function afficherListe(): void
    {
        require_once __DIR__ . '/../config/database.php';
        require_once __DIR__ . '/../classes/PosteDAO.class.php';
        require_once __DIR__ . '/../classes/DecorationDAO.class.php';
        require_once __DIR__ . '/../classes/VilleDAO.class.php';
        $pdo = getDatabaseConnection();

        // Mode d’affichage
        $display_mode = isset($_GET['mode']) && $_GET['mode'] === 'liste' ? 'liste' : 'grille';

        // Filtres
        $search     = $_GET['search'] ?? '';      // AJOUT IMPORTANT
        $annee_min  = $_GET['annee_min']  ?? '';
        $annee_max  = $_GET['annee_max']  ?? '';
        $ville_id   = $_GET['ville_id']   ?? '';
        $entite_id  = $_GET['entite_id']  ?? '';
        $genre      = $_GET['genre']      ?? '';

        // Récupère villes et entités
        $villes = $pdo->query("SELECT id, nom FROM ville ORDER BY nom")->fetchAll(\PDO::FETCH_ASSOC);
        $entites = $pdo->query("SELECT id, nom FROM entite ORDER BY nom")->fetchAll(\PDO::FETCH_ASSOC);

        if ($display_mode == 'liste') {
            $dao = new DignitaireDAO();  // ← OBLIGATOIRE
            $dignitaires = $dao->findAll();
            $totalDignitaires = $dao->countAll();
            // Mode LISTE (tableau associatif)
            $sql = "SELECT DISTINCT 
            d.id, d.nom, d.prenom, d.matricule, d.date_naissance, d.genre, 
            v.nom AS lieu_naissance, 
            p.intitule AS poste_actuel,
            e.nom AS nom_entite,
            vp.nom AS ville_poste
        FROM dignitaire d 
        LEFT JOIN ville v ON d.lieu_naissance = v.id 
        LEFT JOIN postes p ON d.id = p.dignitaire_id 
            AND (p.date_fin IS NULL OR p.date_fin >= CURDATE()) 
        LEFT JOIN entite e ON p.entite_id = e.id
        LEFT JOIN ville vp ON p.ville_id = vp.id
        WHERE 1=1";
            $params = [];
            if (!empty($search)) {
                $sql .= " AND (d.nom LIKE :search OR d.prenom LIKE :search OR d.matricule LIKE :search)";
                $params[':search'] = "%$search%";
            }
            $params = array_combine(array_map('trim', array_keys($params)), array_map('trim', $params)); // <-- Ajoute cette ligne juste avant le execute !

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            if ($annee_min !== '') {
                $sql .= " AND YEAR(d.date_naissance) >= :annee_min";
                $params[':annee_min'] = $annee_min;
            }
            if ($annee_max !== '') {
                $sql .= " AND YEAR(d.date_naissance) <= :annee_max";
                $params[':annee_max'] = $annee_max;
            }
            if ($ville_id !== '') {
                $sql .= " AND vp.id = :ville_id";
                $params[':ville_id'] = $ville_id;
            }
            if ($entite_id !== '') {
                $sql .= " AND p.entite_id = :entite_id";
                $params[':entite_id'] = $entite_id;
            }
            if ($genre !== '') {
                $sql .= " AND d.genre = :genre";
                $params[':genre'] = $genre;
            }

            $sql .= " ORDER BY d.nom, d.prenom";
            $stmt = $pdo->prepare($sql);
            echo "<pre>";
            echo "SQL: $sql\n";
            print_r($params);
            echo "</pre>";
            foreach ($params as $k => $v) {
                echo "Param: >$k< => >$v< <br>";
            }


            $stmt->execute($params);
            $dignitaires = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            // Mode GRILLE/DASHBOARD (objet Dignitaire + données liées)
            $dao = new DignitaireDAO();
            $dignitaires = $dao->findAll();
            foreach ($dignitaires as $d) {
                $d->diplomes = $dao->findDiplomesByDignitaireId($d->getId());
                $d->enfants  = $dao->findEnfantsByDignitaireId($d->getId());
                $d->postes   = $dao->findPostesByDignitaireId_2($d->getId());
            }
        }

        // Récupérer les totaux pour les stats dashboard
$totalDignitaires = $dao->countAll();
        // Tu peux utiliser les autres DAO comme plus haut


        $posteDAO = new PosteDAO();
        $decorationDAO = new DecorationDAO();
        $villeDAO = new VilleDAO();

        $totalPostes = $posteDAO->countAll();
        $totalDecorations = $decorationDAO->countAll();
        $totalVilles = $villeDAO->countAll();
        require __DIR__ . '/../views/dashboard_dignitaire.view.php';
    }




    public function afficherDetail($id) {
        $dao = new DignitaireDAO();
        $dignitaire = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_dignitaire.view.php';
    }

    public function afficherFormulaireAjout() {
        require __DIR__ . '/../views/dashboard_dignitaire.view.php';
    }

    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // On récupère les noms des fichiers
            $photo      = $_FILES['photo']['name'];
            $casier     = $_FILES['casier_judiciaire']['name'];
            $certificat = $_FILES['certificats_medicaux']['name'];

            // On définit les chemins de destination
            $chemin_photo      = 'uploads/photos/' . $photo;
            $chemin_casier     = 'uploads/casiers/' . $casier;
            $chemin_certificat = 'uploads/certificats/' . $certificat;

            // On déplace les fichiers vers leurs dossiers respectifs
            move_uploaded_file($_FILES['photo']['tmp_name'], $chemin_photo);
            move_uploaded_file($_FILES['casier_judiciaire']['tmp_name'], $chemin_casier);
            move_uploaded_file($_FILES['certificats_medicaux']['tmp_name'], $chemin_certificat);

            // Création de l'objet dignitaire (mapping strict à la classe)
            $dignitaire = new Dignitaire(
                null,
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['date_naissance'],
                $_POST['lieu_naissance'],
                $_POST['nationalite'],
                $_POST['genre'],
                $_POST['etat_civil'],
                $_POST['tel'],
                $_POST['adresse'],
                $_POST['nip'],
                $_POST['matricule'],
                $photo,               // uniquement le nom du fichier
                $casier,
                $certificat
            );

            // Enregistrement en base
            $dao = new DignitaireDAO();
            $dao->create($dignitaire);
            header('Location: index.php?controller=dignitaire&action=afficherListe');
            exit;
        }
    }

    public function afficherFormulaireModification($id) {
        $dao = new DignitaireDAO();
        $dignitaire = $dao->findById($id);
        require __DIR__ . '/../views/dashboard_dignitaire.view.php';
    }

    public function modifier($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Gestion des fichiers (mise à jour, si l'utilisateur a uploadé un nouveau fichier)
            $photo      = $_FILES['photo']['name'] ? $_FILES['photo']['name'] : $_POST['photo_old'];
            $casier     = $_FILES['casier_judiciaire']['name'] ? $_FILES['casier_judiciaire']['name'] : $_POST['casier_judiciaire_old'];
            $certificat = $_FILES['certificats_medicaux']['name'] ? $_FILES['certificats_medicaux']['name'] : $_POST['certificats_medicaux_old'];

            if ($_FILES['photo']['tmp_name']) {
                $chemin_photo = 'uploads/photos/' . $photo;
                move_uploaded_file($_FILES['photo']['tmp_name'], $chemin_photo);
            }
            if ($_FILES['casier_judiciaire']['tmp_name']) {
                $chemin_casier = 'uploads/casiers/' . $casier;
                move_uploaded_file($_FILES['casier_judiciaire']['tmp_name'], $chemin_casier);
            }
            if ($_FILES['certificats_medicaux']['tmp_name']) {
                $chemin_certificat = 'uploads/certificats/' . $certificat;
                move_uploaded_file($_FILES['certificats_medicaux']['tmp_name'], $chemin_certificat);
            }

            // Création de l'objet dignitaire à jour
            $dignitaire = new Dignitaire(
                null,
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['date_naissance'],
                $_POST['lieu_naissance'],
                $_POST['nationalite'],
                $_POST['genre'],
                $_POST['etat_civil'],
                $_POST['tel'],
                $_POST['adresse'],
                $_POST['nip'],
                $_POST['matricule'],
                $photo,               // uniquement le nom du fichier
                $casier,
                $certificat
            );
            $dao = new DignitaireDAO();
            $dao->update($dignitaire);
            header('Location: index.php?controller=dignitaire&action=afficherListe');
            exit;
        }
    }

    public function supprimer($id) {
        $dao = new DignitaireDAO();
        $dao->delete($id);
        header('Location: index.php?controller=dignitaire&action=afficherListe');
        exit;
    }



}
