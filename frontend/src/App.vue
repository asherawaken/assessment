<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
  <div class="app">
    <header>
      <div class="header-content">
        <h1>Accounting Module</h1>
        <nav>
          <RouterLink to="/">Home</RouterLink>
          <RouterLink to="/journal-entries">Journal Entries</RouterLink>
          <RouterLink to="/trial-balance">Trial Balance</RouterLink>
        </nav>
      </div>
    </header>

    <main>
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

header {
  background: linear-gradient(135deg, #42b983 0%, #35a372 100%);
  color: white;
  padding: 1.25rem 0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-content {
  max-width: 1600px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.header-content h1 {
  margin: 0;
  font-size: 1.75rem;
  font-weight: 700;
  letter-spacing: -0.5px;
}

nav {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

nav a {
  color: white;
  text-decoration: none;
  padding: 0.75rem 1.25rem;
  border-radius: 6px;
  transition: all 0.3s ease;
  font-weight: 500;
  font-size: 0.95rem;
  position: relative;
}

nav a:hover {
  background-color: rgba(255, 255, 255, 0.15);
  transform: translateY(-2px);
}

nav a.router-link-exact-active {
  background-color: rgba(255, 255, 255, 0.25);
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

main {
  flex: 1;
  background-color: #f5f7fa;
  padding: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
  header {
    padding: 1rem 0;
  }

  .header-content {
    padding: 0 1rem;
    flex-direction: column;
    align-items: stretch;
  }

  .header-content h1 {
    font-size: 1.5rem;
    text-align: center;
  }

  nav {
    justify-content: center;
    width: 100%;
  }

  nav a {
    flex: 1;
    text-align: center;
    padding: 0.625rem 0.75rem;
    font-size: 0.875rem;
  }
}

@media (max-width: 480px) {
  .header-content h1 {
    font-size: 1.25rem;
  }

  nav {
    flex-direction: column;
    gap: 0.25rem;
  }

  nav a {
    padding: 0.75rem;
  }
}
</style>
