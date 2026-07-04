# Design Document - Affichage des Images dans les Quiz Côté Collaborateur

## Overview

Cette fonctionnalité améliore l'expérience utilisateur lors de la passation des quiz en garantissant que toutes les images configurées par les administrateurs s'affichent correctement côté collaborateur. Le design se concentre sur trois aspects principaux :

1. **Affichage des images d'énoncé** : Images illustratives pour les questions
2. **Affichage des images dans les types de questions spécifiques** : CHOIX_IMAGE et ASSOCIATION_IMAGE
3. **Gestion robuste des erreurs et optimisation des performances**

Le système utilise Vue.js 3 avec Composition API pour le frontend et s'intègre avec l'API backend Node.js/Express existante.

## Architecture

### Composants Principaux

```
QuizTakePage.vue (Vue existant)
├── QuizQuestionImage.vue (Nouveau composant)
│   ├── Affichage image d'énoncé
│   ├── Modale plein écran
│   └── Gestion des erreurs
├── QuizImageOptions.vue (Nouveau composant)
│   ├── Grille d'images pour CHOIX_IMAGE
│   └── Indicateurs de sélection
└── QuizImageAssociation.vue (Nouveau composant)
    ├── Colonnes A et B
    └── Sélection d'associations

Composables
├── useImageLoader.js (Nouveau)
│   ├── Préchargement d'images
│   ├── Cache d'images
│   └── Gestion des erreurs
└── useImageUrl.js (Nouveau)
    └── Construction d'URLs complètes
```

### Flux de Données

```
1. Chargement du Quiz
   QuizTakePage → quizService.getQuizById() → API Backend
   ↓
   Réception des questions avec données d'images
   ↓
2. Traitement des URLs
   useImageUrl.buildImageUrl(relativePath) → URL complète
   ↓
3. Préchargement
   useImageLoader.preloadImages(urls[]) → Promise<loaded[]>
   ↓
4. Affichage
   Composants d'images → Rendu avec fallback
```

## Components and Interfaces

### 1. QuizQuestionImage.vue

Composant réutilisable pour afficher une image d'énoncé de question.

```vue
<template>
  <div class="quiz-question-image">
    <div v-if="loading" class="image-loader">
      <Loader2 class="animate-spin" />
      <span>Chargement de l'image...</span>
    </div>
    
    <div v-else-if="error" class="image-error">
      <ImageOff class="w-8 h-8" />
      <span class="text-sm">Image non disponible</span>
    </div>
    
    <div v-else class="image-container" @click="openFullscreen">
      <img 
        :src="imageUrl" 
        :alt="altText"
        @load="onImageLoad"
        @error="onImageError"
        class="question-image"
      />
      <div class="image-overlay">
        <Maximize2 class="w-5 h-5" />
        <span>Cliquer pour agrandir</span>
      </div>
    </div>
    
    <!-- Modale plein écran -->
    <Teleport to="body">
      <div v-if="showFullscreen" class="fullscreen-modal" @click="closeFullscreen">
        <img :src="imageUrl" :alt="altText" class="fullscreen-image" />
        <button class="close-button" @click="closeFullscreen">
          <X class="w-6 h-6" />
        </button>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Loader2, ImageOff, Maximize2, X } from 'lucide-vue-next';
import { useImageUrl } from '@/composables/useImageUrl';
import { useImageLoader } from '@/composables/useImageLoader';

const props = defineProps({
  imagePath: {
    type: String,
    required: true
  },
  altText: {
    type: String,
    default: 'Image de la question'
  }
});

const { buildImageUrl } = useImageUrl();
const { loadImage } = useImageLoader();

const imageUrl = computed(() => buildImageUrl(props.imagePath));
const loading = ref(true);
const error = ref(false);
const showFullscreen = ref(false);

const onImageLoad = () => {
  loading.value = false;
  error.value = false;
};

const onImageError = () => {
  loading.value = false;
  error.value = true;
  console.error('Erreur chargement image:', imageUrl.value);
};

const openFullscreen = () => {
  showFullscreen.value = true;
};

const closeFullscreen = () => {
  showFullscreen.value = false;
};

// Précharger l'image
watch(() => props.imagePath, async (newPath) => {
  if (newPath) {
    loading.value = true;
    error.value = false;
    try {
      await loadImage(buildImageUrl(newPath));
      loading.value = false;
    } catch (err) {
      error.value = true;
      loading.value = false;
    }
  }
}, { immediate: true });
</script>
```

**Props:**
- `imagePath`: Chemin relatif ou absolu de l'image
- `altText`: Texte alternatif pour l'accessibilité

**Events:**
- Aucun (composant autonome)

