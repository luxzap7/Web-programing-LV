<?php
// includes/auth.php
// Pomoćne funkcije za autentifikaciju

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Provjerava je li korisnik prijavljen.
 */
function jePrijavljen(): bool {
    return isset($_SESSION['korisnik_id']);
}

/**
 * Provjerava je li korisnik admin.
 */
function jeAdmin(): bool {
    return isset($_SESSION['uloga']) && $_SESSION['uloga'] === 'admin';
}

/**
 * Vraća ID prijavljenog korisnika ili null.
 */
function getKorisnikId(): ?int {
    return $_SESSION['korisnik_id'] ?? null;
}

/**
 * Vraća korisničko ime prijavljenog korisnika.
 */
function getKorisnickoIme(): ?string {
    return $_SESSION['korisnicko_ime'] ?? null;
}

/**
 * Preusmjerava na login ako korisnik nije prijavljen.
 */
function zahtijevajPrijavu(): void {
    if (!jePrijavljen()) {
        header('Location: login.php');
        exit;
    }
}
