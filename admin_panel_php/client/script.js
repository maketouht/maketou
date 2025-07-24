// Liste des produits (exemple, à adapter selon vos vrais produits)
const produits = [
  {
    id: 1,
    nom: "Veste en cuir marron",
    image: "images/i001.jpg",
    prix: "79,99 €",
    description: "Veste en cuir véritable, parfaite pour l'hiver.",
    couleurs: ["#6B3F26", "#000", "#FFF"],
    tailles: ["S", "M", "L", "XL"],
    qualite: "Cuir premium",
    similaires: [2, 3, 4]
  },
  {
    id: 2,
    nom: "Chemise blanche classique",
    image: "images/i002.jpg",
    prix: "49,90 €",
    description: "Chemise élégante pour toutes occasions.",
    couleurs: ["#FFF", "#0000FF"],
    tailles: ["M", "L", "XL"],
    qualite: "Coton supérieur",
    similaires: [1, 3, 5]
  },
  // Ajoutez d'autres produits ici...
];

// Fonction pour récupérer l'ID du produit depuis l'URL
function getProductIdFromUrl() {
  const params = new URLSearchParams(window.location.search);
  return parseInt(params.get('ref'));
}

// Fonction pour charger les produits depuis produits.json
async function chargerProduitsDepuisJson() {
  try {
    const response = await fetch('produits.json');
    if (!response.ok) throw new Error('Erreur chargement produits.json');
    const data = await response.json();
    return data;
  } catch (e) {
    console.error(e);
    return produits; // fallback sur la liste JS si erreur
  }
}

// Fonction pour afficher les infos du produit
async function afficherProduit() {
  const id = getProductIdFromUrl();
  const produitsData = await chargerProduitsDepuisJson();
  const produit = produitsData.find(p => p.id === id) || produitsData[0];
  if (!produit) return;

  // Remplir les infos dans la page (exemple pour produits.html)
  if (document.getElementById('product-nom')) {
    document.getElementById('product-nom').textContent = produit.nom;
    document.getElementById('product-image').src = produit.image;
    document.getElementById('product-image').alt = produit.nom;
    document.getElementById('product-prix').textContent = produit.prix;
    document.getElementById('product-description').textContent = produit.description;
    document.getElementById('product-qualite').textContent = produit.qualite;
    // Couleurs
    const couleursDiv = document.getElementById('product-couleurs');
    couleursDiv.innerHTML = produit.couleurs.map(c => `<span class='w-6 h-6 rounded-full inline-block mr-2' style='background:${c}'></span>`).join('');
    // Tailles
    const taillesDiv = document.getElementById('product-tailles');
    taillesDiv.innerHTML = produit.tailles.map(t => `<span class='px-3 py-1 bg-gray-200 rounded mr-2'>${t}</span>`).join('');
  }
}

// Recherche intelligente sur les produits (nom, description, catégorie)
document.addEventListener('DOMContentLoaded', function() {
  const input = document.getElementById('search-input');
  const resultsDiv = document.getElementById('search-results');
  if (!input) return;

  async function fetchProduits() {
    try {
      const response = await fetch('produits.json');
      if (!response.ok) throw new Error('Erreur chargement produits.json');
      return await response.json();
    } catch {
      return produits;
    }
  }

  function highlight(text, query) {
    return text.replace(new RegExp(`(${query})`, 'gi'), '<mark>$1</mark>');
  }

  input.addEventListener('input', async function() {
    const query = input.value.trim().toLowerCase();
    if (query.length < 2) {
      resultsDiv.classList.add('hidden');
      resultsDiv.innerHTML = '';
      return;
    }
    const produitsData = await fetchProduits();
    const matches = produitsData.filter(p =>
      p.nom.toLowerCase().includes(query) ||
      (p.description && p.description.toLowerCase().includes(query))
    );
    if (matches.length === 0) {
      resultsDiv.innerHTML = '<div class="p-4 text-gray-500">Aucun résultat</div>';
      resultsDiv.classList.remove('hidden');
      return;
    }
    resultsDiv.innerHTML = matches.map(p =>
      `<a href="produits.html?ref=${p.id}" class="block px-4 py-2 hover:bg-blue-50 border-b last:border-b-0">
        <span class="font-semibold">${highlight(p.nom, query)}</span><br>
        <span class="text-sm text-gray-500">${highlight(p.description || '', query)}</span>
      </a>`
    ).join('');
    resultsDiv.classList.remove('hidden');
  });

  document.addEventListener('click', function(e) {
    if (!resultsDiv.contains(e.target) && e.target !== input) {
      resultsDiv.classList.add('hidden');
    }
  });
});

