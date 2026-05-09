<?php
// config/validator.php

class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Valide que le champ est requis
     */
    public function required(string $field, string $message = null): self
    {
        if (empty($this->data[$field])) {
            $this->errors[$field][] = $message ?? "Le champ $field est requis";
        }
        return $this;
    }

    /**
     * Valide un email
     */
    public function email(string $field, string $message = null): self
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = $message ?? "L'email n'est pas valide";
        }
        return $this;
    }

    /**
     * Valide la longueur minimale
     */
    public function minLength(string $field, int $min, string $message = null): self
    {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field][] = $message ?? "Le champ $field doit contenir au moins $min caractères";
        }
        return $this;
    }

    /**
     * Valide la longueur maximale
     */
    public function maxLength(string $field, int $max, string $message = null): self
    {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field][] = $message ?? "Le champ $field ne doit pas dépasser $max caractères";
        }
        return $this;
    }

    /**
     * Valide un numéro de téléphone
     */
    public function phone(string $field, string $message = null): self
    {
        if (!empty($this->data[$field]) && !preg_match('/^[0-9+\-\s()]+$/', $this->data[$field])) {
            $this->errors[$field][] = $message ?? "Le numéro de téléphone n'est pas valide";
        }
        return $this;
    }

    /**
     * Valide une date
     */
    public function date(string $field, string $format = 'Y-m-d', string $message = null): self
    {
        if (!empty($this->data[$field])) {
            $d = DateTime::createFromFormat($format, $this->data[$field]);
            if (!$d || $d->format($format) !== $this->data[$field]) {
                $this->errors[$field][] = $message ?? "La date n'est pas valide";
            }
        }
        return $this;
    }

    /**
     * Valide que la valeur est dans une liste
     */
    public function in(string $field, array $values, string $message = null): self
    {
        if (!empty($this->data[$field]) && !in_array($this->data[$field], $values)) {
            $this->errors[$field][] = $message ?? "La valeur du champ $field n'est pas valide";
        }
        return $this;
    }

    /**
     * Valide un fichier uploadé
     */
    public function file(string $field, array $allowedExtensions = [], int $maxSize = 5242880): self
    {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
                $this->errors[$field][] = "Erreur lors de l'upload du fichier";
                return $this;
            }

            if ($_FILES[$field]['size'] > $maxSize) {
                $this->errors[$field][] = "Le fichier est trop volumineux (max: " . ($maxSize / 1024 / 1024) . "MB)";
            }

            if (!empty($allowedExtensions)) {
                $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExtensions)) {
                    $this->errors[$field][] = "Extension de fichier non autorisée. Autorisées: " . implode(', ', $allowedExtensions);
                }
            }
        }
        return $this;
    }

    /**
     * Vérifie si la validation a réussi
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Retourne les erreurs
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retourne la première erreur pour un champ
     */
    public function getFirstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Sanitize une chaîne de caractères
     */
    public static function sanitize(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize un tableau de données
     */
    public static function sanitizeArray(array $data): array
    {
        return array_map(function($value) {
            return is_string($value) ? self::sanitize($value) : $value;
        }, $data);
    }
}
