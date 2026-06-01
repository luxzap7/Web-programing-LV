<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Primjer HTML5 stranice</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <header>
        <h1>Dobrodošli na moju web stranicu</h1>
    </header>

    <div class="navigation">
        <ul>
            <li><a href="index.php">Početna</a></li>
            <li><a href="slike.php">Slike</a></li>
            <li><a href="grafikon.php">Grafikon</a></li>
        </ul>
    </div>

    <div id="hamburger-menu">
        <input type="checkbox" id="menu-toggle" aria-label="Toggle navigation" />
        <label for="menu-toggle" class="hamburger-button">&#9776;</label>
        <nav class="nav-links" aria-hidden="true">
            <ul>
                <li><a href="index.php">Početna</a></li>
                <li><a href="slike.php">Slike</a></li>
                <li><a href="grafikon.php">Grafikon</a></li>
            </ul>
        </nav>
    </div>

    <h1>Most popular movie genres</h1>
    <div class="center">
        <div class="grafikon-large">
            <dl>
                <dd class="percentage percentage-10"><span class="text"> Action 10%</span></dd>
                <dd class="percentage percentage-20"><span class="text"> Adventure 20%</span></dd>
                <dd class="percentage percentage-24"><span class="text"> Animated 24%</span></dd>
                <dd class="percentage percentage-12"><span class="text"> Comedy 12%</span></dd>
                <dd class="percentage percentage-9"><span class="text"> Drama 9%</span></dd>
                <dd class="percentage percentage-10"><span class="text"> Horror 10%</span></dd>
                <dd class="percentage percentage-15"><span class="text"> Sci Fi 15%</span></dd>
            </dl>
        </div>
        <div class="grafikon-small">
            <figure class="charts">
                <div class="pie"></div>
                <figcaption class="legends">
                    <span class="text"> Action 10%</span>
                    <span class="text"> Adventure 20%</span>
                    <span class="text"> Animated 24%</span>
                    <span class="text"> Comedy 12%</span>
                    <span class="text"> Drama 9%</span>
                    <span class="text"> Horror 10%</span>
                    <span class="text"> Sci Fi 15%</span>
                </figcaption>
            </figure>
        </div>
    </div>

    <section>
        <h2>Ovo je glavna sekcija</h2>
        <p>HTML5 omogućava semantičku strukturu koja poboljšava pristupačnost i SEO.</p>
    </section>

    <article>
        <h2>Najnovije vijesti</h2>
        <p>Ovdje se nalazi članak s važnim informacijama.</p>
    </article>

    <footer>
        <p>&copy; 2026. Web Programiranje. Sva prava pridržana.</p>
    </footer>
</body>
</html>
