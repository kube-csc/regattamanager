<h1>Internetauftritt von einen Regattamanagesystem</h1>
<p>Version: V00.02.01</p>
<p>
Ausgelegt z.B.&nbsp;für einen Verein mit verschiedenen Abteilungen / Sportarten
</p>
Beispiel ein Kanu Verein mit Abteilungen / Sportarten:
    <ul>
      <li>Jugend</li>
      <li>Wandersport</li>
      <li>Rennsport</li>
      <li>Drachenboot mit drei Mannschaften</li>
      <li>SUP</li>
    </ul>

<a href="https://www.day-of-dragons.de">Beispiel eines Frontend</a>

<h2>Installierte Programme / Temples</h2>
<ul>
  <li>Installation Laravel 11.* mit jetstream 4.* , livewire 3.* teams  und tailwindcss 3.*
    <ul>
        <li><a href="https://jetstream.laravel.com/4.x/introduction.html">jetstream 5.x Anleitung</a></li>
        <li><a href="https://jetstream.laravel.com/3.x/stacks/livewire.html">livewire</a></li>
    </ul>
  </li>
  <li><a href="https://boxicons.com/">boxicons</a>(Forntend)</li>
  <li><a href="https://tailwindcss.com/">Tailwindcss</a>(Backend)</li>
  <li><a href="https://bootstrapmade.com/squadfree-free-bootstrap-template-creative/">BootstrapMade.com </a></li>
  <li>.htaccess für ionos.de (1und1.de) Server</li>
</ul>

<h2>Benötigte Lizenzen</h2>
Es wird eine Lizenz für
<a href="https://bootstrapmade.com/squadfree-free-bootstrap-template-creative/">Squadfree von bootstrapmade</a>
benötigt.

<h2>Frontend</h2>
<ul>
    <li>Header ist abhängig von Event</li>
    <li>Leanding Page
        <ul>
           <li>Beschreibung der Events</li>
           <li>Dokumente zum Herunterladen
              <ul>
                  <li>Ausschreibung</li>
                  <li>Programm</li>
                  <li>Ergebnisse</li>
                  <li>Flyer / Plakat</li>
              </ul>
           </li>
        </ul>
    </li>
    <li>Ausschreibung</li>
    <li>Meldung der Teams ()Warteliste (Teams können bei voller Teilnehmerzahl auf „Warteliste“ gesetzt werden)</li>
    <li>Gemeldete Teams</li>
    <li>Mannschaftssteckbrief / Team-Profil (Detailansicht gemeldeter Teams inkl. Blätter-Navigation)</li>
    <li>Anfahrt **</li>
    <li>Footer
      <ul> 
        <li>Impressum</li>
        <li>Datenschutzerklärung</li>
      </ul>
      </li>
</ul>

* Begriff wird in der .env eingetragen  
  ** Anfahrt kann in der .env aktiviert bzw. deaktiviert werden

<h3>Dynamisches Branding (Header/Hero) pro Domain</h3>
<p>
Das Frontend kann je nach aufgerufener Domain automatisch Akzentfarbe und Hero-Hintergrundbild setzen.
Die Logik befindet sich in der Datei resources/views/layouts/header.blade.php.
</p>
<p>
Ablauf:
</p>
<ul>
  <li>
    Ermittlung der aktuellen Domain über die Laravel-URL (Host) und Entfernen von „www.“.
  </li>
  <li>
    Auswahl der passenden Event-Gruppe aus der Tabelle event_groups:
    <ul>
      <li>domain = aktuelle Domain (ohne „www.“)</li>
      <li>visible = 1</li>
      <li>es wird der neueste Eintrag verwendet (nach id absteigend)</li>
    </ul>
  </li>
  <li>
    <b>Akzentfarbe</b> (Feld accentColor, optional):
    Wenn gesetzt (nicht leer), werden Header/Footer-Akzente, Buttons und „Back-to-top“ per Inline-CSS überschrieben.
  </li>
  <li>
    <b>Hero-Bild</b> (Feld headerBild, optional):
    Wenn gesetzt, wird das Hero-Hintergrundbild per CSS auf /storage/groupEventHeader/&lt;dateiname&gt; gesetzt.
    Die vollständige URL wird mit VEREIN_URL als Basis aufgebaut (siehe unten).
  </li>
</ul>

