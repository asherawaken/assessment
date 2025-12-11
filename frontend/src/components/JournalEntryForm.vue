<template>
  <div class="journal-entry-form">
    <h2>{{ entry ? 'Edit' : 'Create' }} Journal Entry</h2>
    
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label>Entry Date *</label>
        <input
          type="date"
          v-model="formData.entry_date"
          required
        />
      </div>

      <div class="form-group">
        <label>Description *</label>
        <input
          type="text"
          v-model="formData.description"
          required
          placeholder="Enter description"
        />
      </div>

      <div class="form-group">
        <label>Reference Number</label>
        <input
          type="text"
          v-model="formData.reference_number"
          placeholder="Optional reference number"
        />
      </div>

      <div class="form-section">
        <div class="section-header">
          <h3>Journal Entry Lines</h3>
          <button type="button" @click="addLine" class="btn btn-sm">Add Line</button>
        </div>

        <div v-if="validationError" class="validation-error">
          {{ validationError }}
        </div>

        <div class="lines-container">
          <div v-for="(line, index) in formData.lines" :key="index" class="line-item">
            <div class="line-controls">
              <button type="button" @click="removeLine(index)" class="btn-remove">×</button>
            </div>
            <div class="line-fields">
              <div class="form-group">
                <label>Account *</label>
                <select v-model="line.account_id" required>
                  <option value="">Select Account</option>
                  <option v-for="account in accounts" :key="account.id" :value="account.id">
                    {{ account.code }} - {{ account.name }}
                  </option>
                </select>
              </div>

              <div class="form-group">
                <label>Type *</label>
                <select v-model="line.type" required>
                  <option value="">Select Type</option>
                  <option value="debit">Debit</option>
                  <option value="credit">Credit</option>
                </select>
              </div>

              <div class="form-group">
                <label>Amount *</label>
                <input
                  type="number"
                  v-model.number="line.amount"
                  step="0.01"
                  min="0.01"
                  required
                  placeholder="0.00"
                />
              </div>

              <div class="form-group">
                <label>Description</label>
                <input
                  type="text"
                  v-model="line.description"
                  placeholder="Optional line description"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="totals">
          <div class="total-item">
            <strong>Total Debits:</strong>
            <span :class="{ 'error': !isBalanced }">{{ formatCurrency(totalDebits) }}</span>
          </div>
          <div class="total-item">
            <strong>Total Credits:</strong>
            <span :class="{ 'error': !isBalanced }">{{ formatCurrency(totalCredits) }}</span>
          </div>
          <div class="total-item">
            <strong>Difference:</strong>
            <span :class="{ 'error': !isBalanced, 'success': isBalanced }">
              {{ formatCurrency(difference) }}
            </span>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" :disabled="!isBalanced || formData.lines.length < 2" class="btn btn-primary">
          {{ entry ? 'Update' : 'Create' }} Entry
        </button>
        <button type="button" @click="$emit('cancel')" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
// @ts-expect-error - API service doesn't have TypeScript definitions
import { api } from '../services/api'

interface Account {
  id: number
  code: string
  name: string
  type: string
}

interface JournalEntry {
  id: number
  entry_date: string
  description: string
  reference_number?: string | null
  lines: Array<{
    id: number
    account_id: number
    type: string
    amount: number
    description?: string | null
    account: Account
  }>
}

const props = defineProps<{
  entry: JournalEntry | null
}>()

const emit = defineEmits<{
  save: []
  cancel: []
}>()

const accounts = ref<Account[]>([])
const validationError = ref('')

interface FormLine {
  account_id: string
  type: string
  amount: string | number
  description: string
}

const totalDebits = computed(() => {
  return formData.value.lines
    .filter(line => line.type === 'debit')
    .reduce((sum, line) => sum + (parseFloat(String(line.amount)) || 0), 0)
})

const totalCredits = computed(() => {
  return formData.value.lines
    .filter(line => line.type === 'credit')
    .reduce((sum, line) => sum + (parseFloat(String(line.amount)) || 0), 0)
})

interface FormData {
  entry_date: string
  description: string
  reference_number: string
  lines: FormLine[]
}

const formData = ref<FormData>({
  entry_date: new Date().toISOString().split('T')[0] ?? '',
  description: '',
  reference_number: '',
  lines: [
    { account_id: '', type: '', amount: '', description: '' },
    { account_id: '', type: '', amount: '', description: '' }
  ]
})


const difference = computed(() => {
  return totalDebits.value - totalCredits.value
})

const isBalanced = computed(() => {
  return Math.abs(difference.value) < 0.01 && formData.value.lines.length >= 2
})

const loadAccounts = async () => {
  try {
    accounts.value = await api.getAccounts()
  } catch (err) {
    console.error('Error loading accounts:', err)
  }
}

const addLine = () => {
  formData.value.lines.push({
    account_id: '',
    type: '',
    amount: '',
    description: ''
  })
}

