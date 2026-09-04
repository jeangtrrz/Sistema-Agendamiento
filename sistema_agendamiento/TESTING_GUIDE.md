# 🧪 Testing Guide - Mobile Navigation (Hamburger Menu)

**Documento:** Testing & Validation Guide  
**Fecha:** 2026-08-13  
**Sistema:** Sistema de Agendamiento Internet Cordillera  

---

## 🚀 Quick Start - Verificar Implementación

### En el Navegador

1. **Abrir cualquier página del sistema**
   - dashboard.php
   - citas.php
   - calendario.php
   - eventos.php
   - reportes.php
   - usuarios.php

2. **Redimensionar la ventana a 768px o menos** (DevTools)
   - F12 → Toggle Device Toolbar (Ctrl+Shift+M en Chrome)
   - Seleccionar dispositivo mobile (iPhone, Android, etc.)

3. **Ver cambios:**
   - ✅ Navbar mostrada normalmente (sin cambios)
   - ✅ Hamburger icon (≡) aparece a la derecha
   - ✅ Menú de navegación desaparece

4. **Hacer clic en el hamburger icon**
   - ✅ Icon se transforma a X
   - ✅ Menú desliza desde arriba
   - ✅ Overlay oscuro aparece
   - ✅ User section se fija al fondo

---

## 📋 Testing Checklist - Funcionalidad

### Desktop Mode (> 768px)

- [ ] **Navbar Display**
  - Hamburger icon NO visible
  - Menú horizontal visible
  - Logo + marca visible
  - User profile visible

- [ ] **Resize a 769px**
  - Hamburger desaparece
  - Menu se convierte a horizontal
  - Todo vuelve a normal

- [ ] **Links Navigation**
  - Dashboard funciona
  - Citas funciona
  - Calendario funciona
  - Eventos funciona
  - Reportes funciona
  - Usuarios funciona (admin)

### Mobile Mode (≤ 768px)

- [ ] **Initial State**
  - Hamburger icon (≡) visible
  - Menú NO visible
  - Logo visible
  - User profile NO visible (escondido)

- [ ] **Click Hamburger**
  - Icon → X animation suave
  - Menú aparece con items:
    - Dashboard
    - Citas
    - Calendario
    - Eventos
    - Reportes
    - Usuarios (si es admin)
  - Overlay oscuro aparece (50% opacidad)

- [ ] **User Section**
  - Avatar visible al fondo
  - Username visible
  - "Salir" link visible
  - Background oscuro

- [ ] **Click Item del Menú**
  - Navega a la página
  - Menú se cierra automáticamente
  - Overlay desaparece
  - Page actualiza correctamente

- [ ] **Click Overlay**
  - Menú se cierra
  - Icon X → ≡
  - Overlay desaparece
  - Puedes seguir usando la app

- [ ] **Hamburger Toggle**
  - Abre/cierra sin problemas
  - Animación suave
  - Sin glitches

### Keyboard Navigation

- [ ] **Presionar ESC**
  - Menú se cierra inmediatamente
  - Icon vuelve a ≡
  - Overlay desaparece

- [ ] **Tab navigation**
  - Puedes navegar con TAB
  - Buttons son focusables
  - Links son focusables

### Scroll Behavior

- [ ] **Menú cerrado**
  - Body scroll funciona normalmente
  - Puedes hacer scroll

- [ ] **Menú abierto**
  - Body scroll DESHABILITADO
  - No puedes hacer scroll en body
  - Solo el menú es scrolleable si necesario

- [ ] **Menú cierra**
  - Body scroll se HABILITA nuevamente
  - Scroll funciona normal

### Resize Handling

- [ ] **Desktop → Mobile (> 768px a ≤ 768px)**
  - Hamburger aparece
  - Menú se oculta
  - Layout cambia correctamente

- [ ] **Mobile → Desktop (≤ 768px a > 768px)**
  - Hamburger desaparece
  - Si menú estaba abierto, se cierra
  - Menú vuelve a horizontal
  - Layout cambia correctamente

---

## 📱 Device Testing

### Smartphones