**Styling:**
- Largeur maximale: 600px
- Hauteur: auto (préserve le ratio)
- Responsive: 100% sur mobile

### 2. QuizImageOptions.vue

Composant pour afficher les options d'images dans les questions CHOIX_IMAGE.

```vue
<template>
  <div class="quiz-image-options">
    <div v-if="allLoading" class="loading-state">
      <Loader2 class="animate-spin w-8 h-8" />
      <span>Chargement des options...</span>
    </div>
    
    <div v-else class="image-grid" :class="gridClass">
      <button
        v-for="(option, index) in options"
        :key="index"
        @click="selectOption(option)"
        :class="[
          'image-option',
          { 'selected': isSelected(option) },
          { 'error': imageErrors[index] }
        ]"
        :aria-label="`Option ${index + 1}: ${option.label || ''}`"
        :aria-pressed="isSelected(option)"
      >
        <div v-if="imageErrors[index]" class="option-error">
          <ImageOff class="w-8 h-8" />
          <span class="text-xs">{{ option.label || `Option ${index + 1}` }}</span>
        </div>
        
        <img
          v-else
          :src="buildImageUrl(option.url)"
          :alt="option.label || `Option ${index + 1}`"
          @error="() => handleImageError(index)"
          class="option-image"
        />
        
        <div v-if="isSelected(option)" class="selection-indicator">
          <Check class="w-8 h-8 text-white" :stroke-width="3" />
        </div>
        
        <div v-if="option.label" class="option-label">
          {{ option.label }}
        </div>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Loader2, ImageOff, Check } from 'lucide-vue-next';
import { useImageUrl } from '@/composables/useImageUrl';
import { useImageLoader } from '@/composables/useImageLoader';

const props = defineProps({
  options: {
    type: Array,
    required: true,
    validator: (options) => {
      return options.every(opt => opt.url);
    }
  },
  selectedValue: {
    type: String,
    default: null
  }
});

const emit = defineEmits(['select']);

const { buildImageUrl } = useImageUrl();
const { preloadImages } = useImageLoader();

const allLoading = ref(true);
const imageErrors = ref({});

const gridClass = computed(() => {
  const count = props.options.length;
  if (count <= 2) return 'grid-cols-2';
  if (count <= 4) return 'grid-cols-2 md:grid-cols-2';
  return 'grid-cols-2 md:grid-cols-3';
});

const isSelected = (option) => {
  return props.selectedValue === option.url;
};

const selectOption = (option) => {
  if (!imageErrors.value[props.options.indexOf(option)]) {
    emit('select', option.url);
  }
};

const handleImageError = (index) => {
  imageErrors.value[index] = true;
  console.error('Erreur chargement image option:', props.options[index].url);
};

onMounted(async () => {
  try {
    const urls = props.options.map(opt => buildImageUrl(opt.url));
    await preloadImages(urls);
  } catch (error) {
    console.error('Erreur préchargement images:', error);
  } finally {
    allLoading.value = false;
  }
});
</script>
```

**Props:**
- `options`: Array d'objets `{ url: string, label?: string }`
- `selectedValue`: URL de l'option sélectionnée

**Events:**
- `select`: Émis quand une option est sélectionnée, payload: `url`

**Styling:**
- Grille responsive (2-3 colonnes selon écran)
- Aspect ratio: square
- Bordure colorée pour sélection

### 3. QuizImageAssociation.vue

Composant pour les questions d'association d'images.

