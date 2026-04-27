const express = require('express');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

// EJS setup
app.set('view engine', 'ejs');

// static files (HTML, CSS, images)
app.use(express.static('public'));


// 📌 POČETNA STRANICA
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public/index.html'));
});


// 📌 GALERIJA (ZADATAK 3)
app.get('/slike', (req, res) => {

  const folderPath = path.join(__dirname, 'public/images');

  fs.readdir(folderPath, (err, files) => {
    if (err) {
      return res.send("Greška");
    }

    // filtriraj samo slike
    const images = files
      .filter(file => file.endsWith('.jpg') || file.endsWith('.png'))
      .map(file => ({
        url: `/images/${file}`,
        title: file
      }));

    res.render('slike', { images });
  });

});



// SERVER
app.listen(PORT, () => {
  console.log(`Server pokrenut na portu ${PORT}`);
});