<h2>API</h2>
<ul>
  <li><b>Sprecherkarten (CSV)</b>
    <ul>
      <li><b>URL</b>: /API/Sprecherkarten</li>
      <li><b>Option (Erfolge-Filter)</b>: Standardmäßig werden nur Finale berücksichtigt.
        <ul>
          <li><b>nur Finale</b>: /API/Sprecherkarten?finale=1</li>
          <li><b>alle Ergebnisse</b> (Finale + Nicht‑Finale): /API/Sprecherkarten?finale=0</li>
        </ul>
      </li>
      <li><b>Response</b>: CSV-Download (Dateiname: Sprecherkarte.csv)</li>
      <li><b>Format</b>:
        <ul>
          <li>Trennzeichen: ;</li>
          <li>Encoding: UTF-8 (Excel-kompatibel)</li>
          <li>Zeilenumbrüche innerhalb von Feldern (z.B. in <b>Erfolge</b>) werden als echte Newlines ausgegeben.</li>
        </ul>
      </li>
      <li><b>Welche Teams werden exportiert?</b>
        <ul>
          <li>Alle gemeldeten Teams der <b>aktuellen Veranstaltung</b> (die Veranstaltung wird über die aktuelle Domain ermittelt).</li>
          <li>Wenn für die Domain keine Veranstaltung gefunden wird, wird eine Fehlermeldung zurückgegeben.</li>
        </ul>
      </li>
      <li><b>CSV-Spalten</b> (Header-Zeile):
        <ul>
          <li><b>Nr.</b>: laufende Nummer im Export</li>
          <li><b>Datum</b>: Anmeldedatum (Format dd.mm.YYYY)</li>
          <li><b>Teamname</b></li>
          <li><b>Verein / Firma</b></li>
          <li><b>Ort</b></li>
          <li><b>Wertung</b>: Rennklasse / Wertungsgruppe</li>
          <li><b>Teambeschreibung</b>: Teamtext (Zeilenumbrüche werden in der CSV übernommen)</li>
          <li><b>Teilnahmen</b></li>
          <li><b>Erfolge</b></li>
        </ul>
      </li>
      <li><b>Zusatzfelder</b> – analog zum Regatta‑Team‑Steckbrief:
        <ul>
          <li>
            <b>Teilnahmen</b>: Anzahl vergangener Teilnahmen des Teams (frühere Veranstaltungen).
          </li>
          <li>
            <b>Erfolge</b>: letzte Final-Ergebnisse, mehrzeilig formatiert als:<br>
            Platz X – Rennenbezeichnung – dd.mm.YYYY
            <ul>
              <li>es werden nur Finale berücksichtigt</li>
              <li>es werden nur veröffentlichte/abgeschlossene Ergebnisse berücksichtigt</li>
              <li>die <b>aktuelle Regatta</b> wird dabei nicht berücksichtigt</li>
              <li>pro früherer Veranstaltung wird das jeweils letzte Ergebnis ausgegeben (wie im Steckbrief)</li>
            </ul>
          </li>
        </ul>
      </li>
      <li><b>Hinweis</b>: Die Felder <b>Teilnahmen</b> und <b>Erfolge</b> werden nur berechnet, wenn das Team über mehrere Veranstaltungen eindeutig wiedererkannt werden kann.</li>
    </ul>
  </li>
</ul>

<h2>Installation</h2>
<ul>
   <li>git clone https://github.com/kube-csc/regattamangaer.git</li>
   <li>.env Datei ausfüllen (Es werden auch Informationen über den Verein abgefragt.)
     <ul>
       <li>
         <b>VEREIN_URL</b>: Basis-URL (öffentlich) für die Generierung von Header-Bild-URLs im Frontend.
         Verwendet wird (vereinfacht): VEREIN_URL + /storage/groupEventHeader/ + Dateiname aus headerBild.
         <br>
         Hinweis: Beim Erstellen der URL werden Unterstriche im VEREIN_URL-Wert in Leerzeichen umgewandelt.
         Unterstriche im Wert würden dadurch zu Leerzeichen und die URL wäre i.d.R. ungültig. Empfehlung: VEREIN_URL ohne Unterstriche pflegen.
       </li>
     </ul>
   </li>
   <li>cd vereinsverwaltung</li>
   <li>curl -sS https://getcomposer.org/installer</li>
   <li>php composer.phar</li>
   <li>php composer.phar install</li>
   <li>Die Unterordner unter "/storage/app/public/" sollten angelegt sein, wenn nicht von hand anlegen
       <ul>
         <li>boardPortrait</li>
          <li>groupEventHeader (öffentlich: /storage/groupEventHeader)</li>
       </ul>
   </li>
   <li>In Ordner "/recources/views/textimport ist folgendes zu Bearbeiten:
   <ul>
     <li>cssColor.blade.php anlegen und mit der Vorlage von cssColor_example.blade.php ausfüllen</li>
     <li>recht.blade.php anlegen und mit der Vorlage von recht_example.blade.php ausfüllen</li>
     <li>anfahrt.blade.php anlegen und mit der Vorlage von anfahrt_example.blade.php ausfüllen</li>
     <li>mailImpressum.blade.php anlegen und mit der Vorlage von mailImpressum_example.blade.php ausfüllen</li>
     <li>footer.blade.php anlegen und mit der Vorlage von footer_example.blade.php ausfüllen</li>
   </ul>
   <li>In Ordner "public sind die folgenden Dateien anzulegen:
       <ul>
         <li>apple-touch-icon.png</li>
         <li>favicon.ico</li>
       </ul>
   </li>
   <li>php artisan storage:link</li>