```vue
<template>
  <div class="quiz-image-association">
    <div v-if="allLoading" class="loading-state">
      <Loader2 class="animate-spin w-8 h-8" />
      <span>Chargement des images...</span>
    </div>
    
    <div v-else class="association-grid">
      <!-- Colonne A -->
      <div class="column-a">
        <h3 class="column-title">Images à associer</h3>
        <div class="image-list">
          <div
            v-for="(pair, index) in pairs"
            :key="'a-' + index"
            class="image-item"
          >
            <div class="image-wrapper">
              <img
                :src="buildImageUrl(pair.imageA)"
                :alt="`Image ${getLetter(index)}`"
                @error="() => handleImageError('A', index)"
                class="association-image"
              />
              <div class="image-badge">{{ getLetter(index) }}</div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Colonne B -->
      <div class="column-b">
        <h3 class="column-title">Réponses</h3>
        <div class="selection-list">
          <div
            v-for="(pair, index) in pairs"
            :key="'b-' + index"
            class="selection-item"
          >
            <div class="selection-label">
              Pour l'image {{ getLetter(index) }}
            </div>
            <div class="option-grid">
              <button
                v-for="(imageB, optIndex) in shuffledImagesB"
                :key="'opt-' + optIndex"
                @click="selectAssociation(index, imageB)"
                :class="[
                  'option-button',
                  { 'selected': isSelected(index, imageB) }
                ]"
                :aria-label="`Option ${optIndex + 1} pour ${getLetter(index)}`"
              >
                <img
                  :src="buildImageUrl(imageB)"
                  :alt="`Option ${optIndex + 1}`"
                  @error="() => handleImageError('B', optIndex)"
                  class="option-image"
                />
                <div v-if="isSelected(index, imageB)" class="check-overlay">
                  <Check class="w-6 h-6 text-white" :stroke-width="3" />
                </div>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Loader2, Check } from 'lucide-vue-next';
import { useImageUrl } from '@/composables/useImageUrl';
import { useImageLoader } from '@/composables/useImageLoader';

const props = defineProps({
  pairs: {
    type: Array,
    required: true,
    validator: (pairs) => {
      return pairs.every(p => p.imageA && p.imageB);
    }
  },
  selectedAssociations: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['update']);

const { buildImageUrl } = useImageUrl();
const { preloadImages } = useImageLoader();

const allLoading = ref(true);
const shuffledImagesB = ref([]);

const getLetter = (index) => {
  return String.fromCharCode(65 + index); // A, B, C, ...
};

const isSelected = (index, imageB) => {
  return props.selectedAssociations[index] === imageB;
};

const selectAssociation = (index, imageB) => {
  const updated = { ...props.selectedAssociations, [index]: imageB };
  emit('update', updated);
};

const handleImageError = (column, index) => {
  console.error(`Erreur chargement image ${column}[${index}]`);
};

const shuffleArray = (array) => {
  const shuffled = [...array];
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
  }
  return shuffled;
};

onMounted(async () => {
  // Mélanger les images B
  shuffledImagesB.value = shuffleArray(props.pairs.map(p => p.imageB));
  
  // Précharger toutes les images
  try {
    const allUrls = [
      ...props.pairs.map(p => buildImageUrl(p.imageA)),
      ...props.pairs.map(p => buildImageUrl(p.imageB))
    ];
    await preloadImages(allUrls);
  } catch (error) {
    console.error('Erreur préchargement images:', error);
  } finally {
    allLoading.value = false;
  }
});
</script>
```

**Props:**
- `pairs`: Array d'objets `{ imageA: string, imageB: string }`
- `selectedAssociations`: Objet `{ index: imageB_url }`

**Events:**
- `update`: Émis quand une association change, payload: objet complet des associations

## Data Models

### Question avec Image d'Énoncé

```typescript
interface Question {
  id: string;
  quizId: string;
  typeQuestion: TypeQuestion;
  question: string;
  imageQuestion?: string; // Chemin relatif ou URL
  options: string[] | ImageOption[] | MatchPair[];
  reponsesCorrectes: any;
  ordre: number;
  points: number;
  explication?: string;
}
```

### Options d'Images (CHOIX_IMAGE)

```typescript
interface ImageOption {
  url: string;      // Chemin de l'image
  label?: string;   // Label optionnel
}

// Dans la question
interface QuestionChoixImage extends Question {
  typeQuestion: 'CHOIX_IMAGE';
  optionsImages: ImageOption[];
}
```

### Paires d'Images (ASSOCIATION_IMAGE)

```typescript
interface ImagePair {
  imageA: string;  // Image à associer
  imageB: string;  // Image réponse
}

// Dans la question
interface QuestionAssociationImage extends Question {
  typeQuestion: 'ASSOCIATION_IMAGE';
  pairesImages: ImagePair[];
}
```

## Composables

### useImageUrl.js

Gère la construction des URLs d'images complètes.

```javascript
import { computed } from 'vue';

export function useImageUrl() {
  const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:5000';
  
  /**
   * Construit une URL complète pour une image
   * @param {string} imagePath - Chemin relatif ou URL complète
   * @returns {string} URL complète de l'image
   */
  const buildImageUrl = (imagePath) => {
    if (!imagePath) return '';
    
    // Si c'est déjà une URL complète
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
      return imagePath;
    }
    
    // Si c'est un chemin relatif
    const cleanPath = imagePath.startsWith('/') ? imagePath : `/${imagePath}`;
    return `${API_BASE_URL}${cleanPath}`;
  };
  
  /**
   * Valide qu'une URL d'image est sécurisée
   * @param {string} url - URL à valider
   * @returns {boolean} true si l'URL est valide
   */
  const isValidImageUrl = (url) => {
    if (!url) return false;
    
    try {
      const urlObj = new URL(url, API_BASE_URL);
      
      // Rejeter les protocoles non sécurisés
      if (!['http:', 'https:'].includes(urlObj.protocol)) {
        return false;
      }
      
      // Vérifier que l'URL provient du domaine autorisé
      const allowedOrigin = new URL(API_BASE_URL).origin;
      if (urlObj.origin !== allowedOrigin) {
        console.warn('URL d\'image non autorisée:', url);
        return false;
      }
      
      return true;
    } catch (error) {
      console.error('URL invalide:', url, error);
      return false;
    }
  };
  
  return {
    buildImageUrl,
    isValidImageUrl
  };
}
```

