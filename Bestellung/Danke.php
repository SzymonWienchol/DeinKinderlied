<?php
$orderId = htmlspecialchars($_GET['order_id'] ?? '');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellung bestätigt - DeinKinderlied.de</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="burger-menu">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
            <ul class="nav-links">
                <li><a href="index.html">Startseite</a></li>
                <li><a href="impressum.html">Impressum</a></li>
            </ul>
        </div>
    </nav>

    <header class="hero">
        <a href="index.html" class="logo-link">
            <img src="logo.png" alt="Dein Kinderlied Logo" class="logo">
        </a>
        <h1>Vielen Dank für deine Bestellung! 🎶</h1>
        <p>Deine Bestellung #<?php echo $orderId; ?> ist bei uns eingegangen.</p>
    </header>

    <section class="thank-you-content">
        <div class="container">
            <div class="thank-you-card">
                <h2>Was passiert jetzt?</h2>
                <ol>
                    <li>Wir erstellen dein persönliches Lied basierend auf deinen Angaben</li>
                    <li>Du erhältst das fertige Lied als MP3-Datei per E-Mail</li>
                    <li>Die Rechnung folgt separat mit Zahlungsinformationen</li>
                </ol>
                
                <?php if (!empty($orderId)): ?>
                <div class="order-summary">
                    <h3>Deine Bestellnummer: <?php echo $orderId; ?></h3>
                    <p>Behalte diese Nummer für Rückfragen bereit.</p>
                </div>
                <?php endif; ?>
                
                <div class="contact-info">
                    <h3>Fragen zu deiner Bestellung?</h3>
                    <p>Schreibe uns an <a href="mailto:bestellung@deinkinderlied.de">bestellung@deinkinderlied.de</a></p>
                </div>
                
                <a href="index.html" class="button">Zurück zur Startseite</a>
            </div>
        </div>
    </section>

    <footer>
        <p>© 2025 DeinKinderlied.de • <a href="impressum.html">Impressum & Datenschutz</a></p>
    </footer>

    <script>
        // Burger menu JS same as before
        document.addEventListener('DOMContentLoaded', function() {
            const burgerMenu = document.querySelector('.burger-menu');
            const navLinks = document.querySelector('.nav-links');
            
            burgerMenu.addEventListener('click', function() {
                navLinks.classList.toggle('active');
                
                const bars = document.querySelectorAll('.bar');
                if(navLinks.classList.contains('active')) {
                    bars[0].style.transform = 'rotate(45deg) translate(5px, 6px)';
                    bars[1].style.opacity = '0';
                    bars[2].style.transform = 'rotate(-45deg) translate(5px, -6px)';
                } else {
                    bars.forEach(bar => {
                        bar.style.transform = 'rotate(0) translate(0)';
                        bar.style.opacity = '1';
                    });
                }
            });
        });
    </script>
</body>
</html>