<?php
// config/upload.php

class FileUploader
{
    private const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const ALLOWED_DOCUMENT_TYPES = ['pdf', 'doc', 'docx'];
    private const MAX_FILE_SIZE = 5242880; // 5MB
    
    private string $uploadDir;
    private array $allowedTypes;
    private int $maxSize;

    public function __construct(
        string $uploadDir = 'uploads/',
        array $allowedTypes = null,
        int $maxSize = self::MAX_FILE_SIZE
    ) {
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        $this->allowedTypes = $allowedTypes ?? self::ALLOWED_IMAGE_TYPES;
        $this->maxSize = $maxSize;

        // Créer le dossier s'il n'existe pas
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        // Créer un fichier .htaccess pour sécuriser le dossier
        $this->createHtaccess();
    }

    /**
     * Upload un fichier de manière sécurisée
     */
    public function upload(array $file): array
    {
        // Vérifications de base
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'error' => 'Paramètres invalides'];
        }

        // Vérifier les erreurs d'upload
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['success' => false, 'error' => 'Le fichier est trop volumineux'];
            case UPLOAD_ERR_NO_FILE:
                return ['success' => false, 'error' => 'Aucun fichier uploadé'];
            default:
                return ['success' => false, 'error' => 'Erreur inconnue lors de l\'upload'];
        }

        // Vérifier la taille
        if ($file['size'] > $this->maxSize) {
            return ['success' => false, 'error' => 'Le fichier dépasse la taille maximale autorisée'];
        }

        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedTypes, true)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé'];
        }

        // Vérifier le type MIME réel
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        $allowedMimes = $this->getAllowedMimeTypes();
        if (!in_array($mimeType, $allowedMimes, true)) {
            return ['success' => false, 'error' => 'Type MIME non autorisé'];
        }

        // Générer un nom de fichier sécurisé et unique
        $filename = $this->generateSecureFilename($extension);
        $filepath = $this->uploadDir . $filename;

        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'error' => 'Échec du déplacement du fichier'];
        }

        // Définir les permissions appropriées
        chmod($filepath, 0644);

        getLogger()->info("Fichier uploadé avec succès", [
            'filename' => $filename,
            'original_name' => $file['name'],
            'size' => $file['size']
        ]);

        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'url' => $this->uploadDir . $filename
        ];
    }

    /**
     * Génère un nom de fichier sécurisé
     */
    private function generateSecureFilename(string $extension): string
    {
        return bin2hex(random_bytes(16)) . '_' . time() . '.' . $extension;
    }

    /**
     * Retourne les types MIME autorisés
     */
    private function getAllowedMimeTypes(): array
    {
        $mimeMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        $allowed = [];
        foreach ($this->allowedTypes as $ext) {
            if (isset($mimeMap[$ext])) {
                $allowed[] = $mimeMap[$ext];
            }
        }

        return $allowed;
    }

    /**
     * Crée un fichier .htaccess pour sécuriser le dossier
     */
    private function createHtaccess(): void
    {
        $htaccessPath = $this->uploadDir . '.htaccess';
        
        if (!file_exists($htaccessPath)) {
            $content = <<<HTACCESS
# Interdire l'exécution de scripts
<FilesMatch "\.(php|php3|php4|php5|phtml|pl|py|jsp|asp|sh|cgi)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Autoriser uniquement certains types de fichiers
<FilesMatch "\.(jpg|jpeg|png|gif|webp|pdf|doc|docx)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>
HTACCESS;
            file_put_contents($htaccessPath, $content);
        }
    }

    /**
     * Supprime un fichier uploadé
     */
    public function delete(string $filename): bool
    {
        $filepath = $this->uploadDir . basename($filename);
        
        if (file_exists($filepath) && is_file($filepath)) {
            if (unlink($filepath)) {
                getLogger()->info("Fichier supprimé", ['filename' => $filename]);
                return true;
            }
        }
        
        return false;
    }
}