document.addEventListener('DOMContentLoaded', afficherProduit);

// Gestion du panier localStorage + affichage sur panier.html
function getCart() {
  return JSON.parse(localStorage.getItem('cart') || '[]');
}
function setCart(cart) {
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartCount();
}
function updateCartCount() {
  const count = getCart().reduce((sum, item) => sum + item.quantite, 0);
  document.querySelectorAll('#cart-count').forEach(e => e.textContent = count);
}
// Ajouter au panier (à utiliser sur les boutons)
window.ajouterAuPanier = function(produitId, quantite = 1) {
  let cart = getCart();
  const idx = cart.findIndex(item => item.id === produitId);
  if (idx > -1) { cart[idx].quantite += quantite; }
  else { cart.push({ id: produitId, quantite }); }
  setCart(cart);
  alert('Produit ajouté au panier !');
};
// Affichage du panier sur panier.html
async function afficherPanier() {
  if (!document.getElementById('cart-list')) return;
  const produitsData = await fetch('produits.json').then(r => r.json());
  const cart = getCart();
  if (cart.length === 0) {
    document.getElementById('cart-empty').style.display = '';
    document.getElementById('cart-list').innerHTML = '';
    document.getElementById('cart-summary').classList.add('hidden');
    return;
  }
  document.getElementById('cart-empty').style.display = 'none';
  let total = 0;
  document.getElementById('cart-list').innerHTML = cart.map(item => {
    const p = produitsData.find(pr => pr.id === item.id);
    if (!p) return '';
    const sousTotal = parseFloat((p.prix||'0').replace(/[^\d.,]/g, '').replace(',', '.')) * item.quantite;
    total += sousTotal;
    return `<div class='flex items-center bg-white rounded shadow p-4'>
      <img src='${p.image}' alt='${p.nom}' class='w-20 h-20 object-cover rounded mr-4'>
      <div class='flex-1'>
        <div class='font-bold text-lg mb-1'>${p.nom}</div>
        <div class='text-blue-600 mb-1'>${p.prix}</div>
        <div class='flex items-center'>
          <button onclick='modifierQuantite(${p.id},-1)' class='px-2 py-1 bg-gray-200 rounded-l'>-</button>
          <span class='px-3'>${item.quantite}</span>
          <button onclick='modifierQuantite(${p.id},1)' class='px-2 py-1 bg-gray-200 rounded-r'>+</button>
        </div>
        <button onclick='supprimerDuPanier(${p.id})' class='text-red-500 text-sm mt-2'>Supprimer</button>
      </div>
      <div class='font-semibold text-lg'>${sousTotal.toFixed(2)} €</div>
    </div>`;
  }).join('');
  document.getElementById('cart-summary').classList.remove('hidden');
  document.getElementById('cart-total').textContent = total.toFixed(2) + ' €';
}
window.modifierQuantite = function(id, delta) {
  let cart = getCart();
  const idx = cart.findIndex(item => item.id === id);
  if (idx > -1) {
    cart[idx].quantite += delta;
    if (cart[idx].quantite < 1) cart[idx].quantite = 1;
    setCart(cart);
    afficherPanier();
  }
};
window.supprimerDuPanier = function(id) {
  let cart = getCart().filter(item => item.id !== id);
  setCart(cart);
  afficherPanier();
};
document.addEventListener('DOMContentLoaded', function() {
  updateCartCount();
  afficherPanier();
});