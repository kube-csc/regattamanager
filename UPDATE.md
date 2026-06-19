## Update Anleitung
**Version V00.02.01**

Es wird https://github.com/kube-csc/vereinsverwaltung  GitHub Projekt Vereinsverwaltung ab V00.10.05 benötigt

***Neue Funktionen***
**Header Bild**
- Dynamischer Header / Hero-Bereich: Der Header der Anwendung passt sich automatisch an die aufgerufene Domain an.
    - Für jede Domain kann in der Vereinsverwaltung unter dem Menüpunkt **Vereinsserver** ein Hero-Hintergrundbild hinterlegt werden, das als vollflächiges Bild im oberen Seitenbereich erscheint.
    - Zusätzlich kann dort eine Akzentfarbe für Header, Footer-Balken, Buttons und den Zurück-nach-oben-Button festgelegt werden.
    - Sind kein Bild und keine Farbe hinterlegt, greift das Standard-Design.
    - Die Eingabe erfolgt über ein Formular in der Vereinsverwaltung – es sind keine manuellen Datenbankänderungen erforderlich.
  Achtung folgendes ist in der .env Datei zu setzen:
   - VEREIN_URL=vereindomain.de 
  - Hinweis: Unterstriche in VEREIN_URL werden zu Leerzeichen umgewandelt; Unterstriche vermeiden.

**Gemeldete Teams**
- Mannschaftssteckbrief / Team-Profil-Ansicht (Blätter-Navigation über gemeldete Teams, Detailansicht inkl. Erfolgen/Teilnahmen).
  
  **Sprecherkarten**
- Sprecherkarten-CSV (Sprecherkarten-Export):
  [domain]/API/Sprecherkarten
  - Spalte **"Teilnahmen"**: Anzahl vergangener Teilnahmen.
  - Spalte **"Erfolge"**: letzte Final-Ergebnisse aus früheren Veranstaltungen (aktuelle Regatta wird nicht berücksichtigt)
  - Optional kann der Erfolgs-Filter beim Export gesteuert werden:
    - nur Finale: [domain]/API/Sprecherkarten?finale=1
    - alle Ergebnisse (Finale + Nicht‑Finale): [domain]/API/Sprecherkarten?finale=0

***Wichtige Hinweise (Migration)***
- Migrationen ergänzt/angepasst, damit die Datenbank-Struktur wieder konsistent ist (u.a. Sportarten-Bereich und Textfelder).
- Nach dem Update bitte die Datenbank-Migration ausführen.

**Version V00.02.00**

Es wird https://github.com/kube-csc/vereinsverwaltung  GitHub Projekt Vereinsverwaltung ab V00.10.01 benötigt

***Neue Funktionen***
FAQ Bereich wurde hinzugefügt
