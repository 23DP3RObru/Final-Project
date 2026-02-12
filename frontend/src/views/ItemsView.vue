<template>
  <div class="marketplace">
    <div class="marketplace-header">
      <div class="header-info">
        <h1 class="page-title">Tirgojums</h1>
        <p class="page-subtitle">Pērciet un pārdodiet preces tiešsaistē</p>
      </div>
      <div class="header-action">
        <button @click="showCreateForm = !showCreateForm" class="btn btn-outline">
          {{ showCreateForm ? 'Atcelt' : 'Pārdot preci' }}
        </button>
      </div>
    </div>

    <div v-if="showCreateForm" class="form-card">
      <div class="form-card-header">
        <h5>Uzskaitīt preci pārdošanai</h5>
      </div>
      <div class="form-card-body">
        <form @submit.prevent="createItem">
          <div class="form-grid">
            <div class="form-section">
              <h6>Pamatinformācija</h6>
              <div class="field">
                <label for="name">Preces nosaukums *</label>
                <input 
                  type="text" 
                  id="name" 
                  v-model="newItem.name" 
                  required
                  placeholder="Ko jūs pārdodat?"
                >
              </div>
              <div class="field">
                <label for="category">Kategorija</label>
                <select id="category" v-model="newItem.category">
                  <option value="">Izvēlieties kategoriju</option>
                  <option value="electronics">Elektronika</option>
                  <option value="clothing">Apģērbs</option>
                  <option value="books">Grāmatas</option>
                  <option value="home">Mājas un Dārzs</option>
                  <option value="sports">Sports</option>
                  <option value="other">Cits</option>
                </select>
              </div>
              <div class="field">
                <label for="condition">Stāvoklis</label>
                <select id="condition" v-model="newItem.condition">
                  <option value="">Izvēlieties stāvokli</option>
                  <option value="new">Jauns</option>
                  <option value="used">Lietots</option>
                  <option value="refurbished">Atjaunots</option>
                </select>
              </div>
              <div class="field">
                <label for="description">Apraksts</label>
                <textarea 
                  id="description" 
                  v-model="newItem.description"
                  rows="3"
                  placeholder="Aprakstiet savu preci..."
                ></textarea>
              </div>
            </div>

            <div class="form-section">
              <h6>Cena un Piegāde</h6>
              <div class="field-group">
                <div class="field half">
                  <label for="price">Cena (€) *</label>
                  <div class="input-symbol">
                    <span class="symbol">€</span>
                    <input 
                      type="number" 
                      id="price" 
                      v-model="newItem.price" 
                      required
                      min="0"
                      step="0.01"
                      placeholder="0.00"
                    >
                  </div>
                </div>
                <div class="field half">
                  <label for="quantity">Daudzums *</label>
                  <input 
                    type="number" 
                    id="quantity" 
                    v-model="newItem.quantity" 
                    required
                    min="0"
                    placeholder="Cik daudz?"
                  >
                </div>
              </div>
              <div class="field">
                <label for="shipping_type">Piegādes veids *</label>
                <select id="shipping_type" v-model="newItem.shipping_type" required>
                  <option value="free">Bezmaksas piegāde</option>
                  <option value="flat_rate">Fiksēta piegādes cena</option>
                  <option value="calculated">Aprēķināta piegāde</option>
                </select>
              </div>
              <div v-if="newItem.shipping_type !== 'free'" class="field">
                <label for="shipping_cost">
                  Piegādes izmaksas (€)
                  <span v-if="newItem.shipping_type === 'flat_rate'">*</span>
                </label>
                <div class="input-symbol">
                  <span class="symbol">€</span>
                  <input 
                    type="number" 
                    id="shipping_cost" 
                    v-model="newItem.shipping_cost" 
                    :required="newItem.shipping_type === 'flat_rate'"
                    min="0"
                    step="0.01"
                    placeholder="0.00"
                  >
                </div>
                <small v-if="newItem.shipping_type === 'calculated'">
                  Piegādes izmaksas tiks aprēķinātas pēc pircēja atrašanās vietas
                </small>
              </div>

              <div class="summary-card">
                <h6>Cenas kopsavilkums</h6>
                <div class="summary-row">
                  <span>Preces cena:</span>
                  <span>{{ formatCurrency(newItem.price || 0) }}</span>
                </div>
                <div class="summary-row">
                  <span>Piegāde:</span>
                  <span>{{ calculateShipping() }}</span>
                </div>
                <hr class="divider">
                <div class="summary-row total">
                  <span>Kopā:</span>
                  <span>{{ formatCurrency(calculateTotal()) }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" @click="showCreateForm = false" class="btn btn-outline">
              Atcelt
            </button>
            <button type="submit" class="btn btn-solid" :disabled="creating">
              {{ creating ? 'Uzskaitīju preci...' : 'Uzskaitīt preci pārdošanai' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="marketplace-grid">
      <aside class="filters-sidebar">
        <div class="filters-card">
          <div class="filters-header">
            <h6>Filtri</h6>
          </div>
          <div class="filters-body">
            <div class="filter-group">
              <label for="search">Meklēt</label>
              <input 
                type="text" 
                id="search" 
                v-model="filters.search"
                placeholder="Meklēt preces..."
              >
            </div>

            <div class="filter-group">
              <label>Kategorija</label>
              <div class="checkbox-group">
                <div v-for="category in categories" :key="category" class="checkbox-item">
                  <input 
                    type="checkbox" 
                    :id="`cat-${category}`"
                    :value="category"
                    v-model="filters.categories"
                  >
                  <label :for="`cat-${category}`">
                    {{ translateCategory(category) }}
                  </label>
                </div>
              </div>
            </div>

            <div class="filter-group">
              <label>Cenu diapazons</label>
              <div class="price-range">
                <input 
                  type="number" 
                  placeholder="Min" 
                  v-model="filters.minPrice"
                  min="0"
                >
                <input 
                  type="number" 
                  placeholder="Maks" 
                  v-model="filters.maxPrice"
                  min="0"
                >
              </div>
            </div>

            <div class="filter-group">
              <label for="filter-condition">Stāvoklis</label>
              <select id="filter-condition" v-model="filters.condition">
                <option value="">Visi stāvokļi</option>
                <option value="new">Jauns</option>
                <option value="used">Lietots</option>
                <option value="refurbished">Atjaunots</option>
              </select>
            </div>

            <div class="filter-group">
              <label for="filter-shipping">Piegāde</label>
              <select id="filter-shipping" v-model="filters.shippingType">
                <option value="">Visi piegādes veidi</option>
                <option value="free">Bezmaksas piegāde</option>
                <option value="flat_rate">Fiksēta cena</option>
                <option value="calculated">Aprēķināta</option>
              </select>
            </div>

            <button @click="clearFilters" class="btn btn-outline btn-small full-width">
              Notīrīt filtrus
            </button>
          </div>
        </div>
      </aside>

      <section class="items-section">
        <div v-if="loading" class="state-empty">
          <div class="spinner"></div>
          <p>Ielādes tirgojuma preces...</p>
        </div>

        <div v-else-if="filteredItems.length === 0" class="state-empty">
          <div class="empty-icon">
            <i class="bi bi-box-seam"></i>
          </div>
          <h5>Preces netika atrastas</h5>
          <p>Mēģiniet pielāgot filtrus vai uzskaitīt jaunu preci pārdošanai.</p>
          <button @click="showCreateForm = true" class="btn btn-solid">
            Pārdodiet savu pirmo preci
          </button>
        </div>

        <div v-else class="items-grid">
          <div v-for="item in filteredItems" :key="item.id" class="item-card">
            <div class="item-card-content">
              <div class="item-header">
                <div class="item-badges">
                  <span class="badge badge-category">{{ translateCategory(item.category) || 'Bez kategorijas' }}</span>
                  <span class="badge" :class="conditionClass(item.condition)">
                    {{ translateCondition(item.condition) || 'N/A' }}
                  </span>
                </div>
                <div class="item-menu">
                  <button class="menu-dots" type="button" @click="toggleMenu(item.id)">
                    <i class="bi bi-three-dots"></i>
                  </button>
                  <div v-if="activeMenuId === item.id" class="dropdown-menu">
                    <button @click="startEdit(item)" class="dropdown-item">Rediģēt</button>
                    <button @click="deleteItem(item.id)" class="dropdown-item text-danger">Dzēst</button>
                  </div>
                </div>
              </div>

              <h5 class="item-title">{{ item.name }}</h5>
              <p class="item-description">
                {{ truncateDescription(item.description) || 'Nav apraksta' }}
              </p>

              <div class="item-pricing">
                <div class="price">
                  <span class="amount">{{ formatCurrency(item.price) }}</span>
                  <span class="unit">par vienu</span>
                </div>
                <div class="shipping">
                  <span v-if="item.shipping_type === 'free'">🚚 Bezmaksas piegāde</span>
                  <span v-else-if="item.shipping_type === 'flat_rate'">
                    📦 Piegāde: {{ formatCurrency(item.shipping_cost) }}
                  </span>
                  <span v-else>📦 Aprēķināta piegāde</span>
                </div>
              </div>

              <div class="item-footer">
                <span class="stock" :class="stockClass(item.quantity)">
                  {{ item.quantity }} uz krājuma
                </span>
                <button @click="addToCart(item)" class="btn btn-outline btn-small">
                  <i class="bi bi-cart-plus"></i> Pievienot
                </button>
              </div>
            </div>

            <div v-if="editingId === item.id" class="edit-form">
              <form @submit.prevent="updateItem(item.id)">
                <div class="edit-grid">
                  <input type="text" v-model="editData.name" placeholder="Nosaukums" required>
                  <input type="number" v-model="editData.price" placeholder="Cena" min="0" step="0.01" required>
                  <input type="number" v-model="editData.quantity" placeholder="Daudzums" min="0" required>
                  <select v-model="editData.shipping_type">
                    <option value="free">Bezmaksas</option>
                    <option value="flat_rate">Fiksēta</option>
                    <option value="calculated">Aprēķināta</option>
                  </select>
                  <div class="edit-actions">
                    <button type="submit" class="btn btn-small btn-solid">Saglabāt</button>
                    <button type="button" @click="cancelEdit" class="btn btn-small btn-outline">Atcelt</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>
    </div>

    <div v-if="cart.length > 0" class="cart-summary">
      <div class="cart-container">
        <div class="cart-info">
          <span class="cart-count">{{ cart.length }} preces grozā</span>
          <span class="cart-total">Kopā: {{ formatCurrency(cartTotal) }}</span>
        </div>
        <div class="cart-actions">
          <button @click="clearCart" class="btn btn-outline btn-small">Notīrīt grozu</button>
          <button @click="checkout" class="btn btn-solid">Norēķināties</button>
        </div>
      </div>
    </div>

    <div v-if="message" class="message success">
      {{ message }}
      <button class="message-close" @click="message = ''">×</button>
    </div>
    <div v-if="error" class="message error">
      {{ error }}
      <button class="message-close" @click="error = ''">×</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const items = ref([]);
const loading = ref(false);
const creating = ref(false);
const showCreateForm = ref(false);
const editingId = ref(null);
const message = ref('');
const error = ref('');
const cart = ref([]);
const activeMenuId = ref(null);

const newItem = ref({
  name: '',
  description: '',
  price: 0,
  quantity: 1,
  shipping_type: 'flat_rate',
  shipping_cost: 0,
  condition: '',
  category: ''
});

const editData = ref({
  name: '',
  price: 0,
  quantity: 0,
  shipping_type: 'flat_rate'
});

const filters = ref({
  search: '',
  categories: [],
  minPrice: '',
  maxPrice: '',
  condition: '',
  shippingType: ''
});

const categories = ref(['electronics', 'clothing', 'books', 'home', 'sports', 'other']);

onMounted(() => {
  fetchItems();
});

const filteredItems = computed(() => {
  return items.value.filter(item => {
    if (filters.value.search) {
      const searchTerm = filters.value.search.toLowerCase();
      if (!item.name.toLowerCase().includes(searchTerm) && 
          !item.description?.toLowerCase().includes(searchTerm)) {
        return false;
      }
    }
    if (filters.value.categories.length > 0 && item.category) {
      if (!filters.value.categories.includes(item.category)) {
        return false;
      }
    }
    if (filters.value.minPrice && item.price < parseFloat(filters.value.minPrice)) {
      return false;
    }
    if (filters.value.maxPrice && item.price > parseFloat(filters.value.maxPrice)) {
      return false;
    }
    if (filters.value.condition && item.condition !== filters.value.condition) {
      return false;
    }
    if (filters.value.shippingType && item.shipping_type !== filters.value.shippingType) {
      return false;
    }
    return true;
  });
});

const totalValue = computed(() => {
  return filteredItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
});

const totalQuantity = computed(() => {
  return filteredItems.value.reduce((sum, item) => sum + item.quantity, 0);
});

const cartTotal = computed(() => {
  return cart.value.reduce((sum, item) => sum + item.price, 0);
});

const categoryTranslations = {
  'electronics': 'Elektronika',
  'clothing': 'Apģērbs',
  'books': 'Grāmatas',
  'home': 'Mājas un Dārzs',
  'sports': 'Sports',
  'other': 'Cits'
};

const conditionTranslations = {
  'new': 'Jauns',
  'used': 'Lietots',
  'refurbished': 'Atjaunots'
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('lv-LV', {
    style: 'currency',
    currency: 'EUR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount);
};

const translateCategory = (category) => {
  return categoryTranslations[category] || category;
};

const translateCondition = (condition) => {
  return conditionTranslations[condition] || condition;
};

const truncateDescription = (text, length = 60) => {
  if (!text) return 'Nav apraksta';
  return text.length > length ? text.substring(0, length) + '...' : text;
};

const calculateShipping = () => {
  if (newItem.value.shipping_type === 'free') return 'Bezmaksas';
  if (newItem.value.shipping_type === 'flat_rate' && newItem.value.shipping_cost) {
    return formatCurrency(newItem.value.shipping_cost);
  }
  return 'Aprēķināts';
};

const calculateTotal = () => {
  let total = parseFloat(newItem.value.price) || 0;
  if (newItem.value.shipping_type === 'flat_rate' && newItem.value.shipping_cost) {
    total += parseFloat(newItem.value.shipping_cost);
  }
  return total;
};

const conditionClass = (condition) => {
  switch (condition) {
    case 'new': return 'badge-new';
    case 'used': return 'badge-used';
    case 'refurbished': return 'badge-refurbished';
    default: return 'badge-default';
  }
};

const stockClass = (quantity) => {
  if (quantity === 0) return 'stock-out';
  if (quantity < 5) return 'stock-low';
  return 'stock-ok';
};

const fetchItems = async () => {
  loading.value = true;
  error.value = '';
  try {
    const response = await fetch('http://localhost:8000/api/items');
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    items.value = await response.json();
  } catch (err) {
    error.value = `Preču ielāde neizdevās: ${err.message}`;
    console.error('Error fetching items:', err);
  } finally {
    loading.value = false;
  }
};

const createItem = async () => {
  if (!newItem.value.name.trim()) {
    error.value = 'Preces nosaukums ir obligāts';
    return;
  }
  creating.value = true;
  error.value = '';
  try {
    const response = await fetch('http://localhost:8000/api/items', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newItem.value)
    });
    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.errors ? JSON.stringify(errorData.errors) : `HTTP ${response.status}`);
    }
    const createdItem = await response.json();
    items.value.push(createdItem);
    message.value = `"${createdItem.name}" sekmīgi uzskaitīta pārdošanai!`;
    newItem.value = {
      name: '',
      description: '',
      price: 0,
      quantity: 1,
      shipping_type: 'flat_rate',
      shipping_cost: 0,
      condition: '',
      category: ''
    };
    showCreateForm.value = false;
    setTimeout(() => { message.value = ''; }, 3000);
  } catch (err) {
    error.value = `Preču uzskaitīšana neizdevās: ${err.message}`;
    console.error('Error creating item:', err);
  } finally {
    creating.value = false;
  }
};

const startEdit = (item) => {
  editingId.value = item.id;
  activeMenuId.value = null;
  editData.value = {
    name: item.name,
    price: item.price,
    quantity: item.quantity,
    shipping_type: item.shipping_type
  };
};

const cancelEdit = () => {
  editingId.value = null;
  editData.value = { name: '', price: 0, quantity: 0, shipping_type: 'flat_rate' };
};

const updateItem = async (id) => {
  try {
    const response = await fetch(`http://localhost:8000/api/items/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(editData.value)
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    const updatedItem = await response.json();
    const index = items.value.findIndex(item => item.id === id);
    if (index !== -1) items.value[index] = updatedItem;
    message.value = `Prece "${updatedItem.name}" sekmīgi atjaunināta!`;
    editingId.value = null;
    setTimeout(() => { message.value = ''; }, 3000);
  } catch (err) {
    error.value = `Preču atjaunināšana neizdevās: ${err.message}`;
    console.error('Error updating item:', err);
  }
};

const deleteItem = async (id) => {
  if (!confirm('Vai esat pārliecināts, ka vēlaties noņemt šo preci no tirgojuma?')) return;
  const itemName = items.value.find(item => item.id === id)?.name || 'Prece';
  try {
    const response = await fetch(`http://localhost:8000/api/items/${id}`, { method: 'DELETE' });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    items.value = items.value.filter(item => item.id !== id);
    message.value = `Prece "${itemName}" noņemta no tirgojuma!`;
    setTimeout(() => { message.value = ''; }, 3000);
  } catch (err) {
    error.value = `Preču dzēšana neizdevās: ${err.message}`;
    console.error('Error deleting item:', err);
  }
};

const addToCart = (item) => {
  if (item.quantity === 0) {
    error.value = 'Šī prece ir beigusies!';
    return;
  }
  cart.value.push({ ...item });
  message.value = `"${item.name}" pievienota grozam!`;
  const index = items.value.findIndex(i => i.id === item.id);
  if (index !== -1) items.value[index].quantity -= 1;
  setTimeout(() => { message.value = ''; }, 2000);
};

const clearCart = () => {
  cart.value = [];
  message.value = 'Grozs notīrīts!';
  setTimeout(() => { message.value = ''; }, 2000);
};

const checkout = () => {
  alert(`Norēķināšanās pabeigta! Kopā: ${formatCurrency(cartTotal.value)}\n\nŠis ir demonstrējums - īstā lietojumprogrammā jūs apstrādātu maksājumu šeit.`);
  cart.value = [];
};

const clearFilters = () => {
  filters.value = {
    search: '',
    categories: [],
    minPrice: '',
    maxPrice: '',
    condition: '',
    shippingType: ''
  };
};

const toggleMenu = (id) => {
  activeMenuId.value = activeMenuId.value === id ? null : id;
};

window.addEventListener('click', (e) => {
  if (!e.target.closest('.item-menu')) {
    activeMenuId.value = null;
  }
});
</script>

<style scoped>
.marketplace {
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem 2rem 5rem;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  background: #fbfbf9;
  color: #1e1e1e;
}

h1, h2, h3, h4, h5, h6, p {
  margin: 0;
  font-weight: 350;
  letter-spacing: -0.01em;
}

.page-title {
  font-size: 2.6rem;
  font-weight: 220;
  letter-spacing: -0.03em;
  color: #1a1a1a;
  margin-bottom: 0.3rem;
}

.page-subtitle {
  font-size: 1.1rem;
  color: #5a5a5a;
  font-weight: 330;
}

.marketplace-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #ececec;
}

.header-action {
  display: flex;
  gap: 1rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.8rem;
  border-radius: 40px;
  font-size: 0.95rem;
  font-weight: 400;
  text-decoration: none;
  transition: all 0.2s ease;
  border: 1.5px solid transparent;
  cursor: pointer;
  background: transparent;
  letter-spacing: 0.2px;
}

.btn-outline {
  border: 1.5px solid #2a2a2a;
  color: #1e1e1e;
  background: transparent;
}

.btn-outline:hover {
  background: #1a1a1a;
  color: white;
  border-color: #1a1a1a;
}

.btn-solid {
  background: #1e1e1e;
  border: 1.5px solid #1e1e1e;
  color: white;
}

.btn-solid:hover {
  background: #000;
  border-color: #000;
}

.btn-small {
  padding: 0.4rem 1.2rem;
  font-size: 0.85rem;
}

.full-width {
  width: 100%;
  justify-content: center;
}

.form-card {
  background: white;
  border: 1px solid #efefef;
  border-radius: 28px;
  margin-bottom: 3rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}

.form-card-header {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #f0f0f0;
}

.form-card-header h5 {
  font-size: 1.2rem;
  font-weight: 400;
  color: #222;
}

.form-card-body {
  padding: 2rem;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2.5rem;
  margin-bottom: 2rem;
}

.form-section h6 {
  font-size: 1rem;
  font-weight: 450;
  margin-bottom: 1.5rem;
  color: #2a2a2a;
  letter-spacing: -0.01em;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.5rem;
}

.field {
  margin-bottom: 1.2rem;
  display: flex;
  flex-direction: column;
}

.field label {
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #6a6a6a;
  margin-bottom: 0.3rem;
  font-weight: 450;
}

.field input,
.field select,
.field textarea {
  border: 1px solid #e2e2e2;
  border-radius: 14px;
  padding: 0.7rem 1rem;
  font-size: 0.95rem;
  background: white;
  transition: border 0.15s;
  font-family: inherit;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
  outline: none;
  border-color: #8a8a8a;
}

.field-group {
  display: flex;
  gap: 1rem;
}

.field.half {
  flex: 1;
}

.input-symbol {
  display: flex;
  align-items: center;
  border: 1px solid #e2e2e2;
  border-radius: 14px;
  overflow: hidden;
}

.input-symbol .symbol {
  padding: 0.7rem 0 0.7rem 1rem;
  background: #f9f9f9;
  color: #5a5a5a;
  border-right: 1px solid #e2e2e2;
  font-size: 0.9rem;
}

.input-symbol input {
  border: none;
  border-radius: 0;
  flex: 1;
  padding: 0.7rem 1rem;
}

.summary-card {
  background: #f9f9f9;
  border-radius: 18px;
  padding: 1.2rem;
  margin-top: 1rem;
}

.summary-card h6 {
  border-bottom: none;
  padding-bottom: 0;
  margin-bottom: 0.8rem;
  font-size: 0.9rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.9rem;
  color: #4a4a4a;
  padding: 0.2rem 0;
}

.summary-row.total {
  font-weight: 500;
  color: #1e1e1e;
  font-size: 1rem;
}

.divider {
  margin: 0.6rem 0;
  border: none;
  border-top: 1px solid #e0e0e0;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #f0f0f0;
}

.marketplace-grid {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 2.5rem;
}

.filters-card {
  background: white;
  border: 1px solid #efefef;
  border-radius: 24px;
  overflow: hidden;
}

.filters-header {
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

.filters-header h6 {
  font-size: 1rem;
  font-weight: 430;
  color: #2a2a2a;
}

.filters-body {
  padding: 1.5rem;
}

.filter-group {
  margin-bottom: 1.8rem;
}

.filter-group label {
  display: block;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: #777;
  margin-bottom: 0.6rem;
  font-weight: 500;
}

.filter-group input[type="text"],
.filter-group input[type="number"],
.filter-group select {
  width: 100%;
  padding: 0.6rem 1rem;
  border: 1px solid #e2e2e2;
  border-radius: 12px;
  font-size: 0.9rem;
  background: white;
}

.price-range {
  display: flex;
  gap: 0.5rem;
}

.checkbox-group {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.checkbox-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.checkbox-item input[type="checkbox"] {
  width: 16px;
  height: 16px;
  border: 1.5px solid #aaa;
  border-radius: 3px;
  accent-color: #1e1e1e;
}

.checkbox-item label {
  text-transform: none;
  letter-spacing: normal;
  color: #4a4a4a;
  font-size: 0.9rem;
  font-weight: 350;
}

.items-section {
  min-height: 400px;
}

.items-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 1.8rem;
}

.item-card {
  background: white;
  border: 1px solid #efefef;
  border-radius: 24px;
  transition: all 0.2s;
  display: flex;
  flex-direction: column;
}

.item-card:hover {
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
  transform: translateY(-3px);
}

.item-card-content {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.item-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.badge {
  font-size: 0.65rem;
  padding: 0.25rem 0.7rem;
  border-radius: 20px;
  background: #f1f1f1;
  color: #3a3a3a;
  font-weight: 430;
  letter-spacing: 0.2px;
  text-transform: uppercase;
}

.badge-category {
  background: #eaeaea;
}

.badge-new { background: #e0e0e0; }
.badge-used { background: #d6d6d6; }
.badge-refurbished { background: #cacaca; }
.badge-default { background: #efefef; }

.item-menu {
  position: relative;
}

.menu-dots {
  background: transparent;
  border: none;
  font-size: 1.2rem;
  color: #6a6a6a;
  cursor: pointer;
  padding: 0.2rem;
}

.dropdown-menu {
  position: absolute;
  right: 0;
  top: 1.8rem;
  background: white;
  border: 1px solid #efefef;
  border-radius: 14px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.04);
  min-width: 130px;
  z-index: 10;
}

.dropdown-item {
  display: block;
  width: 100%;
  padding: 0.6rem 1rem;
  text-align: left;
  background: none;
  border: none;
  font-size: 0.85rem;
  color: #2e2e2e;
  cursor: pointer;
}

.dropdown-item:hover {
  background: #f5f5f5;
}

.text-danger {
  color: #a33f3f;
}

.item-title {
  font-size: 1.1rem;
  font-weight: 420;
  margin-bottom: 0.5rem;
  color: #1c1c1c;
}

.item-description {
  font-size: 0.85rem;
  color: #6a6a6a;
  line-height: 1.5;
  margin-bottom: 1.2rem;
  flex: 1;
}

.item-pricing {
  margin-bottom: 1.2rem;
}

.price {
  display: flex;
  align-items: baseline;
  gap: 0.3rem;
  margin-bottom: 0.2rem;
}

.amount {
  font-size: 1.4rem;
  font-weight: 280;
  color: #1a1a1a;
}

.unit {
  font-size: 0.75rem;
  color: #888;
}

.shipping {
  font-size: 0.8rem;
  color: #6b6b6b;
}

.item-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0.5rem;
}

.stock {
  font-size: 0.7rem;
  padding: 0.25rem 0.8rem;
  border-radius: 20px;
  background: #f1f1f1;
  color: #4a4a4a;
  text-transform: uppercase;
  letter-spacing: 0.4px;
}

.stock-ok { background: #e6e6e6; }
.stock-low { background: #dbdbdb; }
.stock-out { background: #d1d1d1; }

.edit-form {
  border-top: 1px solid #efefef;
  padding: 1.2rem;
  background: #fbfbf9;
}

.edit-grid {
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
}

.edit-grid input,
.edit-grid select {
  padding: 0.5rem 0.8rem;
  border: 1px solid #e2e2e2;
  border-radius: 12px;
  font-size: 0.85rem;
}

.edit-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.3rem;
}

.cart-summary {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background: white;
  border-top: 1px solid #eaeaea;
  padding: 1.2rem 2rem;
  z-index: 1000;
  box-shadow: 0 -4px 12px rgba(0,0,0,0.02);
}

.cart-container {
  max-width: 1280px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-info {
  display: flex;
  gap: 1.2rem;
}

.cart-count {
  font-weight: 450;
  color: #1e1e1e;
}

.cart-total {
  color: #5a5a5a;
}

.cart-actions {
  display: flex;
  gap: 0.8rem;
}

.state-empty {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border: 1px solid #efefef;
  border-radius: 32px;
}

.empty-icon i {
  font-size: 3rem;
  color: #aaa;
  margin-bottom: 1rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 2px solid #eee;
  border-top-color: #2e2e2e;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin { to { transform: rotate(360deg); } }

.message {
  position: fixed;
  bottom: 6rem;
  right: 2rem;
  padding: 0.8rem 1.5rem;
  background: white;
  border: 1px solid #eaeaea;
  border-radius: 40px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.04);
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: 0.9rem;
  z-index: 1100;
}

.message.success { border-left: 4px solid #2e7d32; }
.message.error { border-left: 4px solid #b33f3f; }

.message-close {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  color: #888;
  padding: 0 0.2rem;
}

@media (max-width: 992px) {
  .marketplace-grid {
    grid-template-columns: 1fr;
  }
  .form-grid {
    grid-template-columns: 1fr;
  }
  .cart-container {
    flex-direction: column;
    gap: 1rem;
  }
}

@media (max-width: 768px) {
  .marketplace-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  .marketplace {
    padding: 1.5rem 1.2rem 6rem;
  }
  .form-card-body {
    padding: 1.5rem;
  }
}

.item-card {
  background: white;
  border: 1px solid #efefef;
  border-radius: 24px;
  transition: all 0.2s;
  display: flex;
  flex-direction: column;
  position: relative; /* Add this */
  z-index: 1; /* Add this - base z-index */
}

.item-card:hover {
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
  transform: translateY(-3px);
  z-index: 5; /* Add this - raise on hover */
}

.item-menu {
  position: relative;
  z-index: 20; /* Add this - higher than card */
}

.dropdown-menu {
  position: absolute;
  right: 0;
  top: 1.8rem;
  background: white;
  border: 1px solid #efefef;
  border-radius: 14px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.08);
  min-width: 130px;
  z-index: 100; /* Increase this significantly */
  display: block; /* Add this */
}

/* Add this to ensure dropdown is visible */
.item-card:has(.dropdown-menu) {
  z-index: 10; /* Raise card when its dropdown is open */
}

/* Fix potential overflow issues */
.items-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 1.8rem;
  overflow: visible !important; /* Add this */
}

.item-card-content {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: visible; /* Add this */
}

.marketplace-grid {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 2.5rem;
  overflow: visible; /* Add this */
}

.items-section {
  min-height: 400px;
  overflow: visible; /* Add this */
}

.bi {
  color: currentColor;
}
</style>