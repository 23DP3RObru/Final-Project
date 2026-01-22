<template>
  <div class="container mt-4">
    <h1 class="mb-4">Database Operations Test</h1>
    
    <!-- Status Messages -->
    <div v-if="message" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ message }}
      <button type="button" class="btn-close" @click="message = ''"></button>
    </div>
    
    <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ error }}
      <button type="button" class="btn-close" @click="error = ''"></button>
    </div>

    <!-- Create Item Card -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Create New Item</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="createItem">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="name" class="form-label">Name *</label>
              <input 
                type="text" 
                class="form-control" 
                id="name" 
                v-model="newItem.name" 
                required
                placeholder="Enter item name"
              >
            </div>
            <div class="col-md-6 mb-3">
              <label for="description" class="form-label">Description</label>
              <input 
                type="text" 
                class="form-control" 
                id="description" 
                v-model="newItem.description"
                placeholder="Enter description (optional)"
              >
            </div>
          </div>
          <button type="submit" class="btn btn-primary" :disabled="isCreating">
            {{ isCreating ? 'Creating...' : 'Create Item' }}
          </button>
        </form>
      </div>
    </div>

    <!-- Items List Card -->
    <div class="card">
      <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Items List</h5>
        <div>
          <button @click="fetchItems" class="btn btn-light btn-sm me-2" :disabled="isLoading">
            {{ isLoading ? 'Loading...' : 'Refresh' }}
          </button>
          <button @click="testAll" class="btn btn-info btn-sm">
            Run All Tests
          </button>
        </div>
      </div>
      <div class="card-body">
        <!-- Loading State -->
        <div v-if="isLoading" class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading items...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="items.length === 0" class="text-center py-4">
          <p class="text-muted">No items found. Create your first item above!</p>
        </div>

        <!-- Items Table -->
        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in items" :key="item.id">
                <td>{{ item.id }}</td>
                <td>
                  <input 
                    v-if="editingId === item.id"
                    v-model="editData.name"
                    class="form-control form-control-sm"
                  >
                  <span v-else>{{ item.name }}</span>
                </td>
                <td>
                  <input 
                    v-if="editingId === item.id"
                    v-model="editData.description"
                    class="form-control form-control-sm"
                  >
                  <span v-else>{{ item.description || 'No description' }}</span>
                </td>
                <td>{{ formatDate(item.created_at) }}</td>
                <td>
                  <div v-if="editingId === item.id">
                    <button @click="updateItem(item.id)" class="btn btn-success btn-sm me-1">
                      Save
                    </button>
                    <button @click="cancelEdit" class="btn btn-secondary btn-sm">
                      Cancel
                    </button>
                  </div>
                  <div v-else>
                    <button @click="startEdit(item)" class="btn btn-warning btn-sm me-1">
                      Edit
                    </button>
                    <button @click="deleteItem(item.id)" class="btn btn-danger btn-sm">
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- API Info Card -->
    <div class="card mt-4">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0">API Information</h5>
      </div>
      <div class="card-body">
        <p><strong>Backend URL:</strong> http://localhost:8000</p>
        <p><strong>API Base URL:</strong> http://localhost:8000/api</p>
        <p><strong>Total Items:</strong> {{ items.length }}</p>
        <div class="mt-3">
          <h6>Quick Test Commands:</h6>
          <pre class="bg-light p-3 rounded"><code>// In browser console
fetch('http://localhost:8000/api/items')
  .then(r => r.json())
  .then(console.log)</code></pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// Reactive variables
const items = ref([]);
const isLoading = ref(false);
const isCreating = ref(false);
const newItem = ref({ name: '', description: '' });
const editingId = ref(null);
const editData = ref({ name: '', description: '' });
const message = ref('');
const error = ref('');

// Load items when component mounts
onMounted(() => {
  fetchItems();
});

// Format date for display
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
};

// Fetch all items from API
const fetchItems = async () => {
  isLoading.value = true;
  error.value = '';
  try {
    const response = await fetch('http://localhost:8000/api/items');
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    items.value = await response.json();
    console.log('Items loaded:', items.value);
  } catch (err) {
    error.value = `Failed to load items: ${err.message}`;
    console.error('Error fetching items:', err);
  } finally {
    isLoading.value = false;
  }
};

