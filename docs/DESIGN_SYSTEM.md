# 🎨 LibraShelf - Design System

## Vue d'ensemble

Le design de LibraShelf adopte une approche moderne et professionnelle avec un style épuré et élégant.

## 🎨 Palette de couleurs

### Couleurs principales
- **Gradient principal** : `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`
- **Violet primaire** : `#667eea`
- **Violet secondaire** : `#764ba2`

### Couleurs d'état
- **Succès** : `#28a745` (badges verts)
- **Danger** : `#dc3545` (badges rouges)
- **Avertissement** : `#ffc107` (badges jaunes)
- **Info** : `#17a2b8` (badges bleus)

## 📐 Composants

### Cards
- **Border-radius** : `20px`
- **Shadow** : `0 10px 40px rgba(0, 0, 0, 0.1)`
- **Shadow hover** : `0 15px 60px rgba(0, 0, 0, 0.15)`
- **Effet hover** : Translation Y de `-10px`
- **Background** : `rgba(255, 255, 255, 0.98)` avec `backdrop-filter: blur(10px)`

### Boutons
- **Border-radius** : `12px`
- **Padding** : `0.75rem 2rem`
- **Font-weight** : `600`
- **Text-transform** : `uppercase`
- **Letter-spacing** : `0.5px`
- **Effet ripple** : Animation d'ondulation au clic

### Formulaires
- **Border-radius** : `10px`
- **Border** : `2px solid #e0e0e0`
- **Background** : `#f8f9fa` (normal) / `white` (focus)
- **Focus** : Translation Y de `-2px` avec bordure `#667eea`

### Tableaux
- **Header** : Gradient violet avec texte blanc
- **Hover row** : `rgba(102, 126, 234, 0.05)` avec `scale(1.01)`
- **Border-radius** : `20px` pour la card conteneur

## 🔤 Typographie

- **Police principale** : `Inter` (Google Fonts)
- **Fallback** : `-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`

### Hiérarchie
- **Display-4/5** : Titres de pages principales (fw: 800)
- **H1-H6** : Titres de sections (fw: 700)
- **Lead** : Sous-titres (fw: 300, size: 1.3rem)
- **Body** : Texte standard (fw: 400)

## 🎭 Animations

### Animations principales
1. **fadeInDown** : Entrée par le haut
2. **fadeInUp** : Entrée par le bas
3. **fadeInScale** : Entrée avec zoom
4. **pulse** : Pulsation d'icônes
5. **shimmer** : Effet de chargement

### Transitions
- **Durée standard** : `0.3s`
- **Easing** : `ease` ou `cubic-bezier(0.165, 0.84, 0.44, 1)`

## 📱 Responsive

### Breakpoints
- **Mobile** : `< 576px`
- **Tablet** : `576px - 768px`
- **Desktop** : `> 768px`

### Adaptations mobiles
- Réduction des `display-4/5`
- Padding des cards réduit
- Icons circles plus petits
- Boutons adaptés

## 🎯 Éléments spéciaux

### Icon Circle
```html
<div class="icon-circle">
    <i class="bi bi-icon" style="font-size: 2rem; color: #667eea;"></i>
</div>
```
- Dimensions : `60px × 60px`
- Background : Gradient violet transparent
- Border-radius : `50%`

### Badges de rôle
- **Admin** : Badge rouge avec icône shield-lock
- **Librarian** : Badge bleu avec icône book-half
- **Member** : Badge gris avec icône person

### Navigation
- **Navbar** : Fond blanc transparent avec `backdrop-filter`
- **Links** : Couleur violette, hover avec translation
- **Dropdown** : Border-radius `12px`, shadow élégante

## 🌈 États et interactions

### Hover
- Translation légère (`-2px` à `-5px`)
- Augmentation du shadow
- Scale légère (`1.01` à `1.05`)

### Focus
- Outline violet `2px solid #667eea`
- Offset de `2px`
- Border-radius `8px`

### Active
- Effet ripple sur les boutons
- Background plus soutenu

## 📋 Pages types

### Page d'accueil
- Background : Gradient violet full-screen
- Card centrale blanche
- Feature boxes avec icônes

### Dashboard
- Cards en grille (4 colonnes)
- Icônes centrées avec grande taille
- Animations séquentielles

### Pages liste
- Header avec titre et action
- Table avec header gradient
- Footer avec statistiques

### Formulaires
- Layout centré
- Labels avec icônes
- Placeholders informatifs
- Messages flash avec icônes

## 🛠️ Fichiers clés

- **CSS principal** : `/assets/styles/app.css`
- **Template de base** : `/templates/base.html.twig`
- **Police** : Google Fonts (Inter)
- **Icons** : Bootstrap Icons 1.11.0

## 💡 Bonnes pratiques

1. Toujours utiliser des icônes Bootstrap Icons
2. Respecter le gradient principal pour la cohérence
3. Utiliser les animations avec parcimonie
4. Maintenir l'accessibilité (focus visible, contraste)
5. Tester sur mobile et desktop
6. Utiliser les classes utilitaires Bootstrap 5
7. Préférer les cards pour regrouper le contenu
8. Animations avec `animation-delay` pour effet séquentiel

---

**Version** : 1.0  
**Dernière mise à jour** : 25 novembre 2025  
**Auteur** : GitHub Copilot pour LibraShelf