| Device | Resolution | Status | Notes |
|--------|-----------|--------|-------|
| iPhone 12 | 390x844 | [Test] | Portrait mode |
| iPhone 12 Pro | 390x844 | [Test] | Landscape mode |
| Pixel 5 | 393x851 | [Test] | Android test |
| Samsung S21 | 360x800 | [Test] | Ultra-mobile |
| iPhone SE | 375x667 | [Test] | Older device |

### Tablets

| Device | Resolution | Status | Notes |
|--------|-----------|--------|-------|
| iPad Mini | 768x1024 | [Test] | Edge case (768px) |
| iPad (10th) | 820x1180 | [Test] | Should NOT show hamburger |
| Galaxy Tab | 800x1280 | [Test] | Should NOT show hamburger |

### Desktop Browsers

| Browser | Version | Status | Notes |
|---------|---------|--------|-------|
| Chrome | Latest | [Test] | Full support |
| Firefox | Latest | [Test] | Full support |
| Safari | Latest | [Test] | Full support |
| Edge | Latest | [Test] | Full support |

---

## 🎬 Escenarios de Testing Completos

### Escenario 1: Mobile User Journey

```
1. Usuario abre app en móvil
2. Ve hamburger icon (≡)
3. Hace clic en hamburger
4. Menú se abre, overlay aparece
5. Ve lista de opciones: Dashboard, Citas, Calendario, etc.
6. Hace clic en "Citas"
7. Navega a citas.php
8. Menú se cierra automáticamente
9. En citas.php, ve hamburger nuevamente
10. Puede abrir hamburger de nuevo
11. Hace clic en "Calendario"
12. Navega a calendario.php
13. Prueba presionar ESC
14. Menú se cierra
15. Prueba resize a 900px
16. Hamburger desaparece, menú se muestra horizontal
17. ✅ Test completado exitosamente
```

### Escenario 2: Overlay Interaction

```
1. Usuario en móvil
2. Abre menú (hamburger → X)
3. Hace clic en el overlay oscuro (NO en el menú)
4. Menú se cierra
5. X vuelve a ≡
6. Overlay desaparece
7. ✅ Test completado exitosamente
```

### Escenario 3: Touch & Mobile Edge Cases

```
1. Usuario en móvil pequeño (320px)
2. Abre menú
3. Items menú visibles
4. Puede hacer scroll en menú si es muy largo
5. User section al fondo
6. Logout link funciona
7. Hace logout
8. Redirige a login
9. ✅ Test completado exitosamente
```

---

## 🔍 Visual Validation Checklist

### Animaciones

- [ ] **Hamburger Icon Animation**
  - Línea 1 rota 45° y se traslada
  - Línea 2 desaparece (opacity 0)
  - Línea 3 rota -45° y se traslada
  - Duración: suave (~0.3s)
  - Reversible (X → ≡)

- [ ] **Menu Appearance**
  - Menú aparece desde arriba
  - Items visibles y legibles
  - Spacing correcto entre items
  - Sin overlap con navbar

- [ ] **Overlay**
  - Fondo semi-transparente (50% opacidad)
  - Color negro
  - No es clickeable fuera de área
  - Desaparece suavemente

### Styling

- [ ] **Colors Consistent**
  - Primary blue para navbar
  - White text en navbar
  - Overlay color correcto
  - No hay colores inesperados

- [ ] **Spacing**
  - Padding uniforme
  - Menú items spacing consistente
  - Usuario section con separación clara
  - No hay elementos cortados

- [ ] **Typography**
  - Font legible
  - Tamaño adecuado para móvil
  - Peso de fuente correcto
  - Contraste suficiente

- [ ] **Responsive Padding**
  - 768px: padding estándar
  - 480px: padding reducido
  - Números correctos en media queries

---

## 🐛 Bug Prevention Tests

### Potencial Issues to Check

1. **Z-index Stacking**
   - [ ] Menú aparece ENCIMA de contenido
   - [ ] Overlay está DEBAJO de menú
   - [ ] Button es visible ENCIMA

2. **Scroll Issues**
   - [ ] Body no scrollea cuando menú abierto
   - [ ] Body scrollea cuando menú cerrado
   - [ ] Contenido no se mueve lateralmente

