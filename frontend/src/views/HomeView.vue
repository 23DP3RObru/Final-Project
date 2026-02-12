<template>
    <div class="minimal-marketplace">
        <div class="hero-section">
            <h1 class="hero-title">ProbablyLegit</h1>
            <p class="hero-subtitle">Pērciet un pārdodiet priekšmetus viegli un droši.</p>
            <div class="hero-actions">
                <router-link to="/items" class="btn btn-outline">
                    <i class="bi bi-shop"></i> Pārlūkot preces
                </router-link>
                <router-link to="/items" class="btn btn-solid">
                    <i class="bi bi-plus-circle"></i> Pardot preci
                </router-link>
            </div>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <h3>Vienkārša pārdošana</h3>
                <p>Ievieto savus priekšmetus pārdošanā dažu minūšu laikā, izmantojot mūsu vienkāršo formu.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-search"></i>
                </div>
                <h3>Viedie filtri</h3>
                <p>Atrodi tieši to, ko meklē, ar mūsu uzlabotajiem filtriem.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3>Droša platforma</h3>
                <p>Veidota ar Laravel un Vue.js, lai nodrošinātu drošu un uzticamu pieredzi.</p>
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-header">
                <h4>Tirgus statistika</h4>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-value">{{ itemsCount }}</span>
                    <span class="stat-label">Piedāvātie priekšmeti</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">{{ formatCurrency(totalValue) }}</span>
                    <span class="stat-label">Kopējā vērtība</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">{{ categoriesCount }}</span>
                    <span class="stat-label">Kategorijas</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">24/7</span>
                    <span class="stat-label">Pieejamība</span>
                </div>
            </div>
        </div>

        <div class="steps-section">
            <h2 class="steps-title">Kā tas darbojas</h2>
            <div class="steps-grid">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h5>Ievieto priekšmetus</h5>
                        <p>Aizpildi vienkāršo formu ar priekšmeta detaļām, cenu un piegādes informāciju.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h5>Pircēji pārlūko</h5>
                        <p>Pircēji var meklēt, filtrēt un atrast priekšmetus, kurus vēlas iegādāties.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h5>Pabeidz darījumus</h5>
                        <p>Pircēji pievieno priekšmetus grozam un pabeidz pirkumu (demonstrācijas režīms).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const itemsCount = ref(0);
const totalValue = ref(0);
const categoriesCount = ref(6);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
};

onMounted(async () => {
    try {
        const response = await fetch('http://localhost:8000/api/items');
        if (response.ok) {
            const items = await response.json();
            itemsCount.value = items.length;
            totalValue.value = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        }
    } catch (error) {
        console.error('Failed to fetch stats:', error);
        itemsCount.value = 12;
        totalValue.value = 1890;
    }
});
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.minimal-marketplace {
    max-width: 1280px;
    margin: 0 auto;
    padding: 5rem 2rem;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    background-color: #fcfcf9;
    color: #1e1e1e;
}

h1, h2, h3, h4, h5, p {
    margin: 0;
    font-weight: 350;
    letter-spacing: -0.01em;
}

.hero-title {
    font-size: 3.8rem;
    font-weight: 200;
    letter-spacing: -0.03em;
    line-height: 1.1;
    color: #181818;
    margin-bottom: 0.75rem;
}

.hero-subtitle {
    font-size: 1.4rem;
    font-weight: 320;
    color: #3a3a3a;
    margin-bottom: 2rem;
}

.hero-section {
    text-align: center;
    padding: 1.5rem 0 4rem 0;
}

.hero-actions {
    display: flex;
    gap: 1.2rem;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.75rem 2.2rem;
    border-radius: 40px;
    font-size: 1rem;
    font-weight: 400;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1.5px solid transparent;
    letter-spacing: 0.3px;
}

.btn i {
    font-size: 1.2rem;
    color: currentColor;
}

.btn-outline {
    border: 1.5px solid #2a2a2a;
    background: transparent;
    color: #1e1e1e;
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

.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    margin: 5rem 0;
}

.feature-card {
    background: #ffffff;
    padding: 2.5rem 1.8rem;
    border-radius: 32px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.02);
    transition: all 0.2s;
    text-align: center;
}

.feature-card:hover {
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.04);
    transform: translateY(-2px);
}

.feature-icon {
    margin-bottom: 1.5rem;
}

.feature-icon i {
    font-size: 2.8rem;
    color: #2c2c2c;
    opacity: 0.85;
    transition: opacity 0.2s;
}

.feature-card:hover .feature-icon i {
    opacity: 1;
}

.feature-card h3 {
    font-size: 1.5rem;
    font-weight: 380;
    margin-bottom: 0.75rem;
    color: #1c1c1c;
    letter-spacing: -0.02em;
}

.feature-card p {
    color: #5a5a5a;
    line-height: 1.6;
    font-size: 0.98rem;
    font-weight: 350;
}

.stats-card {
    background: white;
    border-radius: 28px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
    margin: 4rem 0;
    border: none;
    overflow: hidden;
}

.stats-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #ececec;
}

.stats-header h4 {
    font-size: 1.3rem;
    font-weight: 400;
    color: #1e1e1e;
    margin: 0;
    letter-spacing: -0.01em;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    padding: 2rem 1.5rem;
    gap: 1rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.stat-value {
    font-size: 2.6rem;
    font-weight: 220;
    line-height: 1.1;
    color: #1a1a1a;
    margin-bottom: 0.3rem;
    letter-spacing: -0.02em;
}

.stat-label {
    font-size: 0.9rem;
    color: #6b6b6b;
    font-weight: 340;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.steps-section {
    margin-top: 5rem;
}

.steps-title {
    font-size: 2rem;
    font-weight: 280;
    text-align: center;
    margin-bottom: 3rem;
    color: #1d1d1d;
    letter-spacing: -0.02em;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2.5rem;
}

.step-item {
    display: flex;
    gap: 1.2rem;
    align-items: flex-start;
}

.step-number {
    flex-shrink: 0;
    width: 56px;
    height: 56px;
    background: #ececec;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 350;
    color: #1e1e1e;
    box-shadow: inset 0 0 0 1px rgba(0,0,0,0.03);
}

.step-content h5 {
    font-size: 1.2rem;
    font-weight: 420;
    margin-bottom: 0.4rem;
    color: #222;
    letter-spacing: -0.01em;
}

.step-content p {
    color: #5b5b5b;
    line-height: 1.6;
    font-size: 0.95rem;
}

@media (max-width: 992px) {
    .features-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .steps-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }
}

@media (max-width: 768px) {
    .minimal-marketplace {
        padding: 3rem 1.5rem;
    }
    .hero-title {
        font-size: 2.8rem;
    }
    .features-grid,
    .steps-grid {
        grid-template-columns: 1fr;
    }
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1.8rem;
    }
    .hero-actions {
        flex-direction: column;
        width: 100%;
    }
    .btn {
        width: 100%;
        justify-content: center;
    }
}

.bi {
    color: inherit;
}
</style>