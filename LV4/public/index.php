<?php
// index.php - Početna stranica
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Početna';
require_once __DIR__ . '/includes/header.php';
?>

<h1>Popis Filmova</h1>

<?php if (jePrijavljen()): ?>
    <p style="text-align:center; color: #3d9970; font-weight: 600;">
        Prijavljeni ste kao <?= htmlspecialchars(getKorisnickoIme()) ?>. 
        Idite na <a href="films.php">Filmove</a> za upravljanje videotekom.
    </p>
<?php else: ?>
    <p style="text-align:center;">
        <a href="login.php">Prijavite se</a> za pristup svim funkcionalnostima.
    </p>
<?php endif; ?>

<!-- FILTRI -->
<div class="filters-container">
    <h2>Filtri</h2>
    <div class="filter-group">
        <label for="filter-genre">Žanr:</label>
        <select id="filter-genre">
            <option value="">-- Svi žanrovi --</option>
        </select>
    </div>
    <div class="filter-group">
        <label for="filter-rating">Minimalna ocjena: <span id="rating-value">0</span></label>
        <input type="range" id="filter-rating" min="0" max="10" step="0.5" value="0">
    </div>
    <div class="filter-group">
        <label for="filter-search">Pretraživanje naslova:</label>
        <input type="text" id="filter-search" placeholder="Upiši naziv filma...">
    </div>
    <button id="reset-filters">Resetiraj filtere</button>
</div>

<!-- TABLICA FILMOVA (CSV - klijentska strana) -->
<div class="table-images-container">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Naslov</th><th>Godina</th><th>Žanr</th>
                    <th>Trajanje</th><th>Država</th><th>Ocjena</th><th>Akcija</th>
                </tr>
            </thead>
            <tbody id="filmovi-tablica"></tbody>
        </table>
    </div>
    <aside>
        <div class="images">
            <img src="images/slika1.jpg" alt="Slika 1">
            <img src="images/slika2.jpg" alt="Slika 2">
        </div>
    </aside>
</div>

<!-- KOŠARICA (klijentska strana - iz LV3) -->
<div class="cart-container">
    <h2>Košarica za posudbu</h2>
    <p id="cart-count">Odabranih filmova: 0</p>
    <div id="cart-items"></div>
    <button id="confirm-cart" style="display: none;">Potvrdi posudbu</button>
    <div id="cart-message"></div>
</div>

<section>
    <h2>Ovo je glavna sekcija</h2>
    <p>HTML5 omogućava semantičku strukturu koja poboljšava pristupačnost i SEO.</p>
</section>

<article>
    <h2>Najnovije vijesti</h2>
    <p>Ovdje se nalazi članak s važnim informacijama.</p>
</article>

<script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
<script src="script.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
