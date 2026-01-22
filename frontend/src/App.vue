<template>
  <div id="app">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <router-link to="/" class="navbar-brand">Vue + Laravel</router-link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <router-link to="/" class="nav-link" active-class="active">Home</router-link>
            </li>
            <li class="nav-item">
              <router-link to="/items" class="nav-link" active-class="active">Database Test</router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    
    <main class="container-fluid py-4">
      <router-view/>
    </main>
    
    <footer class="footer mt-auto py-3 bg-light">
      <div class="container text-center">
        <span class="text-muted">Vue.js + Laravel API Connected</span>
      </div>
    </footer>
  </div>
</template>

<script setup>
// No script needed for basic navigation
</script>

<style>
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}

.nav-link.active {
  font-weight: bold;
  color: #fff !important;
}

.footer {
  margin-top: auto;
}
</style>