### useImageLoader.js

Gère le préchargement et le cache des images.

```javascript
import { ref } from 'vue';

// Cache global des images chargées
const imageCache = new Map();

export function useImageLoader() {
  const loadingImages = ref(new Set());
  
  /**
   * Charge une image et la met en cache
   * @param {string} url - URL de l'image
   * @returns {Promise<HTMLImageElement>}
   */
  const loadImage = (url) => {
    return new Promise((resolve, reject) => {
      // Vérifier le cache
      if (imageCache.has(url)) {
        resolve(imageCache.get(url));
        return;
      }
      
      // Éviter les chargements multiples
      if (loadingImages.value.has(url)) {
        // Attendre que le chargement en cours se termine
        const checkInterval = setInterval(() => {
          if (imageCache.has(url)) {
            clearInterval(checkInterval);
            resolve(imageCache.get(url));
          }
        }, 100);
        return;
      }
      
      loadingImages.value.add(url);
      
      const img = new Image();
      
      img.onload = () => {
        imageCache.set(url, img);
        loadingImages.value.delete(url);
        resolve(img);
      };
      
      img.onerror = (error) => {
        loadingImages.value.delete(url);
        console.error('Erreur chargement image:', url, error);
        
        // Tentative de rechargement une seule fois
        if (!img.dataset.retried) {
          img.dataset.retried = 'true';
          setTimeout(() => {
            img.src = url;
          }, 1000);
        } else {
          reject(new Error(`Impossible de charger l'image: ${url}`));
        }
      };
      
      img.src = url;
    });
  };
  
  /**
   * Précharge plusieurs images en parallèle
   * @param {string[]} urls - Array d'URLs à précharger
   * @returns {Promise<HTMLImageElement[]>}
   */
  const preloadImages = async (urls) => {
    const validUrls = urls.filter(url => url && typeof url === 'string');
    
    try {
      const promises = validUrls.map(url => loadImage(url));
      return await Promise.all(promises);
    } catch (error) {
      console.error('Erreur préchargement images:', error);
      throw error;
    }
  };
  
  /**
   * Vide le cache d'images
   */
  const clearCache = () => {
    imageCache.clear();
  };
  
  /**
   * Obtient la taille du cache
   */
  const getCacheSize = () => {
    return imageCache.size;
  };
  
  return {
    loadImage,
    preloadImages,
    clearCache,
    getCacheSize,
    loadingImages: loadingImages.value
  };
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: URL Construction Consistency

*For any* image path (relative or absolute), building the image URL should produce a valid, accessible URL that points to the correct resource.

**Validates: Requirements 4.1, 4.2, 4.3**

### Property 2: Image Loading Idempotence

*For any* image URL, loading the same image multiple times should return the cached version after the first successful load, without making additional network requests.

**Validates: Requirements 5.4**

### Property 3: Error Handling Graceful Degradation

*For any* image that fails to load, the system should display a fallback UI and allow the user to continue the quiz without blocking functionality.

**Validates: Requirements 7.1, 7.2, 7.4**

### Property 4: Responsive Grid Adaptation

*For any* screen width, the image grid should adapt to display the appropriate number of columns (1 on mobile, 2 on tablet, 3 on desktop) while maintaining image aspect ratios.

**Validates: Requirements 8.1, 8.2, 8.3, 8.4**

### Property 5: Image Association Shuffling

*For any* ASSOCIATION_IMAGE question, the images in column B should be displayed in a random order that differs from the original pairing order.

**Validates: Requirements 3.2**

### Property 6: Accessibility Attributes Presence

*For any* displayed image, the rendered HTML should include appropriate alt text and ARIA attributes for screen reader compatibility.

**Validates: Requirements 6.1, 6.2, 6.3, 6.4**

### Property 7: Security URL Validation

*For any* image URL, the system should reject URLs with unsafe protocols (javascript:, data:, file:) and only accept http: or https: from the configured domain.

**Validates: Requirements 10.1, 10.2**

### Property 8: Preloading Completion Before Display

