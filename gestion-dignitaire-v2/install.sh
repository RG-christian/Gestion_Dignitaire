#!/bin/bash

echo "🚀 Installation automatique - Gestion Dignitaires v2"
echo "=================================================="

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Vérifier les prérequis
print_info "Vérification des prérequis..."

if ! command -v php &> /dev/null; then
    print_error "PHP n'est pas installé"
    exit 1
fi
print_success "PHP installé"

if ! command -v composer &> /dev/null; then
    print_error "Composer n'est pas installé"
    exit 1
fi
print_success "Composer installé"

if ! command -v node &> /dev/null; then
    print_error "Node.js n'est pas installé"
    exit 1
fi
print_success "Node.js installé"

if ! command -v npm &> /dev/null; then
    print_error "npm n'est pas installé"
    exit 1
fi
print_success "npm installé"

echo ""
print_info "Installation du Backend Laravel..."
echo "=================================================="

cd backend

# Vérifier si Laravel est déjà installé
if [ ! -f "artisan" ]; then
    print_info "Installation de Laravel..."
    composer create-project laravel/laravel . "^10.0"
else
    print_info "Laravel déjà installé, installation des dépendances..."
    composer install
fi

# Configuration
if [ ! -f ".env" ]; then
    print_info "Copie du fichier .env..."
    cp .env.example .env
    php artisan key:generate
    print_success "Fichier .env créé"
fi

# Demander les informations de base de données
echo ""
print_info "Configuration de la base de données"
read -p "Nom de la base de données [gestion_dignitaire_v2]: " db_name
db_name=${db_name:-gestion_dignitaire_v2}

read -p "Utilisateur MySQL [root]: " db_user
db_user=${db_user:-root}

read -sp "Mot de passe MySQL: " db_pass
echo ""

# Mettre à jour le fichier .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$db_pass/" .env

print_success "Configuration de la base de données mise à jour"

# Créer la base de données
print_info "Création de la base de données..."
mysql -u "$db_user" -p"$db_pass" -e "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
print_success "Base de données créée"

# Installer Sanctum
print_info "Installation de Laravel Sanctum..."
php artisan install:api --quiet
print_success "Sanctum installé"

# Exécuter les migrations
print_info "Exécution des migrations..."
php artisan migrate --force
print_success "Migrations exécutées"

# Créer un utilisateur admin
print_info "Création d'un utilisateur administrateur..."
php artisan tinker --execute="
\$user = new App\Models\User();
\$user->username = 'admin';
\$user->nom_complet = 'Administrateur';
\$user->email = 'admin@example.com';
\$user->password = bcrypt('password');
\$user->role_id = 1;
\$user->save();
echo 'Utilisateur créé: admin / password';
"
print_success "Utilisateur admin créé (admin / password)"

cd ..

echo ""
print_info "Installation du Frontend Nuxt..."
echo "=================================================="

cd frontend

# Installer les dépendances
print_info "Installation des dépendances npm..."
npm install

# Configuration
if [ ! -f ".env" ]; then
    print_info "Copie du fichier .env..."
    cp .env.example .env
    print_success "Fichier .env créé"
fi

cd ..

echo ""
echo "=================================================="
print_success "Installation terminée avec succès!"
echo "=================================================="
echo ""
echo "📝 Prochaines étapes:"
echo ""
echo "1. Backend Laravel:"
echo "   cd backend"
echo "   php artisan serve"
echo "   → API disponible sur http://localhost:8000"
echo ""
echo "2. Frontend Nuxt (dans un autre terminal):"
echo "   cd frontend"
echo "   npm run dev"
echo "   → Application disponible sur http://localhost:3000"
echo ""
echo "3. Connexion:"
echo "   Utilisateur: admin"
echo "   Mot de passe: password"
echo ""
print_info "Consultez INSTALLATION.md pour plus de détails"
