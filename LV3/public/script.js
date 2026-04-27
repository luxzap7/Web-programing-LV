let sviFilmovi = [];
let filtriranFilmovi = [];
let kosarica = [];

/**
 * Prikazuje filmove u tablici i dodaje gumb "Dodaj u košaricu".
 * @param {Array} filmovi
 */
function prikaziTablicu(filmovi) {
  const tbody = document.getElementById('filmovi-tablica');
  tbody.innerHTML = '';

  filmovi.forEach((film) => {
    const red = document.createElement('tr');
    const uKosarici = kosarica.some(f => f.id === film.id);

    red.innerHTML = `
      <td>${film.id ?? ''}</td>
      <td>${film.title ?? ''}</td>
      <td>${film.year ?? ''}</td>
      <td>${film.genre ?? ''}</td>
      <td>${film.duration ?? ''}</td>
      <td>${Array.isArray(film.country) ? film.country.join(', ') : (film.country ?? '')}</td>
      <td>${film.rating ?? ''}</td>
      <td>
        <button class="add-cart-btn" data-id="${film.id}" ${uKosarici ? 'disabled' : ''}>
          ${uKosarici ? 'Dodano' : 'Dodaj u košaricu'}
        </button>
      </td>
    `;

    tbody.appendChild(red);
  });

  document.querySelectorAll('.add-cart-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      const filmId = Number(btn.dataset.id);
      const film = sviFilmovi.find(f => f.id === filmId);
      dodajUKosaricu(film);
    });
  });
}

async function ucitajFilmove() {
  try {
    const response = await fetch('/filmovi.csv');
    const csvText = await response.text();

    const parsed = Papa.parse(csvText, {
      header: true,
      skipEmptyLines: true
    });

    sviFilmovi = parsed.data.map((film, index) => ({
      id: index + 1,
      title: film.Naslov,
      year: Number(film.Godina),
      duration: Number(film.Trajanje_min),
      rating: Number(film.Ocjena),
      genre: film.Zanr,
      country: film.Zemlja_porijekla ? film.Zemlja_porijekla.split('/').map(c => c.trim()) : []
    }));

    filtriranFilmovi = [...sviFilmovi];
    prikaziTablicu(sviFilmovi.slice(0, 150));
    popuniZanrove();
    ucitajKosaricuIzLocalStorage();
  } catch (error) {
    console.error('Greška pri učitavanju filmova:', error);
  }
}

// ======================== ZADATAK 2: FILTRIRANJE ========================

function popuniZanrove() {
  const zanrovi = new Set();

  sviFilmovi.forEach(film => {
    if (film.genre) zanrovi.add(film.genre);
  });

  const selectGenre = document.getElementById('filter-genre');
  Array.from(zanrovi).sort().forEach(zanr => {
    const option = document.createElement('option');
    option.value = zanr;
    option.textContent = zanr;
    selectGenre.appendChild(option);
  });
}

function primijeniFiltere() {
  const searchValue = document.getElementById('filter-search').value.toLowerCase();
  const genreValue = document.getElementById('filter-genre').value;
  const ratingValue = Number(document.getElementById('filter-rating').value);

  filtriranFilmovi = sviFilmovi.filter(film => {
    const naslovMatch = film.title.toLowerCase().includes(searchValue);
    const zanrMatch = genreValue === '' || film.genre === genreValue;
    const ocjenaMatch = film.rating >= ratingValue;

    return naslovMatch && zanrMatch && ocjenaMatch;
  });

  prikaziTablicu(filtriranFilmovi.slice(0, 150));
}

function resetirajFiltere() {
  document.getElementById('filter-search').value = '';
  document.getElementById('filter-genre').value = '';
  document.getElementById('filter-rating').value = '0';
  document.getElementById('rating-value').textContent = '0';

  filtriranFilmovi = [...sviFilmovi];
  prikaziTablicu(sviFilmovi.slice(0, 150));
}

// ======================== ZADATAK 3: KOŠARICA ========================

/**
 * Dodaje film u košaricu.
 * @param {Object} film
 */
function dodajUKosaricu(film) {
  if (!film) return;
  if (!kosarica.some(f => f.id === film.id)) {
    kosarica.push(film);
    spremiKosaricuULocalStorage();
    azurirajKosaricu();
    prikaziTablicu(filtriranFilmovi.slice(0, 150));
  }
}

/**
 * Uklanja film iz košarice.
 * @param {number} filmId
 */
function ukloniIzKosarice(filmId) {
  kosarica = kosarica.filter(f => f.id !== filmId);
  spremiKosaricuULocalStorage();
  azurirajKosaricu();
  prikaziTablicu(filtriranFilmovi.slice(0, 150));
}


function azurirajKosaricu() {
  const cartCount = document.getElementById('cart-count');
  const cartItems = document.getElementById('cart-items');
  const confirmBtn = document.getElementById('confirm-cart');

  cartCount.textContent = `Odabranih filmova: ${kosarica.length}`;
  cartItems.innerHTML = '';

  kosarica.forEach(film => {
    const div = document.createElement('div');
    div.className = 'cart-item';
    div.innerHTML = `
      <span><strong>${film.title}</strong> | ${film.year} | ${film.genre}</span>
      <button class="remove-btn" data-id="${film.id}">Ukloni</button>
    `;

    div.querySelector('.remove-btn').addEventListener('click', () => {
      ukloniIzKosarice(film.id);
    });

    cartItems.appendChild(div);
  });

  confirmBtn.style.display = kosarica.length > 0 ? 'block' : 'none';
}


function potvrdiPosudbu() {
  const cartMessage = document.getElementById('cart-message');
  const count = kosarica.length;

  if (count === 0) {
    cartMessage.innerHTML = '<p style="color: red;">Nema odabranih filmova!</p>';
    return;
  }

  cartMessage.innerHTML = `<p style="color: green; font-weight: bold;">Uspješno ste potvrdili posudbu ${count} filmova.</p>`;

  setTimeout(() => {
    cartMessage.innerHTML = '';
  }, 3000);
}


function spremiKosaricuULocalStorage() {
  localStorage.setItem('kosarica', JSON.stringify(kosarica));
}


function ucitajKosaricuIzLocalStorage() {
  const saved = localStorage.getItem('kosarica');
  if (saved) {
    kosarica = JSON.parse(saved);
    azurirajKosaricu();
  }
}

// ======================== EVENT LISTENERI ========================

document.addEventListener('DOMContentLoaded', () => {
  ucitajFilmove();

  document.getElementById('filter-search').addEventListener('input', primijeniFiltere);
  document.getElementById('filter-genre').addEventListener('change', primijeniFiltere);
  document.getElementById('filter-rating').addEventListener('input', (e) => {
    document.getElementById('rating-value').textContent = e.target.value;
    primijeniFiltere();
  });

  document.getElementById('reset-filters').addEventListener('click', resetirajFiltere);
  document.getElementById('confirm-cart').addEventListener('click', potvrdiPosudbu);
});