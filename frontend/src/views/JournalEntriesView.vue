<template>
  <div class="journal-entries">
    <div class="header">
      <h1>Journal Entries</h1>
      <button @click="showForm = true" class="btn btn-primary">Add New Entry</button>
    </div>

    <div v-if="showForm" class="form-container">
      <JournalEntryForm
        :entry="editingEntry"
        @save="handleSave"
        @cancel="handleCancel"
      />
    </div>

    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else class="entries-list">
      <div v-for="entry in entries" :key="entry.id" class="entry-card">
        <div class="entry-header">
          <div>
            <h3>{{ entry.description }}</h3>
            <p class="entry-meta">
              Date: {{ formatDate(entry.entry_date) }} | 
              Ref: {{ entry.reference_number || 'N/A' }}
            </p>
          </div>
          <div class="entry-actions">
            <button @click="editEntry(entry)" class="btn btn-sm">Edit</button>
            <button @click="deleteEntry(entry.id)" class="btn btn-sm btn-danger">Delete</button>
          </div>
        </div>
        <div class="entry-lines">
          <table>
            <thead>
              <tr>
                <th>Account</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="line in entry.lines" :key="line.id">
                <td>{{ line.account.code }} - {{ line.account.name }}</td>
                <td>
                  <span :class="['badge', line.type]">{{ line.type.toUpperCase() }}</span>
                </td>
                <td class="amount">{{ formatCurrency(line.amount) }}</td>
                <td>{{ line.description || '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
// @ts-expect-error - API service doesn't have TypeScript definitions
import { api } from '../services/api'
import JournalEntryForm from '../components/JournalEntryForm.vue'

interface Account {
  id: number
  code: string
  name: string
}

interface JournalEntryLine {
  id: number
  account_id: number
  type: string
  amount: number
  description?: string | null
  account: Account
}

interface JournalEntry {
  id: number
  entry_date: string
  description: string
  reference_number?: string | null
  lines: JournalEntryLine[]
}

const entries = ref<JournalEntry[]>([])
const loading = ref(false)
const error = ref<string | null>(null)
const showForm = ref(false)
const editingEntry = ref<JournalEntry | null>(null)

const loadEntries = async () => {
  loading.value = true
  error.value = null
  try {
    entries.value = await api.getJournalEntries()
  } catch (err: unknown) {
    error.value = err instanceof Error ? err.message : 'An error occurred'
  } finally {
    loading.value = false
  }
}

const handleSave = () => {
  showForm.value = false
  editingEntry.value = null
  loadEntries()
}

const handleCancel = () => {
  showForm.value = false
  editingEntry.value = null
}

const editEntry = (entry: JournalEntry) => {
  editingEntry.value = entry
  showForm.value = true
}

const deleteEntry = async (id: number) => {
  if (!confirm('Are you sure you want to delete this journal entry?')) return
  
  try {
    await api.deleteJournalEntry(id)
    loadEntries()
  } catch (err: unknown) {
    alert('Error deleting entry: ' + (err instanceof Error ? err.message : 'An error occurred'))
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString()
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount)
}

onMounted(() => {
  loadEntries()
})
</script>

<style scoped>
.journal-entries {
  max-width: 1400px;
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
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn:active {
  transform: translateY(0);
}

.btn-primary {
  background-color: #42b983;
  color: white;
}

.btn-primary:hover {
  background-color: #35a372;
}

.btn-sm {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.btn-sm:hover {
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.btn-danger {
  background-color: #e74c3c;
  color: white;
}

.btn-danger:hover {
  background-color: #c0392b;
}

.form-container {
  margin-bottom: 2rem;
  padding: 0;
  background: transparent;
  border-radius: 0;
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

.entries-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.entry-card {
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  padding: 1.5rem;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.entry-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  transform: translateY(-2px);
}

.entry-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #f0f0f0;
  flex-wrap: wrap;
  gap: 1rem;
}

.entry-header h3 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
  font-size: 1.25rem;
  font-weight: 600;
}

.entry-meta {
  color: #7f8c8d;
  font-size: 0.875rem;
  margin: 0;
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.entry-actions {
  display: flex;
  gap: 0.5rem;
  flex-shrink: 0;
}

.entry-lines {
  overflow-x: auto;
}

.entry-lines table {
  width: 100%;
  border-collapse: collapse;
  min-width: 600px;
}

.entry-lines th,
.entry-lines td {
  padding: 1rem 0.75rem;
  text-align: left;
  border-bottom: 1px solid #f0f0f0;
  color: #2c3e50;
}

.entry-lines th {
  background-color: #f8f9fa;
  font-weight: 700;
  color: #34495e;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  position: sticky;
  top: 0;
  z-index: 1;
}

.entry-lines tbody tr:hover {
  background-color: #f8f9fa;
}

.entry-lines tbody tr:last-child td {
  border-bottom: none;
}

.badge {
  padding: 0.35rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: inline-block;
}

.badge.debit {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.badge.credit {
  background-color: #d1ecf1;
  color: #0c5460;
  border: 1px solid #bee5eb;
}

.amount {
  font-weight: 700;
  text-align: right;
  color: #2c3e50;
  font-family: 'Courier New', monospace;
}

/* Responsive Design */
@media (max-width: 768px) {
  .journal-entries {
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

  .entry-header {
    flex-direction: column;
  }

  .entry-actions {
    width: 100%;
    justify-content: stretch;
  }

  .entry-actions .btn {
    flex: 1;
  }

  .entry-lines {
    font-size: 0.875rem;
  }

  .entry-lines th,
  .entry-lines td {
    padding: 0.75rem 0.5rem;
  }
}

@media (max-width: 480px) {
  .journal-entries {
    padding: 0.5rem;
  }

  .entry-card {
    padding: 1rem;
  }

  .entry-lines table {
    min-width: 500px;
  }
}
</style>