// Create a new item
const createItem = async () => {
  if (!newItem.value.name.trim()) {
    error.value = 'Name is required';
    return;
  }

  isCreating.value = true;
  error.value = '';
  try {
    const response = await fetch('http://localhost:8000/api/items', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(newItem.value)
    });
    
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    
    const createdItem = await response.json();
    items.value.push(createdItem);
    message.value = `Item "${createdItem.name}" created successfully!`;
    newItem.value = { name: '', description: '' };
    
    // Clear success message after 3 seconds
    setTimeout(() => {
      message.value = '';
    }, 3000);
    
  } catch (err) {
    error.value = `Failed to create item: ${err.message}`;
    console.error('Error creating item:', err);
  } finally {
    isCreating.value = false;
  }
};

// Start editing an item
const startEdit = (item) => {
  editingId.value = item.id;
  editData.value = { name: item.name, description: item.description || '' };
};

// Cancel editing
const cancelEdit = () => {
  editingId.value = null;
  editData.value = { name: '', description: '' };
};

// Update an item
const updateItem = async (id) => {
  if (!editData.value.name.trim()) {
    error.value = 'Name cannot be empty';
    return;
  }

  try {
    const response = await fetch(`http://localhost:8000/api/items/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(editData.value)
    });
    
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    
    const updatedItem = await response.json();
    
    // Update item in the list
    const index = items.value.findIndex(item => item.id === id);
    if (index !== -1) {
      items.value[index] = updatedItem;
    }
    
    message.value = `Item "${updatedItem.name}" updated successfully!`;
    editingId.value = null;
    
    setTimeout(() => {
      message.value = '';
    }, 3000);
    
  } catch (err) {
    error.value = `Failed to update item: ${err.message}`;
    console.error('Error updating item:', err);
  }
};

// Delete an item
const deleteItem = async (id) => {
  if (!confirm('Are you sure you want to delete this item?')) {
    return;
  }

  const itemName = items.value.find(item => item.id === id)?.name || 'Item';
  
  try {
    const response = await fetch(`http://localhost:8000/api/items/${id}`, {
      method: 'DELETE',
    });
    
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    
    // Remove item from the list
    items.value = items.value.filter(item => item.id !== id);
    message.value = `Item "${itemName}" deleted successfully!`;
    
    setTimeout(() => {
      message.value = '';
    }, 3000);
    
  } catch (err) {
    error.value = `Failed to delete item: ${err.message}`;
    console.error('Error deleting item:', err);
  }
};

// Test all CRUD operations
const testAll = async () => {
  console.group('🧪 Testing All CRUD Operations');
  
  try {
    // 1. Create
    console.log('1. Testing CREATE...');
    const createRes = await fetch('http://localhost:8000/api/items', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        name: 'Test Item ' + Date.now(),
        description: 'Auto-generated test item'
      })
    });
    const created = await createRes.json();
    console.log('✅ Created:', created);
    
    // 2. Read
    console.log('2. Testing READ...');
    const readRes = await fetch(`http://localhost:8000/api/items/${created.id}`);
    const read = await readRes.json();
    console.log('✅ Read:', read);
    
    // 3. Update
    console.log('3. Testing UPDATE...');
    const updateRes = await fetch(`http://localhost:8000/api/items/${created.id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        name: 'Updated Test Item',
        description: 'This has been updated'
      })
    });
    const updated = await updateRes.json();
    console.log('✅ Updated:', updated);
    
    // 4. Delete
    console.log('4. Testing DELETE...');
    const deleteRes = await fetch(`http://localhost:8000/api/items/${created.id}`, {
      method: 'DELETE'
    });
    console.log('✅ Deleted');
    
    message.value = 'All CRUD operations tested successfully!';
    fetchItems(); // Refresh the list
    
  } catch (err) {
    console.error('❌ Test failed:', err);
    error.value = `Test failed: ${err.message}`;
  }
  
  console.groupEnd();
};
</script>

<style scoped>
.table th {
  background-color: #f8f9fa;
}
.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>