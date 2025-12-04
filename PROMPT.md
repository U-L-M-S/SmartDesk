**Projekt-PROMPT: "SmartDesk" - Das modulare Unternehmens-Dashboard in Laravel (Dockerized)**

---

### Ziel des Projekts

Erstelle ein universelles, modular aufgebautes **Business-Dashboard** namens "SmartDesk", das Unternehmen ein Tool für **Support-Tickets**, **Dokumenten-Management**, **Benachrichtigungen**, **Team-Management** und **Rollenbasierten Zugriff** bereitstellt. Die Anwendung soll skalierbar, dockerisiert und in Microservice-Architektur erweiterbar sein. Sie muss mit einem einzigen Befehl (`docker compose up --build`) lauffähig sein.

---

### Hauptfunktionen

1. **Benutzer- und Rollenverwaltung**

   * Registrieren, Einladen, Aktivieren/Deaktivieren von Nutzern
   * Rollen: Admin, Manager, Mitarbeiter
   * Berechtigungen: CRUD granular pro Modul konfigurierbar

2. **Support-Ticket-Modul**

   * Nutzer können Tickets erstellen, kommentieren und verfolgen
   * Agenten weisen Tickets zu, setzen Status (Offen, In Arbeit, Geschlossen)
   * E-Mail-Benachrichtigung bei neuen oder veränderten Tickets

3. **Dokumenten-Management**

   * Upload, Versionierung, Tagging und Freigabe von Dateien
   * Zugriffskontrolle je nach Benutzerrolle
   * Optional: Integration mit S3 oder MinIO zur persistenten Dateispeicherung

4. **Benachrichtigungssystem**

   * Globale System- oder Modulbezogene Benachrichtigungen (E-Mail und UI)
   * Queue-gesteuert (Laravel Queues + Redis)

5. **Dashboard-Startseite (UI)**

   * Widget-basiert: z. B. neue Tickets, aktive Benutzer, neue Dokumente
   * Jeder Benutzer kann sich ein persönliches Dashboard konfigurieren

6. **RESTful API (für mobile/Frontend-Integration)**

   * Authentifizierte Endpunkte für alle Funktionen
   * Tokenbasierte Authentifizierung mit Laravel Sanctum oder Passport

7. **CI/CD mit Drone oder GitHub Actions**

   * Automated Build/Test/Lint Pipeline
   * Deployment Container Build als Ziel

---

### Technologie-Stack

* **Backend:** Laravel 10, PHP 8.2
* **Frontend:** Blade + TailwindCSS (optional: Vue.js Modulintegration später)
* **Datenbank:** PostgreSQL (alternativ: MySQL)
* **Storage:** Lokales Volume (optional: S3-kompatibel via MinIO)
* **Queue:** Redis
* **CI/CD:** Drone CI (alternativ: GitHub Actions)
* **Containerisierung:** Docker Compose (PHP-FPM, NGINX, Redis, DB, Mailhog)

---

### Container-Struktur

```yaml
docker-compose.yml:

services:
  app:
    build: .
    volumes:
      - .:/var/www
    depends_on:
      - db
      - redis
    environment:
      - APP_ENV=local
    networks:
      - backend

  web:
    image: nginx:alpine
    volumes:
      - .:/var/www
      - ./nginx:/etc/nginx/conf.d
    ports:
      - "8080:80"
    depends_on:
      - app
    networks:
      - backend

  db:
    image: postgres:13
    environment:
      POSTGRES_DB=smartdesk
      POSTGRES_USER=user
      POSTGRES_PASSWORD=secret
    volumes:
      - pgdata:/var/lib/postgresql/data
    networks:
      - backend

  redis:
    image: redis:alpine
    networks:
      - backend

  mailhog:
    image: mailhog/mailhog
    ports:
      - "8025:8025"
    networks:
      - backend

volumes:
  pgdata:

networks:
  backend:
```

---

### Ordnerstruktur

```bash
smartdesk/
├── app/
│   ├── Http/
│   ├── Models/
│   ├── Policies/
│   ├── Services/
├── modules/
│   ├── Tickets/
│   ├── Documents/
│   ├── Notifications/
├── config/
├── database/
│   ├── migrations/
├── public/
├── resources/
│   ├── views/
│   ├── css/
│   ├── js/
├── routes/
│   ├── web.php
│   ├── api.php
├── tests/
├── docker-compose.yml
├── Dockerfile
├── README.md
```

---

### Beziehungen zwischen den Modulen (grafisch beschrieben)

* **Users**

  * hat Rollen, Berechtigungen
  * besitzt Tickets, Dokumente, Dashboard-Einstellungen

* **Tickets**

  * gehören zu einem Benutzer (Ersteller)
  * können Agenten zugewiesen werden (Support-Rolle)
  * Status-Historie als Log

* **Dokumente**

  * haben Versionierung und Freigabestatus
  * Sichtbarkeit abhängig von Rolle/Zugriffsrechten

* **Benachrichtigungen**

  * ausgelöst durch Tickets, Datei-Uploads, Kommentare
  * gerichtet an Benutzer/Teams

---

### Tests und QA

* Unit-Tests für Tickets, Dokumente, Benutzerlogik
* Feature-Tests für API-Endpunkte
* Docker-Setup für CI

---

### Zusätzliche Ideen

* Audit Trail: Wer hat wann was verändert (für DSGVO)
* Export-Funktionen (CSV, PDF)
* Mehrsprachigkeit (EN/DE)
* OAuth-Integration (Login via Google, GitHub)

---

### Zielgruppenbezug

* Jedes Unternehmen kann dieses Tool als internes Dashboard einsetzen
* Unternehmen ohne eigenes Supportsystem profitieren von den Ticket- und Dokumentenfunktionen
* Entwicklerteams erhalten CI/CD-Vorlage + API-Plattform

---

**Erwartetes Ergebnis:** Ein dockerisiertes, sauberes, erweiterbares Laravel-Projekt mit modularer Architektur, hoher Codequalität und sinnvollem Business-Nutzen für jede Firma.
