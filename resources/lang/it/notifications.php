<?php

return [
    // Page titles and headers
    'title' => 'Notifiche',
    'page_title' => 'Notifiche',
    'no_notifications' => 'Nessuna notifica',
    'no_new_notifications' => 'Nessuna nuova notifica',
    'view_all' => 'Visualizza tutte le notifiche',
    'mark_all_read' => 'Segna tutte come lette',
    'mark_as_read' => 'Segna come letta',
    'view_details' => 'Visualizza Dettagli',

    // Filter options
    'filter_all' => 'Tutte',
    'filter_unread' => 'Non lette',
    'filter_read' => 'Lette',

    // Messages
    'no_notifications_found' => 'Nessuna notifica trovata',
    'no_unread_notifications' => 'Non hai notifiche non lette.',
    'no_read_notifications' => 'Non hai notifiche lette.',
    'no_notifications_yet' => 'Non hai ancora nessuna notifica.',
    'notification_marked_read' => 'Notifica segnata come letta',
    'all_notifications_marked_read' => 'Tutte le notifiche segnate come lette',
    'notification_deleted' => 'Notifica eliminata con successo',
    'notification_not_found' => 'Notifica non trovata',
    'confirm_delete' => 'Sei sicuro di voler eliminare questa notifica?',

    // Notification types
    'types' => [
        'user_approval_request' => 'Richiesta Approvazione Utente',
        'user_approved' => 'Utente Approvato',
        'user_rejected' => 'Registrazione Utente Rifiutata',
        'company_invitation' => 'Invito Aziendale',
        'course_enrollment' => 'Iscrizione Corso',
        'certificate_issued' => 'Certificato Emesso',
        'system_update' => 'Aggiornamento Sistema',
        'warning' => 'Attenzione',
        'success' => 'Successo',
        'info' => 'Informazione',
    ],

    // User approval notifications
    'user_approval' => [
        'new_request' => 'Nuova Richiesta Approvazione Utente',
        'bulk_request' => 'Nuova Richiesta Approvazione Utenti Multipli',
        'single_user_message' => 'Nuovo utente :name inviato per approvazione da :company',
        'multiple_users_message' => ':count utenti inviati per approvazione da :company',
        'approved_title_single' => 'Utente Approvato',
        'approved_title_multiple' => 'Utenti Approvati',
        'approved_message_single' => "L'utente :name è stato approvato",
        'approved_message_multiple' => ':count utenti sono stati approvati',
        'rejected_title_single' => 'Registrazione Utente Rifiutata',
        'rejected_title_multiple' => 'Registrazioni Utenti Rifiutate',
        'rejected_message_single' => 'La registrazione utente per :name è stata rifiutata',
        'rejected_message_multiple' => ':count registrazioni utenti sono state rifiutate',
        'please_review' => 'Si prega di rivedere e approvare o rifiutare le registrazioni utente.',
        'review_pending' => 'Rivedi Approvazioni in Sospeso',
        'unknown_company' => 'Azienda Sconosciuta',
    ],

    // Email notifications
    'mail' => [
        'greeting' => 'Ciao :name,',
        'approval_request_subject' => 'Nuova Richiesta Approvazione Utente',
        'approval_request_single' => 'Un nuovo utente è stato inviato per approvazione da :company.',
        'approval_request_multiple' => ':count nuovi utenti sono stati inviati per approvazione da :company.',
        'and_more' => '... e altri :count utente/i',
        'thank_you' => 'Grazie per la gestione del sistema STA!',
        'thank_you_general' => 'Grazie per aver utilizzato il sistema STA!',

        'user_approved_subject' => 'Conferma Approvazione Utente',
        'user_approved_single' => "Buone notizie! L'utente che hai inviato è stato approvato da :approver.",
        'user_approved_multiple' => 'Buone notizie! :count utenti che hai inviato sono stati approvati da :approver.',
        'account_active_single' => "L'account utente è ora attivo e può accedere al sistema.",
        'account_active_multiple' => 'Tutti gli account utente sono ora attivi e possono accedere al sistema.',
        'view_company_users' => 'Visualizza Utenti Azienda',

        'user_rejected_subject' => 'Registrazione Utente Rifiutata',
        'user_rejected_single' => 'La registrazione utente che hai inviato è stata esaminata e rifiutata da :rejector.',
        'user_rejected_multiple' => ':count registrazioni utenti che hai inviato sono state esaminate e rifiutate da :rejector.',
        'reason_for_rejection' => 'Motivo del rifiuto: :reason',
        'contact_sta_manager' => 'Se ritieni che questo sia un errore o hai bisogno di ulteriori chiarimenti, contatta il Manager STA.',
        'resubmit_info' => 'Puoi reinviare la registrazione utente con informazioni corrette, se necessario.',
        'submit_new_user' => 'Invia Nuovo Utente',
    ],

    // Generic notification messages
    'course_enrollment_title' => 'Conferma Iscrizione Corso',
    'course_enrollment_message' => 'Sei stato iscritto al corso: :course',
    'certificate_issued_title' => 'Certificato Emesso',
    'certificate_issued_message' => 'Il tuo certificato per :certificate è stato emesso ed è pronto per il download.',
    'company_invitation_title' => 'Invito Aziendale',
    'company_invitation_message' => 'Sei stato invitato a unirti a :company.',

    // Time formats
    'time' => [
        'just_now' => 'Proprio ora',
        'minutes_ago' => ':count minuto fa|:count minuti fa',
        'hours_ago' => ':count ora fa|:count ore fa',
        'days_ago' => ':count giorno fa|:count giorni fa',
        'weeks_ago' => ':count settimana fa|:count settimane fa',
        'months_ago' => ':count mese fa|:count mesi fa',
        'years_ago' => ':count anno fa|:count anni fa',
    ],

    // Badge labels
    'new_badge' => 'Nuovo',
    'unread_count' => ':count non lette',

    // Actions
    'actions' => [
        'mark_read' => 'Segna come Letta',
        'mark_all_read' => 'Segna Tutte come Lette',
        'delete' => 'Elimina',
        'view_details' => 'Visualizza Dettagli',
        'view_all' => 'Visualizza Tutte le Notifiche',
    ],
];