*For any* question with multiple images, all images should be preloaded before the question is displayed to the user, ensuring a smooth experience.

**Validates: Requirements 5.1, 5.5**

## Error Handling

### Image Loading Errors

**Stratégie:**
1. Première tentative de chargement
2. Si échec, attendre 1 seconde et réessayer une fois
3. Si échec persistant, afficher le placeholder
4. Logger l'erreur dans la console pour le débogage

**Codes d'erreur:**
- `IMAGE_LOAD_FAILED`: Impossible de charger l'image après 2 tentatives
- `IMAGE_URL_INVALID`: URL d'image mal formée ou non sécurisée
- `IMAGE_NOT_FOUND`: Image introuvable (404)

### Network Errors

**Stratégie:**
- Afficher un message d'erreur réseau global
- Permettre à l'utilisateur de réessayer le chargement
- Sauvegarder les réponses déjà données en local

### Validation Errors

**Stratégie:**
- Valider la structure des données d'images à la réception
- Logger les données mal formées
- Afficher la question sans images si les données sont invalides

## Testing Strategy

### Unit Tests

**Composables:**
- `useImageUrl.buildImageUrl()`: Tester avec chemins relatifs, absolus, avec/sans slash
- `useImageUrl.isValidImageUrl()`: Tester avec URLs valides et invalides
- `useImageLoader.loadImage()`: Tester chargement, cache, erreurs
- `useImageLoader.preloadImages()`: Tester chargement parallèle

**Composants:**
- `QuizQuestionImage`: Tester affichage, erreur, modale
- `QuizImageOptions`: Tester sélection, grille responsive
- `QuizImageAssociation`: Tester associations, mélange

### Property-Based Tests

Chaque test de propriété doit être exécuté avec un minimum de 100 itérations pour garantir une couverture suffisante.

**Configuration recommandée:**
- Bibliothèque: `fast-check` pour JavaScript/TypeScript
- Itérations: 100 minimum par test
- Seed: Aléatoire mais loggé pour reproductibilité

**Tests à implémenter:**

1. **Property 1: URL Construction Consistency**
   - Générer des chemins aléatoires (relatifs/absolus)
   - Vérifier que buildImageUrl() produit toujours une URL valide
   - Tag: `Feature: affichage-images-quiz-collaborateur, Property 1: URL Construction Consistency`

2. **Property 2: Image Loading Idempotence**
   - Générer des URLs aléatoires
   - Charger la même image plusieurs fois
   - Vérifier qu'une seule requête réseau est faite
   - Tag: `Feature: affichage-images-quiz-collaborateur, Property 2: Image Loading Idempotence`

3. **Property 3: Error Handling Graceful Degradation**
   - Générer des URLs invalides
   - Vérifier que le système affiche un fallback
   - Vérifier que l'application reste fonctionnelle
   - Tag: `Feature: affichage-images-quiz-collaborateur, Property 3: Error Handling Graceful Degradation`

4. **Property 4: Responsive Grid Adaptation**
   - Générer des largeurs d'écran aléatoires
   - Vérifier que le nombre de colonnes est correct
   - Tag: `Feature: affichage-images-quiz-collaborateur, Property 4: Responsive Grid Adaptation`

5. **Property 5: Image Association Shuffling**
   - Générer des paires d'images aléatoires
   - Vérifier que l'ordre de la colonne B est différent
   - Tag: `Feature: affichage-images-quiz-collaborateur, Property 5: Image Association Shuffling`

6. **Property 6: Accessibility Attributes Presence**
   - Générer des images aléatoires
   - Vérifier la présence d'attributs alt et ARIA
   - Tag: `Feature: affichage-images-quiz-collaborateur, Property 6: Accessibility Attributes Presence`

7. **Property 7: Security URL Validation**
   - Générer des URLs avec protocoles variés
   - Vérifier que seuls http/https sont acceptés
   - Tag: `Feature: affichage-images-quiz-collaborateur, Property 7: Security URL Validation`

8. **Property 8: Preloading Completion Before Display**
   - Générer des questions avec nombre variable d'images
   - Vérifier que toutes les images sont chargées avant affichage
   - Tag: `Feature: affichage-images-quiz-collaborateur, Property 8: Preloading Completion Before Display`

### Integration Tests

- Tester le flux complet de chargement d'un quiz avec images
- Tester la navigation entre questions avec préchargement
- Tester la soumission de réponses avec images sélectionnées

### E2E Tests (Optionnel)

- Tester l'expérience utilisateur complète sur différents navigateurs
- Tester sur différentes tailles d'écran (mobile, tablet, desktop)
- Tester avec connexion lente pour valider les indicateurs de chargement