</ul>

<h2>Backend</h2>
<h3>Vereinsverwaltung</h3>
<h4>Installation</h4>
<p>
Die Verwaltung der Userdaten der Trainer und Abteilungen muss die APP Vereinsverwaltung installiert werden.
Alternativ müssen die Daten in der Datenbank direkt eingetragen werden.
<a href="https://github.com/kube-csc/vereinsverwaltung" target="_blank">zum GitHub Projekt Vereinsverwaltung ab V00.10.01</a>
</p>

<h4>Demodaten</h4>
<p>
  Email: info@info.de<br>
  Password: password
</p>
<h4>Veraltete Daten:</h4>
<ul>
    <li>Userdaten der Trainer</li>
    <li>Abteilungen</li>
</ul>

<h2>Zugehörige Projekte</h2>
<h3>Präsentation der Regatta</h3>
<p>
    Für die live Präsentation der Regatta kann folgende Software verwendet werden.<br>
    Die Version V00.14.XX <a href="https://github.com/kube-csc/regattaView.git" target="_blank">https://github.com/kube-csc/regattaView.git</a> 
    ist kompatibel mit der Version V00.10.XX <a href="https://github.com/kube-csc/vereinsverwaltung.git" target="_blank">https://github.com/kube-csc/vereinsverwaltung.git</a>.
</p>
<h3>Helferlisten</h3>
<p>
Kurse ist eine APP für ein Kursbuchungssystem. Es hat folgende Funktionen:<br>
Kursübersicht: Anzeige der verfügbaren Kurse und Termine.<br>
Buchung: Möglichkeit, Teilnehmer für Kurse zu buchen und die Startzeit zu ändern.<br>
Teilnehmerverwaltung: Hinzufügen und Entfernen von Teilnehmern<br>
Die Version V00.01.XX <a href="https://github.com/kube-csc/helferplanung.git" target="_blank">https://github.com/kube-csc/helferplanung.git</a>
ist kompatibel mit der Version V00.04.XX <a href="https://github.com/kube-csc/helferplanung.git" target="_blank">https://github.com/kube-csc/vereinsverwaltung.git</a>.
</p>
<h3>Kurse</h3>
<p>
Die App bietet ein Kursbuchungssystem, das auch für Fahrten oder Trainings verwendet werden kann. Sie hat folgende Funktionen:<br>
Verwaltung: Sportgeräte, Räume usw. können verwaltet werden.<br>
Teilnehmer: Accounts anlegen und verwalten.<br>
Kursübersicht: Anzeige der verfügbaren Kurse und Termine sowie Informationen zu den Kursen, Trainings, Übungen oder Fahrten, die angeboten werden.<br>
Trainer bzw. Fahrtenleiter: Trainer bzw. Fahrtenleiter können Termine für die Kurse anlegen und bearbeiten.<br>
Sportgeräte und Räume: Zu den Kursen können Sportgeräte, Räume usw. zugeordnet werden.<br>
Buchung: Teilnehmer können Kurse buchen und ihre eigenen Buchungen bearbeiten.<br>
Teilnehmerverwaltung: Hinzufügen und Entfernen von Teilnehmern durch Trainer bzw. Fahrtenleiter.<br>
Die Version  V00.02.01 <a href="https://github.com/kube-csc/kurse.git" target="_blank">https://github.com/kube-csc/kurse.git</a> ist kompatibel mit der Version ab V00.10.03
<a href="https://github.com/kube-csc/vereinsverwaltung" target="_blank"></a>.
</p>
<hr>
<br>

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
