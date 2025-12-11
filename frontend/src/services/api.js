const API_BASE_URL = 'http://localhost:8000/api';

async function request(endpoint, options = {}) {
  const url = `${API_BASE_URL}${endpoint}`;
  const config = {
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    ...options,
  };

  if (options.body) {
    config.body = JSON.stringify(options.body);
  }

  const response = await fetch(url, config);
  
  if (!response.ok) {
    const error = await response.json().catch(() => ({ message: 'An error occurred' }));
    throw new Error(error.message || error.errors || 'Request failed');
  }

  return response.json();
}

export const api = {
  // Accounts
  getAccounts: () => request('/accounts'),
  createAccount: (data) => request('/accounts', { method: 'POST', body: data }),
  
  // Journal Entries
  getJournalEntries: () => request('/journal-entries'),
  getJournalEntry: (id) => request(`/journal-entries/${id}`),
  createJournalEntry: (data) => request('/journal-entries', { method: 'POST', body: data }),
  updateJournalEntry: (id, data) => request(`/journal-entries/${id}`, { method: 'PUT', body: data }),
  deleteJournalEntry: (id) => request(`/journal-entries/${id}`, { method: 'DELETE' }),
  
  // Trial Balance
  getTrialBalance: () => request('/trial-balance'),
};