3. **Memory Leaks**
   - [ ] Console sin errores
   - [ ] Event listeners se limpian correctamente
   - [ ] No hay listeners duplicados

4. **Performance**
   - [ ] Animaciones suaves (60fps idealmente)
   - [ ] Sin lag al abrir/cerrar
   - [ ] Sin freezing en dispositivos lentos

5. **Accesibility**
   - [ ] aria-label en button
   - [ ] Button es keyboard focusable
   - [ ] ESC funciona para screen readers

---

## 🧩 Integration Tests

### Con Otras Features

- [ ] **Dashboard Stats**
  - Hamburger no interfiere con stats
  - Stats visibles correctamente
  - Menú no tapa stats

- [ ] **Calendar View**
  - Calendario responsivo con menú
  - Hamburger no tapa slots
  - Overlay no interfiere

- [ ] **Forms**
  - Inputs no tienen z-index issues
  - Menú no interfiere con focus
  - Dropdowns se cierran correctamente

- [ ] **Modals**
  - Si hay modals, qué pasa cuando menú abierto
  - Modal aparece ENCIMA de menú
  - Menú se cierra si modal abierto

---

## 📊 Testing Report Template

```
=== TESTING REPORT ===
Date: ___________
Tester: _________
Device: ________
Browser: _______
Screen Size: ____

PASSED TESTS: ___/___
FAILED TESTS: ___/___
ISSUES FOUND: ___/___

MAJOR ISSUES:
- [Describe any critical bugs]

MINOR ISSUES:
- [Describe cosmetic issues]

NOTES:
- [Any additional observations]

RECOMMENDATION:
[ ] APPROVED - Ready for production
[ ] APPROVED WITH NOTES - Minor fixes needed
[ ] NEEDS REWORK - Major issues found
[ ] BLOCKED - Critical issues found

Signature: ___________
```

---

## ✅ Final Validation Checklist

Before considering implementation complete:

- [ ] All mobile breakpoints tested
- [ ] All devices tested
- [ ] All browsers tested
- [ ] Keyboard navigation works
- [ ] Accessibility attributes present
- [ ] No console errors
- [ ] No CSS conflicts
- [ ] No JS errors
- [ ] Animations smooth
- [ ] Performance acceptable
- [ ] Documentation complete
- [ ] Ready for production

---

## 📝 Known Limitations & Future Improvements

### Current Limitations
- Focus management no optimizado para screen readers
- Sin swipe gestures (requeriría librería adicional)
- Sub-menús no implementados (pero posibles)

### Future Improvements
- [ ] Agregar slide animation al menú (entrada/salida)
- [ ] Implementar sub-menús para "Reportes"
- [ ] Agregar notificación badges
- [ ] Swipe gesture support (right swipe to close)
- [ ] Dark mode soporte
- [ ] Animated underline en active item

---

## 🆘 Troubleshooting

### Hamburger no aparece en mobile
**Solution:** Verificar que CSS esté incluido en `<head>`
```html
<link rel="stylesheet" href="assets/css/style.css">
```

### Menu no se abre
**Solution:** Verificar que JavaScript esté al final de `<body>`
```html
<script src="assets/js/main.js"></script>
```

### Overlay no aparece
**Solution:** Verificar que div exista en HTML
```html
<div class="navbar-mobile-overlay"></div>
```

### Body scroll no funciona bien
**Solution:** Revisar que `MobileNav.js` se ejecute correctamente
- Abrir DevTools (F12)
- Ver console por errores
- Verificar que `MobileNav` objeto existe

### Animación twitchy
**Solution:** Revisar GPU acceleration
```css
.navbar-toggle span {
    will-change: transform;
}
```

---

## 📞 Support & Questions

Para reportar bugs o problemas:
1. Nota el dispositivo y navegador
2. Screenshot o video del problema
3. Pasos para reproducir
4. Console errors (si hay)
5. Envía a development team

---

**Testing Guide Completado ✅**

*Próximo paso: Ejecutar los tests y documentar resultados*
