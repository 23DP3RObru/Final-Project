<template>
  <div id="app">
    <header class="app-header">
      <div class="header-container">
        <router-link to="/" class="brand">
          <i class="bi bi-shop"></i>
          <span>ProbablyLegit</span>
        </router-link>

        <button 
          class="menu-toggle" 
          @click="mobileMenuOpen = !mobileMenuOpen"
          :aria-expanded="mobileMenuOpen"
          aria-label="Menu"
        >
          <span class="toggle-bar"></span>
          <span class="toggle-bar"></span>
          <span class="toggle-bar"></span>
        </button>

        <nav 
          class="primary-nav" 
          :class="{ 'is-open': mobileMenuOpen }"
        >
          <router-link to="/" class="nav-link" active-class="active" exact>
            <i class="bi bi-house"></i> Home
          </router-link>
          <router-link to="/items" class="nav-link" active-class="active">
            <i class="bi bi-grid"></i> Pārlūkot preces
          </router-link>
        </nav>
      </div>
    </header>

    <main class="app-main">
      <div class="main-container">
        <router-view />
      </div>
    </main>

    <footer class="app-footer">
      <div class="footer-container">
        <span class="footer-text">ProbablyLegit © 2026</span>
        <span class="footer-text">Būvēts ar Vue.js + Laravel</span>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const mobileMenuOpen = ref(false);
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: #fafaf7;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  color: #ffffff;
}

.app-header {
  background: black;
  border-bottom: 1px solid #e2e2e2;
  padding: 0.5rem 0;
  width: 100%;
}

.header-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0.8rem 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
}

.brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  text-decoration: none;
  font-size: 1.5rem;
  font-weight: 320;
  letter-spacing: -0.02em;
  color: #ffffff;
  transition: opacity 0.15s;
}

.brand i {
  font-size: 1.7rem;
  color: #ececec;
  line-height: 1;
}

.brand:hover {
  opacity: 0.7;
}

.menu-toggle {
  display: none;
  flex-direction: column;
  justify-content: space-between;
  width: 26px;
  height: 20px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
}

.toggle-bar {
  width: 100%;
  height: 1.8px;
  background-color: #ffffff;
  border-radius: 2px;
  transition: 0.2s;
}

.primary-nav {
  display: flex;
  align-items: center;
  gap: 2.2rem;
}

.nav-link {
  text-decoration: none;
  color: #ffffff;
  font-weight: 380;
  font-size: 1.05rem;
  padding: 0.4rem 0;
  letter-spacing: -0.005em;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  border-bottom: 1.5px solid transparent;
  transition: border-color 0.15s, color 0.15s;
}

.nav-link i {
  font-size: 1.15rem;
  color: #ffffff;
}


.nav-link.active {
  border-bottom-color: #e9e9e9;
  color: #ffffff;
  font-weight: 430;
}

.nav-link.active i {
  color: #b8b8b8;
}

.nav-link:hover {
  color: #c5c5c5;
  border-bottom-color: #aaa;
}

.app-main {
  flex: 1;
  display: flex;
  width: 100%;
}

.main-container {
  max-width: 1280px;
  width: 100%;
  margin: 0 auto;
  padding: 2.5rem 2rem;
}

.app-footer {
  border-top: 1px solid #000000;
  background: black;
  padding: 1.8rem 0;
  margin-top: auto;
}

.footer-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.8rem;
}

.footer-text {
  font-size: 0.9rem;
  color: #c7c7c7;
  font-weight: 340;
  letter-spacing: 0.2px;
}

@media (max-width: 768px) {
  .header-container {
    padding: 0.8rem 1.5rem;
    flex-wrap: wrap;
  }

  .menu-toggle {
    display: flex;
  }

  .primary-nav {
    display: none;
    flex-direction: column;
    width: 100%;
    padding: 1.5rem 0 0.8rem 0;
    gap: 1.2rem;
  }

  .primary-nav.is-open {
    display: flex;
  }

  .nav-link {
    width: 100%;
    padding: 0.6rem 0;
    border-bottom: none;
    border-left: 2px solid transparent;
    padding-left: 0.5rem;
  }

  .nav-link.active {
    border-bottom: none;
    border-left: 2px solid #ffffff;
    background: none;
    font-weight: 450;
  }

  .brand {
    font-size: 1.4rem;
  }

  .main-container {
    padding: 2rem 1.5rem;
  }

  .footer-container {
    flex-direction: column;
    text-align: center;
    padding: 0 1.5rem;
  }
}


@media (max-width: 480px) {
  .header-container {
    padding: 0.8rem 1.2rem;
  }
  .main-container {
    padding: 1.8rem 1.2rem;
  }
}

.bg-primary, .navbar, .navbar-dark, .navbar-brand, .navbar-nav, .bg-light {
  all: unset;
}

.bi {
  color: currentColor;
}

a {
  text-decoration: none;
}
</style>
