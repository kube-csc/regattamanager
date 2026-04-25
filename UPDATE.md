## Update Anleitung

**Version V00.02.01**

***Neue Funktionen***
- Warteliste für Team-Meldungen (Status "Warteliste" inkl. Anzeige im Frontend).
- Mannschaftssteckbrief / Team-Profil-Ansicht (Blätter-Navigation über gemeldete Teams, Detailansicht inkl. Erfolgen/Teilnahmen).
- Sprecherkarten-CSV (Sprecherkarten-Export):
  - Spalte **"Teilnahmen"**: Anzahl vergangener Teilnahmen (wie im Mannschaftssteckbrief).
  - Spalte **"Erfolge"**: letzte Final-Ergebnisse aus früheren Veranstaltungen (aktuelle Regatta wird nicht berücksichtigt), mehrzeilig formatiert als: Platz X – Rennenbezeichnung – dd.mm.YYYY.

***Wichtige Hinweise (Migration)***
- Migrationen ergänzt/angepasst, damit die Datenbank-Struktur wieder konsistent ist (u.a. Sportarten-Bereich und Textfelder).
- Nach dem Update bitte die Datenbank-Migrationen ausführen.

**Version V00.02.00**

Es wird https://github.com/kube-csc/vereinsverwaltung  GitHub Projekt Vereinsverwaltung ab V00.10.01 benötigt

***Neue Funktionen***
FAQ Bereich wurde hinzugefügt
