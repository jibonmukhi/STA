<?php

return [
    // General
    'courses' => 'Corsi',
    'course' => 'Corso',
    'course_list' => 'Elenco Corsi',
    'course_planning' => 'Pianificazione Corsi',
    'course_management' => 'Gestione Corsi',

    // Navigation
    'dashboard' => 'Dashboard',
    'back_to_list' => 'Torna all\'Elenco',
    'back_to_course' => 'Torna al Corso',
    'all_courses' => 'Tutti i Corsi',

    // Actions
    'add_course' => 'Aggiungi Corso',
    'create_course' => 'Crea Corso',
    'edit_course' => 'Modifica Corso',
    'update_course' => 'Aggiorna Corso',
    'delete_course' => 'Elimina Corso',
    'view' => 'Visualizza',
    'edit' => 'Modifica',
    'delete' => 'Elimina',
    'create' => 'Crea',
    'update' => 'Aggiorna',
    'save' => 'Salva',
    'cancel' => 'Annulla',

    // Form Labels
    'title' => 'Titolo',
    'course_title' => 'Titolo del Corso',
    'course_code' => 'Codice Corso',
    'description' => 'Descrizione',
    'course_programme' => 'Programma del Corso',
    'objectives' => 'Obiettivi',
    'category' => 'Categoria',
    'level' => 'Livello',
    'duration' => 'Durata',
    'duration_hours' => 'Durata (Ore)',
    'credits' => 'Crediti',
    'price' => 'Prezzo',
    'instructor' => 'Istruttore',
    'prerequisites' => 'Prerequisiti',
    'delivery_method' => 'Modalità di Erogazione',
    'max_participants' => 'Partecipanti Massimi',
    'available_from' => 'Disponibile Dal',
    'available_until' => 'Disponibile Fino Al',
    'active_course' => 'Corso Attivo',
    'mandatory_course' => 'Corso Obbligatorio',

    // Form Options
    'select_category' => 'Seleziona Categoria',
    'select_level' => 'Seleziona Livello',
    'select_method' => 'Seleziona Modalità',
    'all_categories' => 'Tutte le Categorie',
    'all_levels' => 'Tutti i Livelli',
    'all_methods' => 'Tutte le Modalità',

    // Categories
    'categories' => [
        'alimentaristi' => 'Alimentaristi',
        'antincendio' => 'Antincendio',
        'altri_corsi' => 'Altri Corsi',
        'other' => 'Altro'
    ],

    // Levels
    'levels' => [
        'beginner' => 'Principiante',
        'intermediate' => 'Intermedio',
        'advanced' => 'Avanzato'
    ],

    // Delivery Methods
    'delivery_methods' => [
        'online' => 'Online',
        'offline' => 'In Presenza',
        'hybrid' => 'Ibrido'
    ],

    // Status
    'status' => 'Stato',
    'active' => 'Attivo',
    'inactive' => 'Inattivo',
    'mandatory' => 'Obbligatorio',
    'optional' => 'Opzionale',

    // Search and Filter
    'search' => 'Cerca',
    'search_courses' => 'Cerca corsi...',
    'show_inactive_courses' => 'Mostra corsi inattivi',
    'filter' => 'Filtra',
    'clear_filters' => 'Pulisci Filtri',

    // Messages
    'no_courses_found' => 'Nessun corso trovato',
    'no_courses_message' => 'Non ci sono corsi che corrispondono ai tuoi criteri.',
    'create_first_course' => 'Crea il Primo Corso',
    'no_active_courses' => 'Nessun Corso Attivo',
    'no_active_courses_message' => 'Attualmente non ci sono corsi attivi nel sistema.',
    'course_created' => 'Corso creato con successo.',
    'course_updated' => 'Corso aggiornato con successo.',
    'course_deleted' => 'Corso eliminato con successo.',
    'confirm_delete' => 'Sei sicuro di voler eliminare questo corso?',
    'confirm_status_change' => 'Sei sicuro di voler cambiare lo stato del corso da ":old_status" a ":new_status"?',
    'send_notification' => 'Invia Email di Notifica',
    'confirm_send_email' => 'Inviare email di notifica per ":course_title" a:

• Tutti i docenti assegnati
• Responsabile aziendale assegnato
• Tutti gli studenti iscritti

Vuoi procedere?',
    'email_sent_success' => 'Email di notifica inviate con successo!',
    'sending_emails' => 'Invio email in corso...',

    // Course Information
    'course_information' => 'Informazioni del Corso',
    'course_details' => 'Dettagli del Corso',
    'course_settings' => 'Impostazioni del Corso',

    // Units and Values
    'hours' => 'ore',
    'unlimited' => 'Illimitati',
    'n_a' => 'N/D',
    'leave_empty_unlimited' => 'Lascia vuoto per illimitati',
    'from' => 'Dal',
    'until' => 'Fino al',
    'availability' => 'Disponibilità',

    // Planning
    'course_planning' => 'Pianificazione Corsi',
    'total_active_courses' => 'Corsi Attivi Totali',
    'total_categories' => 'Categorie',
    'mandatory_courses' => 'Corsi Obbligatori',
    'total_hours' => 'Ore Totali',
    'courses_count' => ':count corsi',

    // Schedule and Calendar
    'schedule' => 'Programma',
    'course_schedule' => 'Programma del Corso',
    'scheduled_sessions' => 'Sessioni Programmate',
    'session' => 'Sessione',
    'start_date' => 'Data Inizio',
    'start_time' => 'Orario Inizio',
    'end_time' => 'Orario Fine',
    'location' => 'Luogo',
    'max_participants' => 'Partecipanti Massimi',
    'registered_participants' => 'Iscritti',
    'available_spots' => 'Posti Disponibili',

    // Event Status
    'scheduled' => 'Programmato',
    'in_progress' => 'In Corso',
    'completed' => 'Completato',
    'cancelled' => 'Annullato',

    // Calendar
    'calendar' => 'Calendario',
    'today' => 'Oggi',
    'this_week' => 'Questa Settimana',
    'this_month' => 'Questo Mese',
    'upcoming_events' => 'Eventi Prossimi',
    'event_details' => 'Dettagli Evento',
    'no_events_scheduled' => 'Nessun evento programmato',
    'view_calendar' => 'Visualizza Calendario',
    'add_to_calendar' => 'Aggiungi al Calendario',
    'back_to_planning' => 'Torna alla Pianificazione',
    'sessions' => 'sessioni',
    'view_in_calendar' => 'Visualizza nel Calendario',
    'no_events_message' => 'Non ci sono sessioni programmate per questo corso.',
    'view_scheduled_courses' => 'Visualizza i tuoi corsi programmati ed eventi di formazione',
    'close' => 'Chiudi',

    // Table
    'teacher' => 'Docente',
    'actions' => 'Azioni',
    'rows_per_page' => 'Righe per pagina',
    'showing_entries' => 'Mostrando da :from a :to di :total risultati',

    // Course Management (Instances)
    'course_instances' => 'Istanze dei Corsi',
    'all_started_courses' => 'Tutti i corsi avviati e le loro istanze',
    'start_new' => 'Avvia Nuovo',
    'start_new_course' => 'Avvia Nuovo Corso',
    'select_master_course' => 'Seleziona Modello Corso Principale',
    'master_course' => 'Corso Principale',
    'search_course_template' => 'Cerca un modello di corso...',
    'master_course_template' => 'Modello Corso Principale',
    'template' => 'Modello',
    'template_code' => 'Codice Modello',
    'instance_title' => 'Titolo Istanza',
    'instance_code' => 'Codice Istanza',
    'course_instance_details' => 'Dettagli Istanza del Corso',
    'schedule_settings' => 'Impostazioni Pianificazione',
    'end_date' => 'Data Fine',
    'select_teachers' => 'Seleziona Docenti',
    'primary_teacher' => 'Docente Principale',
    'additional_teachers' => 'Docenti Aggiuntivi',
    'optional' => 'Opzionale',
    'assign_teachers' => 'Assegna Docenti',
    'no_teachers_available' => 'Nessun docente disponibile',

    // Student Enrollment
    'enroll_students' => 'Iscrivi Studenti',
    'select_students_to_enroll' => 'Seleziona Studenti da Iscrivere',
    'select_companies_filter_info' => 'Seleziona le aziende sopra per filtrare gli studenti, oppure seleziona da tutti gli utenti qui sotto.',
    'search_students' => 'Cerca studenti...',
    'select_all_visible' => 'Seleziona Tutti Visibili',
    'deselect_all' => 'Deseleziona Tutti',
    'students_selected' => ':count studenti selezionati',
    'no_students_available' => 'Nessuno studente disponibile',
    'assign_to_companies' => 'Assegna alle Aziende (Opzionale)',
    'select_companies' => 'Seleziona Azienda',
    'search_companies' => 'Cerca aziende...',
    'select_companies_to_assign' => 'Seleziona le aziende a cui assegnare questo corso',
    'select_company_to_assign' => 'Seleziona un\'azienda a cui assegnare questo corso',

    // Class Sessions
    'class_sessions' => 'Sessioni del Corso',
    'session_schedule' => 'Programma delle Sessioni',
    'add_session' => 'Aggiungi Sessione',
    'remove_session' => 'Rimuovi Sessione',
    'session_number' => 'Sessione :number',
    'session_title' => 'Titolo Sessione',
    'session_date' => 'Data Sessione',
    'session_time' => 'Orario Sessione',
    'session_duration' => 'Durata (Ore)',
    'session_location' => 'Luogo',
    'session_description' => 'Descrizione',
    'total_sessions' => 'Sessioni Totali',
    'total_session_hours' => 'Ore Totali',
    'remaining_hours' => 'Ore Rimanenti',
    'no_sessions_added' => 'Nessuna sessione aggiunta',
    'auto_generate_sessions' => 'Genera Sessioni Automaticamente',
    'generate_sessions' => 'Genera Sessioni',
    'sessions_per_week' => 'Sessioni per Settimana',
    'hours_per_session' => 'Ore per Sessione',
    'select_days' => 'Seleziona Giorni',
    'monday' => 'Lunedì',
    'tuesday' => 'Martedì',
    'wednesday' => 'Mercoledì',
    'thursday' => 'Giovedì',
    'friday' => 'Venerdì',
    'saturday' => 'Sabato',
    'sunday' => 'Domenica',
    'session_start_time' => 'Ora Inizio',
    'session_end_time' => 'Ora Fine',
    'generate' => 'Genera',
    'exclude_weekends' => 'Escludi Fine Settimana',
    'exclude_italian_holidays' => 'Escludi Festività Italiane',
    'weekly_mode' => 'Settimanale (Una sessione a settimana)',
    'generate_calendar' => 'Genera Calendario',
    'generate_sessions' => 'Genera Sessioni',
];