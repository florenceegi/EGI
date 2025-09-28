<!DOCTYPE html>
<html lang="{{ __('patron.page_lang') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('patron.page_title') }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@300;400;600&display=swap"
        rel="stylesheet">
    <style>
        /* Definizione dei colori e font basati sulle Brand Guidelines di FlorenceEGI */
        :root {
            --oro-fiorentino: #D4A574;
            --verde-rinascita: #2D5016;
            --blu-algoritmo: #1B365D;
            --grigio-pietra: #6B6B6B;
            --rosso-urgenza: #C13120;
            --arancio-energia: #E67E22;
            --viola-innovazione: #8E44AD;
            --white: #ffffff;
            --light-grey: #F8F8F8;
            --dark-grey: #333333;
        }

        /* Stili globali per il corpo della pagina (Mobile-First) */
        body {
            font-family: 'Source Sans Pro', sans-serif;
            line-height: 1.6;
            color: var(--grigio-pietra);
            margin: 0;
            padding: 0;
            background-color: var(--white);
            overflow-x: hidden;
            /* Evita lo scroll orizzontale */
        }

        /* Contenitore principale per simulare una "finestra" nel canvas */
        #app-container {
            max-width: 1200px;
            /* Larghezza massima del contenuto dell'applicazione */
            margin: 20px auto;
            /* Centra il contenitore e aggiunge margine intorno */
            background-color: var(--white);
            border-radius: 15px;
            /* Bordi arrotondati per l'intera app */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            /* Ombra per dare profondità */
            overflow: hidden;
            /* Assicura che i bordi arrotondati funzionino con i contenuti */
            display: flex;
            /* Layout flessibile per sidebar e contenuto */
            flex-direction: column;
            /* Mobile-first: sidebar sopra o sotto il contenuto */
            min-height: calc(100vh - 40px);
            /* Altezza minima per la visibilità */
        }

        /* Sidebar per la navigazione (Mobile-First) */
        .sidebar {
            width: 100%;
            /* Larghezza piena su mobile */
            background-color: var(--blu-algoritmo);
            color: var(--white);
            padding: 20px;
            box-sizing: border-box;
            transform: translateX(-100%);
            /* Nascondi la sidebar su mobile */
            position: fixed;
            /* Fisso per l'overlay mobile */
            top: 0;
            left: 0;
            height: 100%;
            z-index: 1000;
            /* Sopra tutto il resto */
            transition: transform 0.3s ease-in-out;
            border-radius: 0 15px 15px 0;
            /* Bordi arrotondati solo su un lato */
            overflow-y: auto;
            /* Permette lo scroll interno della sidebar su mobile */
        }

        .sidebar.open {
            transform: translateX(0);
            /* Mostra la sidebar */
        }

        /* FIXED: Logo styling migliorato */
        .sidebar .logo-container {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 30px;
            padding: 0;
        }

        .sidebar .logo-container .logo {
            height: 40px;
            /* Altezza fissa controllata */
            width: auto;
            /* Mantiene proporzioni */
            max-width: 150px;
            /* Limite massimo larghezza */
            object-fit: contain;
            /* Mantiene proporzioni senza distorsione */
        }

        .sidebar .logo-container .logo-text {
            margin-left: 10px;
            font-size: 0.9em;
            font-weight: 600;
            color: var(--white);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar .logo-container a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
            transition: opacity 0.3s ease;
        }

        .sidebar .logo-container a:hover {
            opacity: 0.8;
        }

        .sidebar nav {
            position: static;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        /* Styling per i link della sidebar (NON BOTTONI) */
        .sidebar nav a {
            color: var(--white);
            text-decoration: none;
            padding: 10px 15px;
            /* Padding più sottile */
            display: block;
            width: 100%;
            transition: background-color 0.3s ease, color 0.3s ease, border-left 0.3s ease;
            border-radius: 0;
            /* Rimuove bordi arrotondati */
            margin-bottom: 2px;
            /* Margine minimale tra le voci */
            border-left: 5px solid transparent;
            /* Bordo iniziale trasparente */
            font-weight: 400;
            /* Peso normale */
        }

        .sidebar nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--oro-fiorentino);
            border-left-color: var(--oro-fiorentino);
            /* Bordo a sinistra all'hover */
        }

        .sidebar nav a.active {
            background-color: rgba(255, 255, 255, 0.15);
            /* Sfondo leggermente più scuro per attivo */
            font-weight: 600;
            /* Semibold per attivo */
            border-left-color: var(--verde-rinascita);
            /* Bordo a sinistra per attivo */
            color: var(--verde-rinascita);
            /* Colore verde per attivo */
        }

        /* FIXED: Hamburger Menu Icon con Font Awesome */
        .hamburger {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            cursor: pointer;
            padding: 12px;
            background-color: rgba(27, 54, 93, 0.9);
            /* Blu algoritmo con trasparenza */
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: var(--white);
            font-size: 18px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hamburger:hover {
            background-color: var(--blu-algoritmo);
            transform: scale(1.05);
        }

        .hamburger:active {
            transform: scale(0.95);
        }

        /* Contenuto principale della pagina */
        .main-content {
            flex-grow: 1;
            padding-top: 60px;
            /* Spazio per l'hamburger menu in mobile */
            padding-left: 0;
            /* Reset per mobile */
            position: relative;
            /* Necessario per posizionare header-top-nav-mobile-hamburger */
        }

        /* Stili per l'header principale della pagina */
        header {
            /* Immagine di sfondo aggiornata (placeholder per il canvas) */
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('{{ asset('images/default/patron_banner_background_rinascimento_1.png') }}') no-repeat center center/cover;
            color: var(--white);
            padding: 80px 20px;
            text-align: center;
            position: relative;
            border-radius: 0 0 15px 15px;
            box-sizing: border-box;
            /* Include padding nel width/height */
        }

        /* Navbar globale per desktop */
        .header-top-nav {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
            display: none;
            /* Nascosta su mobile per default */
        }

        .header-top-nav a {
            color: var(--white);
            text-decoration: none;
            margin-left: 20px;
            font-weight: 400;
            transition: color 0.3s ease;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .header-top-nav a:hover {
            color: var(--oro-fiorentino);
        }

        .header-top-nav a.active-actor {
            color: var(--verde-rinascita);
            font-weight: 600;
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* FIXED: Hamburger per la Navbar Globale con Font Awesome */
        .top-nav-hamburger {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1001;
            /* Sopra tutto, anche la sidebar hamburger */
            cursor: pointer;
            padding: 12px;
            background-color: rgba(27, 54, 93, 0.9);
            /* Blu algoritmo con trasparenza */
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: var(--white);
            font-size: 18px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .top-nav-hamburger:hover {
            background-color: var(--blu-algoritmo);
            transform: scale(1.05);
        }

        .top-nav-hamburger:active {
            transform: scale(0.95);
        }

        /* Overlay per la Navbar Globale Mobile */
        .top-nav-overlay {
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background-color: var(--blu-algoritmo);
            /* Stesso colore della sidebar */
            z-index: 999;
            /* Appena sotto la sidebar hamburger */
            transform: translateX(100%);
            /* Nascosto a destra */
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 15px 0 0 15px;
        }

        .top-nav-overlay.open {
            transform: translateX(0);
        }

        .top-nav-overlay a {
            color: var(--white);
            text-decoration: none;
            padding: 15px 20px;
            font-size: 1.5em;
            display: block;
            width: 80%;
            /* Larghezza per i link nell'overlay */
            text-align: center;
            margin-bottom: 10px;
            border-radius: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .top-nav-overlay a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--oro-fiorentino);
        }

        .top-nav-overlay a.active-actor {
            background-color: var(--verde-rinascita);
            font-weight: 700;
        }

        .header-content {
            max-width: 800px;
            margin: 0 auto;
        }

        header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5em;
            margin-bottom: 10px;
            color: var(--white);
        }

        header p {
            font-family: 'Playfair Display', serif;
            font-size: 1.2em;
            margin-top: 0;
            color: var(--white);
        }

        .cta-button {
            display: inline-block;
            background-color: var(--verde-rinascita);
            color: var(--white);
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 30px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .cta-button:hover {
            background-color: var(--oro-fiorentino);
            transform: translateY(-3px);
        }

        /* Stili per le sezioni generiche della pagina */
        .section {
            padding: 60px 20px;
            max-width: 960px;
            margin: 0 auto;
            box-sizing: border-box;
            /* Include padding nel width/height */
        }

        .section:nth-child(even) {
            background-color: var(--light-grey);
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .section h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            color: var(--blu-algoritmo);
            text-align: center;
            margin-bottom: 30px;
        }

        .section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5em;
            color: var(--verde-rinascita);
            margin-bottom: 15px;
            text-align: center;
        }

        .text-block {
            text-align: center;
            margin-bottom: 30px;
        }

        .text-block p {
            max-width: 700px;
            margin: 0 auto;
            font-size: 1em;
            color: var(--dark-grey);
        }

        /* Stili per le liste di icone/feature */
        .icon-list {
            display: grid;
            grid-template-columns: 1fr;
            /* Una colonna su mobile */
            gap: 20px;
            margin-top: 30px;
        }

        .icon-item {
            text-align: center;
            background-color: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--light-grey);
        }

        .icon-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .icon-item i {
            font-size: 2.5em;
            margin-bottom: 15px;
            color: var(--oro-fiorentino);
        }

        .icon-item h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .icon-item p {
            font-size: 0.9em;
            color: var(--grigio-pietra);
        }

        /* Stili per layout a due colonne (Mobile-First: stack in colonna) */
        .two-column {
            flex-direction: column;
            gap: 30px;
            margin-top: 30px;
            justify-content: center;
        }

        .col-left,
        .col-right {
            flex: 1;
            min-width: 300px;
            background-color: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .col-left h4,
        .col-right h4 {
            font-family: 'Playfair Display', serif;
            color: var(--blu-algoritmo);
            font-size: 1.3em;
            margin-top: 0;
            text-align: center;
            margin-bottom: 15px;
        }

        .col-left ul,
        .col-right ul {
            list-style: none;
            padding: 0;
        }

        .col-left ul li,
        .col-right ul li {
            font-size: 1em;
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
        }

        .col-left ul li::before {
            content: '❌';
            color: var(--rosso-urgenza);
            margin-right: 15px;
            font-size: 1.2em;
        }

        .col-right ul li::before {
            content: '✅';
            color: var(--verde-rinascita);
            margin-right: 15px;
            font-size: 1.2em;
        }

        /* Stili per la timeline */
        .timeline {
            position: relative;
            margin: 40px 0;
            padding-left: 15px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 0;
            width: 4px;
            height: 100%;
            background-color: var(--verde-rinascita);
            border-radius: 2px;
        }

        .timeline-item {
            margin-bottom: 30px;
            position: relative;
            padding-left: 30px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0px;
            top: 5px;
            width: 16px;
            height: 16px;
            background-color: var(--verde-rinascita);
            border-radius: 50%;
            border: 3px solid var(--white);
            box-shadow: 0 0 0 1px var(--verde-rinascita);
            z-index: 1;
        }

        .timeline-item h4 {
            font-family: 'Playfair Display', serif;
            color: var(--blu-algoritmo);
            font-size: 1.2em;
            margin-bottom: 5px;
        }

        .timeline-item p {
            color: var(--grigio-pietra);
            font-size: 0.9em;
        }

        /* Stili per le tabelle del modello economico */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 40px 0;
            font-size: 0.95em;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            /* Bordo sottile */
            padding: 12px 15px;
            text-align: left;
        }

        table th {
            background-color: var(--blu-algoritmo);
            color: var(--white);
            font-weight: 600;
        }

        table tr:nth-child(even) {
            background-color: var(--light-grey);
            /* Strisce chiare */
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        table td strong {
            color: var(--verde-rinascita);
            font-weight: 700;
            /* Reso più forte come nello screenshot */
        }

        /* Stili per la griglia delle storie di successo */
        .stories-grid {
            display: grid;
            grid-template-columns: 1fr;
            /* Una colonna su mobile */
            gap: 20px;
            margin-top: 30px;
        }

        .story-card {
            background-color: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: left;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-top: 4px solid var(--oro-fiorentino);
        }

        .story-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .story-card h4 {
            font-family: 'Playfair Display', serif;
            color: var(--blu-algoritmo);
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .story-card ul {
            list-style: none;
            padding: 0;
            font-size: 0.9em;
            color: var(--grigio-pietra);
        }

        .story-card ul li {
            margin-bottom: 8px;
        }

        /* Stili per le feature box degli strumenti */
        .feature-boxes {
            display: grid;
            grid-template-columns: 1fr;
            /* Una colonna su mobile */
            gap: 20px;
            margin-top: 30px;
        }

        .feature-box {
            background-color: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: left;
            border-top: 4px solid var(--verde-rinascita);
        }

        .feature-box h3 {
            color: var(--blu-algoritmo);
            text-align: left;
            margin-top: 0;
            font-size: 1.3em;
        }

        .feature-box ul {
            list-style: none;
            padding: 0;
            font-size: 0.95em;
        }

        .feature-box ul li {
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
        }

        .feature-box ul li::before {
            content: '✔️';
            color: var(--verde-rinascita);
            margin-right: 10px;
            font-size: 1.1em;
        }

        /* Stili per l'accordion delle FAQ */
        .accordion-container {
            margin-top: 30px;
        }

        .accordion-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .accordion-item:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .accordion-header {
            background-color: var(--white);
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1em;
            font-weight: 600;
            color: var(--dark-grey);
            transition: background-color 0.3s ease;
            border: none;
            width: 100%;
            text-align: left;
        }

        .accordion-header:hover {
            background-color: var(--light-grey);
        }

        .accordion-header .icon {
            font-size: 1.5em;
            color: var(--grigio-pietra);
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .accordion-header.active .icon {
            transform: rotate(180deg);
            color: var(--verde-rinascita);
        }

        .accordion-content {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-out, padding 0.4s ease-out;
            background-color: var(--light-grey);
            color: var(--grigio-pietra);
            border-top: 1px solid #eee;
        }

        .accordion-content p {
            padding: 15px 0;
            margin: 0;
        }

        .accordion-content.open {
            max-height: 500px;
            padding-bottom: 20px;
        }

        /* Stili per il programma Pioneer */
        .pioneer-program {
            background-color: var(--verde-rinascita);
            color: var(--white);
            padding: 40px 20px;
            text-align: center;
            border-radius: 12px;
            margin: 60px auto;
            max-width: 960px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
        }

        .pioneer-program h2 {
            color: var(--white);
            font-size: 2em;
            margin-bottom: 20px;
        }

        .pioneer-program h3 {
            color: var(--oro-fiorentino);
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .pioneer-benefits-grid {
            display: grid;
            grid-template-columns: 1fr;
            /* Una colonna su mobile */
            gap: 15px;
            margin-top: 30px;
            text-align: left;
        }

        .pioneer-benefit-item {
            background-color: rgba(255, 255, 255, 0.15);
            padding: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            color: var(--white);
            font-size: 1em;
        }

        .pioneer-benefit-item .icon {
            font-size: 1.8em;
            margin-right: 10px;
            color: var(--oro-fiorentino);
        }

        .pioneer-program h4 {
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 1.1em;
            margin-top: 15px;
            color: var(--white);
            text-align: center;
        }

        .pioneer-requirements ul {
            list-style: none;
            padding: 0;
            margin-top: 15px;
            text-align: center;
        }

        .pioneer-requirements ul li {
            margin-bottom: 8px;
            font-size: 1em;
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pioneer-requirements ul li::before {
            content: '✅';
            color: var(--oro-fiorentino);
            margin-right: 10px;
        }

        /* Stili per i passi dell'onboarding */
        .onboarding-steps {
            display: grid;
            grid-template-columns: 1fr;
            /* Una colonna su mobile */
            gap: 20px;
            margin-top: 30px;
        }

        .onboarding-step-card {
            background-color: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
            border-bottom: 4px solid var(--oro-fiorentino);
            transition: transform 0.3s ease;
        }

        .onboarding-step-card:hover {
            transform: translateY(-5px);
        }

        .onboarding-step-card .step-number {
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            color: var(--oro-fiorentino);
            margin-bottom: 10px;
            display: block;
        }

        .onboarding-step-card .step-icon {
            font-size: 2.5em;
            color: var(--verde-rinascita);
            margin-bottom: 10px;
        }

        .onboarding-step-card h4 {
            font-family: 'Playfair Display', serif;
            color: var(--blu-algoritmo);
            font-size: 1.3em;
            margin-bottom: 10px;
        }

        .onboarding-step-card ul {
            list-style: none;
            padding: 0;
            font-size: 0.9em;
            color: var(--grigio-pietra);
        }

        .onboarding-step-card ul li {
            margin-bottom: 5px;
        }

        /* Stili per contatti e risorse */
        .contact-resources {
            flex-direction: column;
            /* Stack in colonna su mobile */
            gap: 20px;
            margin-top: 30px;
        }

        .contact-block,
        .download-block {
            background-color: var(--white);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .contact-block h3,
        .download-block h3 {
            color: var(--blu-algoritmo);
            margin-top: 0;
            text-align: left;
            font-size: 1.3em;
        }

        .contact-block ul {
            list-style: none;
            padding: 0;
        }

        .contact-block ul li,
        .download-block ul li {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            font-size: 1em;
            color: var(--dark-grey);
        }

        .contact-block ul li .icon {
            font-size: 1.3em;
            color: var(--verde-rinascita);
            margin-right: 10px;
        }

        .contact-block ul li a,
        .download-block ul li a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-block ul li a:hover,
        .download-block ul li a:hover {
            color: var(--oro-fiorentino);
        }

        /* Stili per il messaggio personale */
        .personal-message {
            background-color: var(--light-grey);
            padding: 40px 20px;
            margin-top: 60px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .personal-message h2 {
            color: var(--blu-algoritmo);
            font-size: 2em;
            margin-bottom: 20px;
        }

        .personal-message p {
            font-family: 'Playfair Display', serif;
            font-size: 1.1em;
            margin-bottom: 30px;
        }

        .signature {
            font-size: 1.4em;
        }

        .final-slogan {
            font-size: 1.1em;
            font-weight: 600;
            color: var(--blu-algoritmo);
            margin-top: 20px;
        }

        /* Footer */
        footer {
            background-color: var(--blu-algoritmo);
            color: var(--white);
            text-align: center;
            padding: 40px 20px;
            font-size: 0.9em;
            border-radius: 15px 15px 0 0;
        }

        footer a {
            color: var(--oro-fiorentino);
            text-decoration: none;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--white);
        }

        /* Desktop specific styles (overrides for larger screens) */
        @media (min-width: 769px) {
            #app-container {
                flex-direction: row;
                /* Sidebar a sinistra, contenuto a destra */
            }

            .hamburger {
                display: none;
                /* Nascondi l'hamburger della sidebar su desktop */
            }

            .top-nav-hamburger {
                display: none;
                /* Nascondi l'hamburger della top nav su desktop */
            }

            .header-top-nav {
                display: block;
                /* Mostra la top nav su desktop */
            }

            .sidebar {
                position: fixed;
                /* Sidebar fissa in viewport */
                top: 0;
                left: 0;
                width: 250px;
                /* Larghezza fissa della sidebar */
                height: 100vh;
                /* Altezza piena della viewport */
                padding: 30px;
                transform: translateX(0);
                /* Sempre visibile su desktop */
                border-radius: 15px 0 0 15px;
                /* Bordi arrotondati solo su un lato */
                overflow-y: auto;
                /* Permette lo scroll interno della sidebar se il contenuto supera l'altezza */
                z-index: 900;
                /* Assicurati che sia sopra il main-content ma sotto gli overlay mobili */
            }

            .main-content {
                margin-left: 250px;
                /* Spazio per la sidebar fissa */
                padding-top: 0;
                /* Nessun padding superiore dovuto agli hamburger */
            }

            header {
                padding: 100px 0;
                /* Torna al padding originale */
            }

            header h1 {
                font-size: 3.5em;
                /* Torna alla dimensione originale */
            }

            header p {
                font-size: 1.5em;
                /* Torna alla dimensione originale */
            }

            .section {
                padding: 80px 20px;
                /* Torna al padding originale */
            }

            .section h2 {
                font-size: 2.5em;
                /* Torna alla dimensione originale */
            }

            .section h3 {
                font-size: 1.8em;
                /* Torna alla dimensione originale */
            }

            .text-block p {
                font-size: 1.1em;
            }

            .icon-list,
            .stories-grid,
            .feature-boxes,
            .onboarding-steps {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 30px;
            }

            .two-column {
                flex-direction: row;
                /* Torna a layout a due colonne */
            }

            .timeline {
                padding-left: 20px;
            }

            .timeline::before {
                left: 0;
            }

            .timeline-item::before {
                left: -8px;
                width: 20px;
                height: 20px;
                border: 4px solid var(--white);
                box-shadow: 0 0 0 2px var(--verde-rinascita);
            }

            table {
                font-size: 0.95em;
            }

            table th,
            table td {
                padding: 12px 15px;
            }

            .pioneer-program {
                padding: 60px 20px;
            }

            .pioneer-program h2 {
                font-size: 2.8em;
            }

            .pioneer-benefits-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }

            .contact-resources {
                flex-direction: row;
            }

            .personal-message h2 {
                font-size: 2.8em;
            }

            .personal-message p {
                font-size: 1.4em;
            }
        }
    </style>
    <!-- Importazione di Font Awesome per le icone -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <!-- Contenitore esterno per simulare la "finestra" dell'app nel Canvas -->
    <div id="app-container" itemscope itemtype="https://schema.org/WebPage">

        <!-- FIXED: Hamburger Icon per Sidebar con Font Awesome -->
        <button class="hamburger" id="sidebar-hamburger" aria-label="Apri menu sezioni" aria-controls="sidebar-menu"
            aria-expanded="false">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>

        <!-- FIXED: Hamburger Icon per Top Nav con Font Awesome -->
        <button class="top-nav-hamburger" id="top-nav-hamburger" aria-label="Apri menu attori"
            aria-controls="top-nav-overlay" aria-expanded="false">
            <i class="fas fa-users" aria-hidden="true"></i>
        </button>

        <!-- Sidebar per la navigazione interna (si trasforma in Hamburger su mobile) -->
        <aside class="sidebar" id="sidebar" role="navigation" aria-label="Menu di navigazione della pagina Mecenate">
            <!-- FIXED: Logo container con classi e styling appropriati -->
            <div class="logo-container">
                <a href="{{ url('/home') }}" aria-label="{{ __('guest_layout.logo_aria_label') }}">
                    <img src="{{ asset('images/logo/logo_1.webp') }}" alt="{{ __('guest_layout.logo_alt_text') }}"
                        class="logo" loading="lazy" decoding="async">
                    <span class="logo-text">{{ __('guest_layout.navbar_brand_name') }}</span>
                </a>
            </div>
            <nav id="sidebar-menu">
                <!-- Link esplicito per tornare alla Home -->
                <a href="{{ route('home') }}" class="sidebar-link home-link" role="menuitem"
                    style="border-bottom: 1px solid rgba(255,255,255,0.2); margin-bottom: 15px; padding-bottom: 10px;">
                    <i class="fas fa-home" aria-hidden="true" style="margin-right: 8px;"></i>
                    Torna alla Home
                </a>
                <!-- Links della sidebar con target alle sezioni interne -->
                <a href="#intro" class="sidebar-link" role="menuitem">{{ __('patron.navigation.introduction') }}</a>
                <a href="#ruolo" class="sidebar-link" role="menuitem">{{ __('patron.navigation.concrete_role') }}</a>
                <a href="#diventare" class="sidebar-link"
                    role="menuitem">{{ __('patron.navigation.becoming_patron') }}</a>
                <a href="#percorso" class="sidebar-link" role="menuitem">{{ __('patron.navigation.success_path') }}</a>
                <a href="#economico" class="sidebar-link"
                    role="menuitem">{{ __('patron.navigation.value_model') }}</a>
                <a href="#storie" class="sidebar-link"
                    role="menuitem">{{ __('patron.navigation.success_stories') }}</a>
                <a href="#strumenti" class="sidebar-link"
                    role="menuitem">{{ __('patron.navigation.tools_support') }}</a>
                <a href="#sfaccettature" class="sidebar-link"
                    role="menuitem">{{ __('patron.navigation.role_facets') }}</a>
                <a href="#faq" class="sidebar-link" role="menuitem">{{ __('patron.navigation.faq') }}</a>
                <a href="#pioneer" class="sidebar-link"
                    role="menuitem">{{ __('patron.navigation.pioneer_program') }}</a>
                <a href="#inizia" class="sidebar-link" role="menuitem">{{ __('patron.navigation.start_today') }}</a>
                <a href="#contatti" class="sidebar-link"
                    role="menuitem">{{ __('patron.navigation.contacts_resources') }}</a>
            </nav>
        </aside>

        <!-- Overlay per la Navbar Globale Mobile -->
        <div class="top-nav-overlay" id="top-nav-overlay" role="dialog" aria-modal="true"
            aria-labelledby="top-nav-label">
            <h2 id="top-nav-label" class="sr-only">{{ __('patron.navigation.main_navigation_label') }}</h2>
            <nav role="navigation" aria-label="Navigazione tra gli attori della piattaforma">
                <a href="#">{{ __('patron.global_nav.creator') }}</a>
                <a href="#">{{ __('patron.global_nav.collectors') }}</a>
                <a href="#">{{ __('patron.global_nav.trader_pro') }}</a>
                <a href="#">{{ __('patron.global_nav.epp') }}</a>
                <a href="#">{{ __('patron.global_nav.companies') }}</a>
                <a href="#">{{ __('patron.global_nav.vip') }}</a>
            </nav>
        </div>

        <!-- Contenuto principale della pagina -->
        <div class="main-content" role="main">
            <!-- Header della pagina con navbar globale (desktop) -->
            <header>
                <div class="header-top-nav" role="navigation"
                    aria-label="Navigazione tra gli attori della piattaforma">
                    <!-- Navbar globale per navigare tra gli attori -->
                    <a href="{{ route('home') }}">{{ __('patron.global_nav.home') }}</a>
                    <a href="#">{{ __('patron.global_nav.creator') }}</a>
                    <a href="#">{{ __('patron.global_nav.collectors') }}</a>
                    <a href="#">{{ __('patron.global_nav.trader_pro') }}</a>
                    <a href="#">{{ __('patron.global_nav.epp') }}</a>
                    <a href="#">{{ __('patron.global_nav.companies') }}</a>
                    <a href="#">{{ __('patron.global_nav.vip') }}</a>
                </div>
                <div class="header-content">
                    <h1 itemprop="name">{{ __('patron.hero.title') }}</h1>
                    <p itemprop="description">{{ __('patron.hero.subtitle') }}</p>
                    <a href="#intro" class="cta-button" role="button">{{ __('patron.hero.cta_button') }}</a>
                </div>
            </header>

            <main>
                <!-- Sezione Introduzione: Chi è il Mecenate in FlorenceEGI -->
                <section id="intro" class="section" itemscope itemtype="https://schema.org/AboutPage">
                    <h2 itemprop="headline">{{ __('patron.intro.title') }}</h2>
                    <h3>{{ __('patron.hero.intro_subtitle') }}</h3>
                    <div class="text-block">
                        <p itemprop="abstract">{{ __('patron.intro.description') }}</p>
                    </div>
                    <div class="icon-list">
                        <div class="icon-item" itemprop="disambiguatingDescription">
                            <i class="fas fa-handshake" aria-hidden="true"></i>
                            <h4>{{ __('patron.intro.facilitator_title') }}</h4>
                            <p>{{ __('patron.intro.facilitator_desc') }}</p>
                        </div>
                        <div class="icon-item" itemprop="disambiguatingDescription">
                            <i class="fas fa-bullhorn" aria-hidden="true"></i>
                            <h4>{{ __('patron.intro.promoter_title') }}</h4>
                            <p>{{ __('patron.intro.promoter_desc') }}</p>
                        </div>
                        <div class="icon-item" itemprop="disambiguatingDescription">
                            <i class="fas fa-chart-line" aria-hidden="true"></i>
                            <h4>{{ __('patron.intro.partner_title') }}</h4>
                            <p>{{ __('patron.intro.partner_desc') }}</p>
                        </div>
                        <div class="icon-item" itemprop="disambiguatingDescription">
                            <i class="fas fa-leaf" aria-hidden="true"></i>
                            <h4>{{ __('patron.intro.agent_title') }}</h4>
                            <p>{{ __('patron.intro.agent_desc') }}</p>
                        </div>
                    </div>
                </section>

                <!-- Sezione Ruolo e Contributo: Il Tuo Ruolo Concreto nel Nuovo Rinascimento -->
                <section id="ruolo" class="section" itemscope itemtype="https://schema.org/Role">
                    <h2 itemprop="name">{{ __('patron.role.title') }}</h2>
                    <div class="icon-list">
                        <div class="icon-item" itemprop="description">
                            <i class="fas fa-lightbulb" aria-hidden="true"
                                style="color: var(--verde-rinascita);"></i>
                            <h4>{{ __('patron.role.discovery_title') }}</h4>
                            <p>{!! __('patron.role.discovery_desc') !!}</p>
                        </div>
                        <div class="icon-item" itemprop="description">
                            <i class="fas fa-cogs" aria-hidden="true" style="color: var(--verde-rinascita);"></i>
                            <h4>{{ __('patron.role.technical_title') }}</h4>
                            <p>{!! __('patron.role.technical_desc') !!}</p>
                        </div>
                        <div class="icon-item" itemprop="description">
                            <i class="fas fa-upload" aria-hidden="true" style="color: var(--verde-rinascita);"></i>
                            <h4>{{ __('patron.role.publication_title') }}</h4>
                            <p>{!! __('patron.role.publication_desc') !!}</p>
                        </div>
                        <div class="icon-item" itemprop="description">
                            <i class="fas fa-network-wired" aria-hidden="true"
                                style="color: var(--verde-rinascita);"></i>
                            <h4>{{ __('patron.role.network_title') }}</h4>
                            <p>{!! __('patron.role.network_desc') !!}</p>
                        </div>
                        <div class="icon-item" itemprop="description">
                            <i class="fas fa-comments" aria-hidden="true" style="color: var(--verde-rinascita);"></i>
                            <h4>{{ __('patron.role.relationships_title') }}</h4>
                            <p>{!! __('patron.role.relationships_desc') !!}</p>
                        </div>
                        <div class="icon-item" itemprop="description">
                            <i class="fas fa-dollar-sign" aria-hidden="true"
                                style="color: var(--verde-rinascita);"></i>
                            <h4>{{ __('patron.role.earning_title') }}</h4>
                            <p>{!! __('patron.role.earning_desc') !!}</p>
                        </div>
                    </div>
                </section>

                <!-- Sezione Percorso di Partecipazione: Diventare Mecenate -->
                <section id="diventare" class="section">
                    <h2>{{ __('patron.becoming.title') }}</h2>
                    <h3>{{ __('patron.becoming.subtitle') }}</h3>
                    <div class="two-column">
                        <div class="col-left">
                            <h4>{{ __('patron.becoming.not_required.title') }}</h4>
                            <ul>
                                <li>{{ __('patron.becoming.not_required.item1') }}</li>
                                <li>{{ __('patron.becoming.not_required.item2') }}</li>
                                <li>{{ __('patron.becoming.not_required.item3') }}</li>
                                <li>{{ __('patron.becoming.not_required.item4') }}</li>
                                <li>{{ __('patron.becoming.not_required.item5') }}</li>
                            </ul>
                        </div>
                        <div class="col-right">
                            <h4>{{ __('patron.becoming.required.title') }}</h4>
                            <ul>
                                <li>{{ __('patron.becoming.required.item1') }}</li>
                                <li>{{ __('patron.becoming.required.item2') }}</li>
                                <li>{{ __('patron.becoming.required.item3') }}</li>
                                <li>{{ __('patron.becoming.required.item4') }}</li>
                                <li>{{ __('patron.becoming.required.item5') }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="text-block" style="margin-top: 40px;">
                        <p>{{ __('patron.becoming.conclusion') }}</p>
                    </div>
                </section>

                <!-- Sezione Percorso da zero a Mecenate di Successo: Passo dopo Passo -->
                <section id="percorso" class="section">
                    <h2>{{ __('patron.success_path.title') }}</h2>
                    <div class="timeline" role="list">
                        <div class="timeline-item" role="listitem">
                            <h4>{{ __('patron.success_path.phase1_title') }}</h4>
                            <p>{{ __('patron.success_path.phase1_desc') }}</p>
                        </div>
                        <div class="timeline-item" role="listitem">
                            <h4>{{ __('patron.success_path.phase2_title') }}</h4>
                            <p>{{ __('patron.success_path.phase2_desc') }}</p>
                        </div>
                        <div class="timeline-item" role="listitem">
                            <h4>{{ __('patron.success_path.phase3_title') }}</h4>
                            <p>{{ __('patron.success_path.phase3_desc') }}</p>
                        </div>
                        <div class="timeline-item" role="listitem">
                            <h4>{{ __('patron.success_path.phase4_title') }}</h4>
                            <p>{{ __('patron.success_path.phase4_desc') }}</p>
                        </div>
                    </div>
                </section>

                <!-- Sezione Modello di Valore: Impatto Misurabile nell'Ecosistema -->
                <section id="economico" class="section">
                    <h2>{{ __('patron.value_model.title') }}</h2>
                    <h3>{{ __('patron.value_model.subtitle') }}</h3>
                    <p style="text-align: center; margin-bottom: 30px;">{{ __('patron.value_model.intro') }}</p>

                    <h4>{{ __('patron.value_model.impact_areas.title') }}</h4>
                    <div class="icon-list">
                        <div class="icon-item">
                            <i class="fas fa-user-graduate" aria-hidden="true"></i>
                            <h4>{{ __('patron.value_model.impact_areas.areas.artist_development') }}</h4>
                            <p>Supporti la crescita professionale degli artisti nell'ecosistema sostenibile.</p>
                        </div>
                        <div class="icon-item">
                            <i class="fas fa-seedling" aria-hidden="true"></i>
                            <h4>{{ __('patron.value_model.impact_areas.areas.environmental_projects') }}</h4>
                            <p>Faciliti il contributo diretto a progetti ambientali verificati.</p>
                        </div>
                        <div class="icon-item">
                            <i class="fas fa-archive" aria-hidden="true"></i>
                            <h4>{{ __('patron.value_model.impact_areas.areas.cultural_preservation') }}</h4>
                            <p>Contribuisci alla preservazione digitale del patrimonio culturale.</p>
                        </div>
                        <div class="icon-item">
                            <i class="fas fa-users" aria-hidden="true"></i>
                            <h4>{{ __('patron.value_model.impact_areas.areas.community_building') }}</h4>
                            <p>Costruisci community attive focalizzate su sostenibilità e arte.</p>
                        </div>
                        <div class="icon-item">
                            <i class="fas fa-lightbulb" aria-hidden="true"></i>
                            <h4>{{ __('patron.value_model.impact_areas.areas.innovation_support') }}</h4>
                            <p>Sostieni l'innovazione artistica orientata alla sostenibilità.</p>
                        </div>
                    </div>

                    <h4 style="margin-top: 40px;">{{ __('patron.value_model.recognition_system.title') }}</h4>
                    <p style="text-align: center; margin-bottom: 30px;">{{ __('patron.value_model.recognition_system.description') }}</p>
                    
                    <div class="icon-list">
                        <div class="icon-item">
                            <i class="fas fa-chart-line" aria-hidden="true"></i>
                            <h4>{{ __('patron.value_model.recognition_system.elements.impact_metrics') }}</h4>
                            <p>Dashboard personalizzati con metriche di impatto verificate blockchain.</p>
                        </div>
                        <div class="icon-item">
                            <i class="fas fa-award" aria-hidden="true"></i>
                            <h4>{{ __('patron.value_model.recognition_system.elements.professional_recognition') }}</h4>
                            <p>Riconoscimento ufficiale del ruolo nell'ecosistema FlorenceEGI.</p>
                        </div>
                        <div class="icon-item">
                            <i class="fas fa-network-wired" aria-hidden="true"></i>
                            <h4>{{ __('patron.value_model.recognition_system.elements.network_prestige') }}</h4>
                            <p>Prestigio e autorevolezza nel network arte-sostenibilità.</p>
                        </div>
                        <div class="icon-item">
                            <i class="fas fa-infinity" aria-hidden="true"></i>
                            <h4>{{ __('patron.value_model.recognition_system.elements.legacy_documentation') }}</h4>
                            <p>Tracciabilità permanente del contributo all'impatto ambientale.</p>
                        </div>
                    </div>
                </section>

                <!-- Sezione Storie di Successo -->
                <section id="storie" class="section">
                    <h2>{{ __('patron.success_stories.title') }}</h2>
                    <p style="text-align: center;">{{ __('patron.success_stories.subtitle') }}</p>
                    <div class="stories-grid">
                        <div class="story-card">
                            <h4>{{ __('patron.success_stories.maria.title') }}</h4>
                            <ul>
                                <li><strong>{{ __('patron.success_stories.maria.context_label') }}</strong>:
                                    {{ __('patron.success_stories.maria.context') }}</li>
                                <li><strong>{{ __('patron.success_stories.maria.investment_label') }}</strong>:
                                    {{ __('patron.success_stories.maria.investment') }}</li>
                                <li><strong>{{ __('patron.success_stories.maria.action_label') }}</strong>:
                                    {{ __('patron.success_stories.maria.action') }}</li>
                                <li><strong>{{ __('patron.success_stories.maria.result_y1_label') }}</strong>:
                                    {{ __('patron.success_stories.maria.result_y1') }}</li>
                                <li><strong>{{ __('patron.success_stories.maria.result_y2_label') }}</strong>:
                                    {{ __('patron.success_stories.maria.result_y2') }}</li>
                            </ul>
                        </div>
                        <div class="story-card">
                            <h4>{{ __('patron.success_stories.giuseppe.title') }}</h4>
                            <ul>
                                <li><strong>{{ __('patron.success_stories.giuseppe.context_label') }}</strong>:
                                    {{ __('patron.success_stories.giuseppe.context') }}</li>
                                <li><strong>{{ __('patron.success_stories.giuseppe.investment_label') }}</strong>:
                                    {{ __('patron.success_stories.giuseppe.investment') }}</li>
                                <li><strong>{{ __('patron.success_stories.giuseppe.action_label') }}</strong>:
                                    {{ __('patron.success_stories.giuseppe.action') }}</li>
                                <li><strong>{{ __('patron.success_stories.giuseppe.result_y1_label') }}</strong>:
                                    {{ __('patron.success_stories.giuseppe.result_y1') }}</li>
                                <li><strong>{{ __('patron.success_stories.giuseppe.today_label') }}</strong>:
                                    {{ __('patron.success_stories.giuseppe.today') }}</li>
                            </ul>
                        </div>
                        <div class="story-card">
                            <h4>{{ __('patron.success_stories.anna.title') }}</h4>
                            <ul>
                                <li><strong>{{ __('patron.success_stories.anna.context_label') }}</strong>:
                                    {{ __('patron.success_stories.anna.context') }}</li>
                                <li><strong>{{ __('patron.success_stories.anna.investment_label') }}</strong>:
                                    {{ __('patron.success_stories.anna.investment') }}</li>
                                <li><strong>{{ __('patron.success_stories.anna.action_label') }}</strong>:
                                    {{ __('patron.success_stories.anna.action') }}</li>
                                <li><strong>{{ __('patron.success_stories.anna.result_label') }}</strong>:
                                    {{ __('patron.success_stories.anna.result') }}</li>
                                <li><strong>{{ __('patron.success_stories.anna.impact_label') }}</strong>:
                                    {{ __('patron.success_stories.anna.impact') }}</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Sezione Strumenti e Supporto -->
                <section id="strumenti" class="section">
                    <h2>{{ __('patron.tools_support.title') }}</h2>
                    <div class="feature-boxes">
                        <div class="feature-box">
                            <h3>{{ __('patron.tools_support.training.title') }}</h3>
                            <ul>
                                <li>{{ __('patron.tools_support.training.item1') }}</li>
                                <li>{{ __('patron.tools_support.training.item2') }}</li>
                                <li>{{ __('patron.tools_support.training.item3') }}</li>
                                <li>{{ __('patron.tools_support.training.item4') }}</li>
                            </ul>
                        </div>
                        <div class="feature-box">
                            <h3>{{ __('patron.tools_support.marketing.title') }}</h3>
                            <ul>
                                <li>{{ __('patron.tools_support.marketing.item1') }}</li>
                                <li>{{ __('patron.tools_support.marketing.item2') }}</li>
                                <li>{{ __('patron.tools_support.marketing.item3') }}</li>
                                <li>{{ __('patron.tools_support.marketing.item4') }}</li>
                            </ul>
                        </div>
                        <div class="feature-box">
                            <h3>{{ __('patron.tools_support.technology.title') }}</h3>
                            <ul>
                                <li>{{ __('patron.tools_support.technology.item1') }}</li>
                                <li>{{ __('patron.tools_support.technology.item2') }}</li>
                                <li>{{ __('patron.tools_support.technology.item3') }}</li>
                                <li>{{ __('patron.tools_support.technology.item4') }}</li>
                            </ul>
                        </div>
                        <div class="feature-box">
                            <h3>{{ __('patron.tools_support.community.title') }}</h3>
                            <ul>
                                <li>{{ __('patron.tools_support.community.item1') }}</li>
                                <li>{{ __('patron.tools_support.community.item2') }}</li>
                                <li>{{ __('patron.tools_support.community.item3') }}</li>
                                <li>{{ __('patron.tools_support.community.item4') }}</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Sezione Diverse Sfaccettature del Ruolo di Mecenate -->
                <section id="sfaccettature" class="section">
                    <h2>{{ __('patron.role_facets.title') }}</h2>
                    <div class="feature-boxes">
                        <div class="feature-box">
                            <h3>{{ __('patron.role_facets.local.title') }}</h3>
                            <ul>
                                <li>{{ __('patron.role_facets.local.item1') }}</li>
                                <li>{{ __('patron.role_facets.local.item2') }}</li>
                                <li>{{ __('patron.role_facets.local.item3') }}</li>
                                <li><strong>{{ __('patron.role_facets.local.earning') }}</strong>:
                                    {{ __('patron.role_facets.local.earning_amount') }}</li>
                            </ul>
                        </div>
                        <div class="feature-box">
                            <h3>{{ __('patron.role_facets.digital.title') }}</h3>
                            <ul>
                                <li>{{ __('patron.role_facets.digital.item1') }}</li>
                                <li>{{ __('patron.role_facets.digital.item2') }}</li>
                                <li>{{ __('patron.role_facets.digital.item3') }}</li>
                                <li><strong>{{ __('patron.role_facets.digital.potential') }}</strong>:
                                    {{ __('patron.role_facets.digital.potential_amount') }}</li>
                            </ul>
                        </div>
                        <div class="feature-box">
                            <h3>{{ __('patron.role_facets.social.title') }}</h3>
                            <ul>
                                <li>{{ __('patron.role_facets.social.item1') }}</li>
                                <li>{{ __('patron.role_facets.social.item2') }}</li>
                                <li>{{ __('patron.role_facets.social.item3') }}</li>
                                <li><strong>{{ __('patron.role_facets.social.mix') }}</strong>:
                                    {{ __('patron.role_facets.social.mix_desc') }}</li>
                            </ul>
                        </div>
                        <div class="feature-box">
                            <h3>{{ __('patron.role_facets.educator.title') }}</h3>
                            <ul>
                                <li>{{ __('patron.role_facets.educator.item1') }}</li>
                                <li>{{ __('patron.role_facets.educator.item2') }}</li>
                                <li>{{ __('patron.role_facets.educator.item3') }}</li>
                                <li><strong>{{ __('patron.role_facets.educator.revenue') }}</strong>:
                                    {{ __('patron.role_facets.educator.revenue_desc') }}</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Sezione Domande Frequenti (FAQ) con Accordion -->
                <section id="faq" class="section">
                    <h2>{{ __('patron.faq.title') }}</h2>
                    <div class="accordion-container" role="presentation">
                        <div class="accordion-item">
                            <button class="accordion-header" aria-expanded="false" aria-controls="faq-content-1"
                                id="faq-header-1" role="button">
                                <span>{{ __('patron.faq.blockchain.q') }}</span>
                                <i class="fas fa-chevron-down icon" aria-hidden="true"></i>
                            </button>
                            <div class="accordion-content" id="faq-content-1" role="region"
                                aria-labelledby="faq-header-1">
                                <p>{{ __('patron.faq.blockchain.a') }}</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <button class="accordion-header" aria-expanded="false" aria-controls="faq-content-2"
                                id="faq-header-2" role="button">
                                <span>{{ __('patron.faq.initial_investment.q') }}</span>
                                <i class="fas fa-chevron-down icon" aria-hidden="true"></i>
                            </button>
                            <div class="accordion-content" id="faq-content-2" role="region"
                                aria-labelledby="faq-header-2">
                                <p>{{ __('patron.faq.initial_investment.a') }}</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <button class="accordion-header" aria-expanded="false" aria-controls="faq-content-3"
                                id="faq-header-3" role="button">
                                <span>{{ __('patron.faq.find_artists.q') }}</span>
                                <i class="fas fa-chevron-down icon" aria-hidden="true"></i>
                            </button>
                            <div class="accordion-content" id="faq-content-3" role="region"
                                aria-labelledby="faq-header-3">
                                <p>{{ __('patron.faq.find_artists.a') }}</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <button class="accordion-header" aria-expanded="false" aria-controls="faq-content-4"
                                id="faq-header-4" role="button">
                                <span>{{ __('patron.faq.artist_leaves.q') }}</span>
                                <i class="fas fa-chevron-down icon" aria-hidden="true"></i>
                            </button>
                            <div class="accordion-content" id="faq-content-4" role="region"
                                aria-labelledby="faq-header-4">
                                <p>{{ __('patron.faq.artist_leaves.a') }}</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <button class="accordion-header" aria-expanded="false" aria-controls="faq-content-5"
                                id="faq-header-5" role="button">
                                <span>{{ __('patron.faq.time_commitment.q') }}</span>
                                <i class="fas fa-chevron-down icon" aria-hidden="true"></i>
                            </button>
                            <div class="accordion-content" id="faq-content-5" role="region"
                                aria-labelledby="faq-header-5">
                                <p>{{ __('patron.faq.time_commitment.a') }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Sezione Programma Pioneer -->
                <section id="pioneer" class="pioneer-program">
                    <h2>{{ __('patron.pioneer.title') }}</h2>
                    <p>{{ __('patron.pioneer.intro') }}</p>
                    <h3>{{ __('patron.pioneer.benefits_title') }}</h3>
                    <div class="pioneer-benefits-grid">
                        <div class="pioneer-benefit-item"><span class="icon" aria-hidden="true">🏆</span>
                            {{ __('patron.pioneer.benefit1') }}</div>
                        <div class="pioneer-benefit-item"><span class="icon" aria-hidden="true">📚</span>
                            {{ __('patron.pioneer.benefit2') }}</div>
                        <div class="pioneer-benefit-item"><span class="icon" aria-hidden="true">🎯</span>
                            {{ __('patron.pioneer.benefit3') }}</div>
                        <div class="pioneer-benefit-item"><span class="icon" aria-hidden="true">💰</span>
                            {{ __('patron.pioneer.benefit4') }}</div>
                        <div class="pioneer-benefit-item"><span class="icon" aria-hidden="true">🛡️</span>
                            {{ __('patron.pioneer.benefit5') }}</div>
                        <div class="pioneer-benefit-item"><span class="icon" aria-hidden="true">🤝</span>
                            {{ __('patron.pioneer.benefit6') }}</div>
                    </div>
                    <h4>{{ __('patron.pioneer.requirements_title') }}</h4>
                    <div class="pioneer-requirements">
                        <ul>
                            <li>{{ __('patron.pioneer.requirement1') }}</li>
                            <li>{{ __('patron.pioneer.requirement2') }}</li>
                            <li>{{ __('patron.pioneer.requirement3') }}</li>
                        </ul>
                    </div>
                </section>

                <!-- Sezione Inizia Oggi il Tuo Percorso -->
                <section id="inizia" class="section">
                    <h2>{{ __('patron.start_today.title') }}</h2>
                    <div class="onboarding-steps">
                        <div class="onboarding-step-card">
                            <span class="step-number">1</span>
                            <i class="fas fa-book-open step-icon" aria-hidden="true"></i>
                            <h4>{{ __('patron.start_today.step1.title') }}</h4>
                            <ul>
                                <li>{{ __('patron.start_today.step1.item1') }}</li>
                                <li>{{ __('patron.start_today.step1.item2') }}</li>
                                <li>{{ __('patron.start_today.step1.item3') }}</li>
                            </ul>
                        </div>
                        <div class="onboarding-step-card">
                            <span class="step-number">2</span>
                            <i class="fas fa-user-plus step-icon" aria-hidden="true"></i>
                            <h4>{{ __('patron.start_today.step2.title') }}</h4>
                            <ul>
                                <li>{{ __('patron.start_today.step2.item1') }}</li>
                                <li>{{ __('patron.start_today.step2.item2') }}</li>
                                <li>{{ __('patron.start_today.step2.item3') }}</li>
                            </ul>
                        </div>
                        <div class="onboarding-step-card">
                            <span class="step-number">3</span>
                            <i class="fas fa-graduation-cap step-icon" aria-hidden="true"></i>
                            <h4>{{ __('patron.start_today.step3.title') }}</h4>
                            <ul>
                                <li>{{ __('patron.start_today.step3.item1') }}</li>
                                <li>{{ __('patron.start_today.step3.item2') }}</li>
                                <li>{{ __('patron.start_today.step3.item3') }}</li>
                            </ul>
                        </div>
                        <div class="onboarding-step-card">
                            <span class="step-number">4</span>
                            <i class="fas fa-rocket step-icon" aria-hidden="true"></i>
                            <h4>{{ __('patron.start_today.step4.title') }}</h4>
                            <ul>
                                <li>{{ __('patron.start_today.step4.item1') }}</li>
                                <li>{{ __('patron.start_today.step4.item2') }}</li>
                                <li>{{ __('patron.start_today.step4.item3') }}</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Sezione Contatti e Risorse -->
                <section id="contatti" class="section">
                    <h2>{{ __('patron.contacts.title') }}</h2>
                    <div class="contact-resources">
                        <div class="contact-block">
                            <h3>{{ __('patron.contacts.contact_title') }}</h3>
                            <ul>
                                <li><i class="fas fa-envelope icon" aria-hidden="true"></i> <a
                                        href="mailto:academy@florenceegi.com"
                                        itemprop="email">{{ __('patron.contacts.email') }}</a></li>
                                <li><i class="fab fa-whatsapp icon" aria-hidden="true"></i> <a
                                        href="https://wa.me/[numero]?text=MECENATE"
                                        itemprop="url">{{ __('patron.contacts.whatsapp') }}</a></li>
                                <li><i class="fas fa-globe icon" aria-hidden="true"></i> <a
                                        href="https://www.florenceegi.com/diventa-mecenate" target="_blank"
                                        itemprop="url">{{ __('patron.contacts.website') }}</a></li>
                                <li><i class="fab fa-youtube icon" aria-hidden="true"></i> <a
                                        href="https://www.youtube.com/c/FlorenceEGIAcademy" target="_blank"
                                        itemprop="url">{{ __('patron.contacts.youtube') }}</a></li>
                            </ul>
                        </div>
                        <div class="download-block">
                            <h3>{{ __('patron.contacts.download_title') }}</h3>
                            <ul>
                                <li><i class="fas fa-book icon" aria-hidden="true"></i>
                                    {{ __('patron.contacts.download1') }}</li>
                                <li><i class="fas fa-file-alt icon" aria-hidden="true"></i>
                                    {{ __('patron.contacts.download2') }}</li>
                                <li><i class="fas fa-video icon" aria-hidden="true"></i>
                                    {{ __('patron.contacts.download3') }}</li>
                                <li><i class="fas fa-clipboard-list icon" aria-hidden="true"></i>
                                    {{ __('patron.contacts.download4') }}</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Messaggio Personale e Slogan Finale -->
                <section class="personal-message">
                    <h2>{{ __('patron.final_message.title') }}</h2>
                    <p>{{ __('patron.final_message.paragraph1') }}</p>
                    <p>{{ __('patron.final_message.paragraph2') }}</p>
                    <p>{{ __('patron.final_message.paragraph3') }}
                        <strong>{{ __('patron.final_message.paragraph3_strong') }}</strong></p>
                    <p>{{ __('patron.final_message.paragraph4') }}</p>
                    <p
                        style="font-family: 'Playfair Display', serif; font-size: 1.8em; font-weight: 700; color: var(--oro-fiorentino);">
                        {{ __('patron.final_message.choice') }}</p>
                    <p class="final-slogan">{{ __('patron.final_message.slogan') }}</p>
                    <p class="signature">{{ __('patron.final_message.signature') }}</p>
                </section>
            </main>
        </div>
    </div>

    <!-- Footer della pagina (fuori dal main content per design) -->
    <footer role="contentinfo">
        <p>{{ __('patron.footer.tagline') }}</p>
        <p>&copy; 2025 {{ __('patron.footer.copyright') }} <a href="#">{{ __('patron.footer.privacy') }}</a>
            | <a href="#">{{ __('patron.footer.terms') }}</a></p>
    </footer>

    <!-- Script JavaScript per la funzionalità dell'accordion nelle FAQ e dei menu hamburger -->
    <script>
        // Funzionalità per l'accordion delle FAQ
        document.querySelectorAll('.accordion-header').forEach(header => {
            header.addEventListener('click', () => {
                const content = header.nextElementSibling;
                const isExpanded = header.getAttribute('aria-expanded') === 'true' || false;
                header.setAttribute('aria-expanded', !isExpanded);

                if (content.classList.contains('open')) {
                    content.classList.remove('open');
                    content.style.maxHeight = null;
                    content.style.paddingBottom = '0';
                    header.classList.remove('active');
                } else {
                    content.classList.add('open');
                    content.style.maxHeight = content.scrollHeight + 'px';
                    content.style.paddingBottom = '25px';
                    header.classList.add('active');
                }
            });
        });

        // Funzionalità per il menu hamburger della sidebar
        const sidebarHamburger = document.getElementById('sidebar-hamburger');
        const sidebar = document.getElementById('sidebar');
        const sidebarMenu = document.getElementById('sidebar-menu');

        sidebarHamburger.addEventListener('click', () => {
            const isSidebarOpen = sidebar.classList.toggle('open');
            sidebarHamburger.setAttribute('aria-expanded', isSidebarOpen);
            if (isSidebarOpen) {
                document.body.style.overflow = 'hidden'; // Blocca lo scroll del body
            } else {
                document.body.style.overflow = '';
            }
        });

        // Funzionalità per il menu hamburger della top nav
        const topNavHamburger = document.getElementById('top-nav-hamburger');
        const topNavOverlay = document.getElementById('top-nav-overlay');

        topNavHamburger.addEventListener('click', () => {
            const isTopNavOpen = topNavOverlay.classList.toggle('open');
            topNavHamburger.setAttribute('aria-expanded', isTopNavOpen);
            if (isTopNavOpen) {
                document.body.style.overflow = 'hidden'; // Blocca lo scroll del body
            } else {
                document.body.style.overflow = '';
            }
        });

        // Chiudi la sidebar quando si clicca su un link della sidebar o fuori da essa (solo su mobile)
        document.addEventListener('click', (event) => {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnSidebarHamburger = sidebarHamburger.contains(event.target);
            const isClickInsideTopNav = topNavOverlay.contains(event.target);
            const isClickOnTopNavHamburger = topNavHamburger.contains(event.target);

            // Chiudi sidebar se aperta e click esterno
            if (!isClickInsideSidebar && !isClickOnSidebarHamburger && sidebar.classList.contains('open') && window
                .innerWidth <= 768) {
                sidebar.classList.remove('open');
                sidebarHamburger.setAttribute('aria-expanded', false);
                document.body.style.overflow = '';
            }
            // Chiudi top nav overlay se aperta e click esterno
            if (!isClickInsideTopNav && !isClickOnTopNavHamburger && topNavOverlay.classList.contains('open') &&
                window.innerWidth <= 768) {
                topNavOverlay.classList.remove('open');
                topNavHamburger.setAttribute('aria-expanded', false);
                document.body.style.overflow = '';
            }
        });

        // Chiudi la sidebar e top nav quando si clicca su un link (per il routing interno)
        document.querySelectorAll('.sidebar-link, .top-nav-overlay a').forEach(link => {
            link.addEventListener('click', () => {
                // Chiudi sidebar se aperta e mobile
                if (sidebar.classList.contains('open') && window.innerWidth <= 768) {
                    sidebar.classList.remove('open');
                    sidebarHamburger.setAttribute('aria-expanded', false);
                    document.body.style.overflow = '';
                }
                // Chiudi top nav se aperta e mobile
                if (topNavOverlay.classList.contains('open') && window.innerWidth <= 768) {
                    topNavOverlay.classList.remove('open');
                    topNavHamburger.setAttribute('aria-expanded', false);
                    document.body.style.overflow = '';
                }
            });
        });

        // Scroll liscio per i link interni
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    // Calcola lo scroll offset (escludendo l'altezza di eventuali header/navbar fisse)
                    // Non applichiamo offset fissi qui, ma lasciamo che il browser gestisca scrollIntoView
                    // che di solito è sufficiente per elementi non coperti da barre fisse con position: fixed
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Gestione active link nella sidebar quando si scorre
        const sections = document.querySelectorAll('.section[id]');
        const sidebarLinks = document.querySelectorAll('.sidebar-link');

        window.addEventListener('scroll', () => {
            let current = '';
            // Determine the current section based on scroll position
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                // Adjusting the offset for better active link detection (e.g., 1/3 of the screen height)
                const offset = window.innerHeight * 0.33;
                if (pageYOffset >= sectionTop - offset) {
                    current = section.getAttribute('id');
                }
            });

            // Update active class for sidebar links
            sidebarLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });

        // Imposta il link attivo all'avvio (per il caricamento della pagina su una sezione specifica o su intro)
        document.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash;
            if (hash) {
                const targetElement = document.querySelector(hash);
                if (targetElement) {
                    // Scroll into view behavior is handled by the general smooth scroll, just set active link
                    sidebarLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === hash) {
                            link.classList.add('active');
                        }
                    });
                }
            } else {
                // Attiva il link 'Introduzione' se non c'è hash all'avvio
                const firstLink = document.querySelector('.sidebar-link[href="#intro"]');
                if (firstLink) {
                    firstLink.classList.add('active');
                }
            }
        });
    </script>

</body>

</html>
