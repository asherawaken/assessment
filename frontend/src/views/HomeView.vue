<template>
  <div class="home">
    <div class="welcome">
      <h1>Welcome to the Accounting Module</h1>
      <p class="subtitle">General Ledger Journal System</p>
      
      <div class="features">
        <div class="feature-card">
          <h3>Journal Entries</h3>
          <p>Create and manage journal entries with double-entry accounting validation.</p>
          <router-link to="/journal-entries" class="btn">Go to Journal Entries</router-link>
        </div>
        
        <div class="feature-card">
          <h3>Trial Balance</h3>
          <p>View trial balance report grouped by account with debit and credit totals.</p>
          <router-link to="/trial-balance" class="btn">View Trial Balance</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.home {
  max-width: 1200px;
  margin: 0 auto;
  padding: 4rem 2rem;
}

.welcome {
  text-align: center;
}

.welcome h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
  color: #2c3e50;
}

.subtitle {
  font-size: 1.25rem;
  color: #666;
  margin-bottom: 3rem;
}

.features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-top: 3rem;
}

.feature-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  text-align: left;
}

.feature-card h3 {
  color: #42b983;
  margin-top: 0;
  margin-bottom: 1rem;
}

.feature-card p {
  color: #666;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.btn {
  display: inline-block;
  padding: 0.75rem 1.5rem;
  background-color: #42b983;
  color: white;
  text-decoration: none;
  border-radius: 4px;
  font-weight: 600;
  transition: background-color 0.2s;
}

.btn:hover {
  background-color: #35a372;
}
</style>
