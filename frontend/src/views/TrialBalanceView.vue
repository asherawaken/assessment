<template>
  <div class="trial-balance">
    <div class="header">
      <h1>Trial Balance</h1>
      <button @click="loadTrialBalance" class="btn btn-primary">Refresh</button>
    </div>

    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="trialBalance">
      <div class="summary">
        <div class="summary-item">
          <strong>Total Debits:</strong>
          <span>{{ formatCurrency(trialBalance.totals.total_debits) }}</span>
        </div>
        <div class="summary-item">
          <strong>Total Credits:</strong>
          <span>{{ formatCurrency(trialBalance.totals.total_credits) }}</span>
        </div>
        <div class="summary-item" :class="{ 'error': trialBalance.totals.difference !== 0 }">
          <strong>Difference:</strong>
          <span>{{ formatCurrency(trialBalance.totals.difference) }}</span>
        </div>
      </div>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Account Code</th>
              <th>Account Name</th>
              <th>Type</th>
              <th class="text-right">Total Debits</th>
              <th class="text-right">Total Credits</th>
              <th class="text-right">Balance</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="account in trialBalance.accounts" :key="account.id">
              <td>{{ account.code }}</td>
              <td>{{ account.name }}</td>
              <td>
                <span class="account-type">{{ account.type }}</span>
              </td>
              <td class="text-right">{{ formatCurrency(account.total_debits) }}</td>
              <td class="text-right">{{ formatCurrency(account.total_credits) }}</td>
              <td class="text-right" :class="{ 'negative': account.balance < 0 }">
                {{ formatCurrency(account.balance) }}
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="totals-row">
              <td colspan="3"><strong>Totals</strong></td>
              <td class="text-right"><strong>{{ formatCurrency(trialBalance.totals.total_debits) }}</strong></td>
              <td class="text-right"><strong>{{ formatCurrency(trialBalance.totals.total_credits) }}</strong></td>
              <td class="text-right"><strong>{{ formatCurrency(trialBalance.totals.difference) }}</strong></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
// @ts-expect-error - API service doesn't have TypeScript definitions
import { api } from '../services/api'

interface TrialBalanceAccount {
  id: number
  code: string
  name: string
  type: string
  total_debits: number
  total_credits: number
  balance: number
}

interface TrialBalance {
  accounts: TrialBalanceAccount[]
  totals: {
    total_debits: number
    total_credits: number
    difference: number
  }
}

const trialBalance = ref<TrialBalance | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

const loadTrialBalance = async () => {
  loading.value = true
  error.value = null
  try {
    trialBalance.value = await api.getTrialBalance()
  } catch (err: unknown) {
    error.value = err instanceof Error ? err.message : 'An error occurred'
  } finally {
    loading.value = false
  }
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount)
}

onMounted(() => {
  loadTrialBalance()
})
</script>

<style scoped>
.trial-balance {
  max-width: 1600px;
  margin: 0 auto;
  padding: 2rem 1rem;
  min-height: calc(100vh - 80px);
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.header h1 {
  margin: 0;
  color: #2c3e50;
  font-size: 2rem;
  font-weight: 700;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  background-color: #42b983;
  color: white;
}

.btn:hover {
  background-color: #35a372;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn:active {
  transform: translateY(0);
}

.loading, .error {
  text-align: center;
  padding: 3rem 2rem;
  font-size: 1.1rem;
}

.loading {
  color: #42b983;
  font-weight: 600;
}

.error {
  color: #e74c3c;
  background-color: #fee;
  border: 1px solid #e74c3c;
  border-radius: 8px;
  padding: 1.5rem;
  margin: 2rem 0;
}

.summary {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
  padding: 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.summary-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.summary-item strong {
  margin-bottom: 0.75rem;
  color: #7f8c8d;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
}

.summary-item span {
  font-size: 1.75rem;
  font-weight: 700;
  color: #2c3e50;
  font-family: 'Courier New', monospace;
}

.summary-item.error {
  background: linear-gradient(135deg, #fee 0%, #fdd 100%);
  border: 2px solid #e74c3c;
}

.summary-item.error span {
  color: #e74c3c;
}

.table-container {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  min-width: 800px;
}

thead {
  background: linear-gradient(135deg, #42b983 0%, #35a372 100%);
  color: white;
}

th {
  padding: 1.25rem 1rem;
  text-align: left;
  font-weight: 700;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  position: sticky;
  top: 0;
  z-index: 10;
}

th.text-right {
  text-align: right;
}

td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #f0f0f0;
  color: #2c3e50;
}

tbody tr:hover {
  background-color: #f8f9fa;
}

tbody tr:last-child td {
  border-bottom: none;
}

.text-right {
  text-align: right;
  font-family: 'Courier New', monospace;
}

.account-type {
  text-transform: capitalize;
  padding: 0.4rem 0.8rem;
  background: #e9ecef;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  color: #495057;
  display: inline-block;
}

.negative {
  color: #e74c3c;
  font-weight: 700;
}

.totals-row {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  font-weight: 700;
  border-top: 3px solid #42b983;
}

.totals-row td {
  padding: 1.25rem 1rem;
  color: #2c3e50;
  font-size: 1.1rem;
}

tfoot td {
  border-top: 3px solid #42b983;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .summary {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .trial-balance {
    padding: 1rem 0.5rem;
  }

  .header {
    flex-direction: column;
    align-items: stretch;
  }

  .header h1 {
    font-size: 1.5rem;
  }

  .btn {
    width: 100%;
    padding: 1rem;
  }

  .summary {
    grid-template-columns: 1fr;
    padding: 1.5rem;
    gap: 1rem;
  }

  .summary-item {
    padding: 1rem;
  }

  .summary-item span {
    font-size: 1.5rem;
  }

  .table-container {
    border-radius: 8px;
  }

  table {
    font-size: 0.875rem;
  }

  th, td {
    padding: 0.75rem 0.5rem;
  }
}

@media (max-width: 480px) {
  .trial-balance {
    padding: 0.5rem;
  }

  .summary {
    padding: 1rem;
  }

  table {
    min-width: 700px;
    font-size: 0.8rem;
  }

  th, td {
    padding: 0.5rem 0.25rem;
  }
}
</style>