const removeLine = (index: number) => {
  if (formData.value.lines.length > 2) {
    formData.value.lines.splice(index, 1)
  } else {
    alert('At least 2 lines are required for a journal entry')
  }
}

const handleSubmit = async () => {
  if (!isBalanced.value) {
    validationError.value = 'Total debits must equal total credits'
    return
  }

  validationError.value = ''

  const payload = {
    ...formData.value,
    lines: formData.value.lines.map(line => ({
      account_id: parseInt(line.account_id),
      type: line.type,
      amount: parseFloat(String(line.amount)),
      description: line.description || null
    }))
  }

  try {
    if (props.entry) {
      await api.updateJournalEntry(props.entry.id, payload)
    } else {
      await api.createJournalEntry(payload)
    }
    emit('save')
  } catch (err: unknown) {
    validationError.value = err instanceof Error ? err.message : 'An error occurred'
  }
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount)
}

watch(() => props.entry, (newEntry: JournalEntry | null) => {
  if (newEntry) {
    formData.value = {
      entry_date: newEntry.entry_date,
      description: newEntry.description,
      reference_number: newEntry.reference_number ?? '',
      lines: newEntry.lines.map((line: { account_id: number; type: string; amount: number; description?: string | null }) => ({
        account_id: line.account_id.toString(),
        type: line.type,
        amount: line.amount.toString(),
        description: line.description || ''
      }))
    }
  } else {
    formData.value = {
      entry_date: new Date().toISOString().split('T')[0] ?? '',
      description: '',
      reference_number: '',
      lines: [
        { account_id: '', type: '', amount: '', description: '' },
        { account_id: '', type: '', amount: '', description: '' }
      ]
    }
  }
}, { immediate: true })

onMounted(() => {
  loadAccounts()
})
</script>

<style scoped>
.journal-entry-form {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.journal-entry-form h2 {
  margin-top: 0;
  margin-bottom: 2rem;
  color: #2c3e50;
  font-size: 1.75rem;
  font-weight: 700;
  padding-bottom: 1rem;
  border-bottom: 2px solid #f0f0f0;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #34495e;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e0e0e0;
  border-radius: 6px;
  font-size: 1rem;
  color: #2c3e50;
  transition: all 0.3s ease;
  background: white;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #42b983;
  box-shadow: 0 0 0 3px rgba(66, 185, 131, 0.1);
}

.form-group input::placeholder {
  color: #bdc3c7;
}

.form-section {
  margin-top: 2.5rem;
  padding-top: 2rem;
  border-top: 3px solid #f0f0f0;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.section-header h3 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.25rem;
  font-weight: 600;
}

.lines-container {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.line-item {
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  padding: 1.5rem;
  position: relative;
  background: #fafafa;
  transition: all 0.3s ease;
}

.line-item:hover {
  border-color: #42b983;
  background: #f8fff9;
}

.line-controls {
  position: absolute;
  top: 1rem;
  right: 1rem;
}

.btn-remove {
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  cursor: pointer;
  font-size: 1.5rem;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-remove:hover {
  background: #c0392b;
  transform: scale(1.1);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.line-fields {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 2fr;
  gap: 1.5rem;
}

.validation-error {
  background: linear-gradient(135deg, #fee 0%, #fdd 100%);
  color: #721c24;
  padding: 1rem 1.25rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  border: 2px solid #e74c3c;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.validation-error::before {
  content: "⚠";
  font-size: 1.25rem;
}

.totals {
  margin-top: 2rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-radius: 8px;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  border: 2px solid #e0e0e0;
}

.total-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem;
  background: white;
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.total-item strong {
  margin-bottom: 0.75rem;
  color: #7f8c8d;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.total-item span {
  font-size: 1.5rem;
  font-weight: 700;
  font-family: 'Courier New', monospace;
  color: #2c3e50;
}

.total-item .error {
  color: #e74c3c;
}

.total-item .success {
  color: #27ae60;
}

.form-actions {
  margin-top: 2.5rem;
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding-top: 2rem;
  border-top: 2px solid #f0f0f0;
  flex-wrap: wrap;
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

.btn-primary:disabled {
  background-color: #bdc3c7;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.btn-secondary {
  background-color: #95a5a6;
  color: white;
}

.btn-secondary:hover {
  background-color: #7f8c8d;
}

.btn-sm {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .line-fields {
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }
}

@media (max-width: 768px) {
  .journal-entry-form {
    padding: 1.5rem;
  }

  .line-fields {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .totals {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .form-actions {
    flex-direction: column;
  }

  .btn {
    width: 100%;
  }

  .section-header {
    flex-direction: column;
    align-items: stretch;
  }
}

@media (max-width: 480px) {
  .journal-entry-form {
    padding: 1rem;
  }

  .journal-entry-form h2 {
    font-size: 1.5rem;
  }

  .line-item {
    padding: 1rem;
  }

  .total-item span {
    font-size: 1.25rem;
  }
}
</style>

