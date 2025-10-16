<?php

return [
    // Page title
    'page_title' => 'Importazione Utenti Massiva',
    'page_subtitle' => 'Importa più utenti contemporaneamente utilizzando un foglio di calcolo Excel',

    // Header buttons
    'company_users' => 'Utenti Azienda',
    'my_profile' => 'Il Mio Profilo',

    // Instructions section
    'how_to_use' => 'Come Utilizzare l\'Importazione Massiva',

    // Step 1
    'step_1_title' => 'Passo 1: Scarica il Modello',
    'step_1_1' => 'Clicca il pulsante "Scarica Modello Excel" qui sotto',
    'step_1_2' => 'Apri il file in Excel o LibreOffice',
    'step_1_3' => 'Rivedi i dati di esempio e le istruzioni incluse',

    // Step 2
    'step_2_title' => 'Passo 2: Compila i Dati Utente',
    'step_2_1' => '<strong>Elimina la riga dei dati di esempio</strong> (riga 2)',
    'step_2_2' => 'Aggiungi i tuoi utenti partendo dalla riga 3',
    'step_2_3' => 'Compila i campi obbligatori: Nome, Cognome, Email, Percentuale',
    'step_2_4' => 'Campi opzionali: Telefono, Cellulare, Data di Nascita, ecc.',

    // Step 3
    'step_3_title' => 'Passo 3: Carica il File',
    'step_3_1' => 'Salva il tuo file Excel',
    'step_3_2' => 'Seleziona l\'azienda di destinazione dal menu a discesa',
    'step_3_3' => 'Carica il file utilizzando il modulo qui sotto',
    'step_3_4' => 'Rivedi i risultati dell\'importazione',

    // Important notes
    'important_notes' => 'Note Importanti:',
    'note_password' => 'Tutti i nuovi utenti avranno la password predefinita: <strong>"password"</strong>',
    'note_status' => 'Gli utenti verranno creati con stato: <strong>"Parcheggiato"</strong> (in attesa di approvazione STA)',
    'note_unique_email' => 'Gli indirizzi email devono essere univoci nel sistema',
    'note_max_size' => 'Dimensione massima del file: 10MB',

    // Download template section
    'download_template_title' => 'Passo 1: Scarica il Modello',
    'excel_template' => 'Modello di Importazione Excel',
    'template_description' => 'Scarica un file Excel preformattato con intestazioni, dati di esempio e istruzioni.',

    'feature_1' => '<strong>12 colonne</strong> con intestazioni chiare',
    'feature_2' => 'Riga di dati di esempio per riferimento',
    'feature_3' => 'Istruzioni dettagliate incluse',
    'feature_4' => 'Formattato e stilizzato correttamente',

    'download_button' => 'Scarica Modello Excel',
    'file_format' => 'Formato file: .xlsx',

    // Upload section
    'upload_title' => 'Passo 2: Carica il Modello Compilato',
    'upload_errors' => 'Errori di Caricamento',
    'import_errors' => 'Errori di Importazione ({count} righe saltate)',

    'select_company' => 'Seleziona Azienda di Destinazione',
    'choose_company' => 'Scegli azienda...',
    'company_info' => 'Gli utenti verranno aggiunti all\'azienda selezionata',

    'upload_excel' => 'Carica File Excel',
    'accepted_formats' => 'Formati accettati: .xlsx, .xls | Dimensione max: 10MB',

    // Before uploading
    'before_uploading' => 'Prima di caricare:',
    'check_1' => 'Assicurati di aver <strong>eliminato la riga dei dati di esempio</strong>',
    'check_2' => 'Verifica che tutti i <strong>campi obbligatori</strong> siano compilati (Nome, Cognome, Email, Percentuale)',
    'check_3' => 'Verifica che tutti gli <strong>indirizzi email siano validi</strong> e univoci',
    'check_4' => 'Assicurati che i <strong>valori percentuali siano tra 1-100</strong>',

    // Buttons
    'upload_import_button' => 'Carica e Importa Utenti',
    'cancel_button' => 'Annulla',

    // Template columns reference
    'columns_reference_title' => 'Riferimento Colonne Modello Excel',
    'column_header' => 'Nome Colonna',
    'description_header' => 'Descrizione',
    'required_header' => 'Obbligatorio',
    'example_header' => 'Esempio',

    'required_badge' => 'Obbligatorio',
    'optional_badge' => 'Opzionale',

    // Column descriptions
    'col_name' => 'Nome',
    'col_name_desc' => 'Nome dell\'utente',
    'col_surname' => 'Cognome',
    'col_surname_desc' => 'Cognome dell\'utente',
    'col_email' => 'Email',
    'col_email_desc' => 'Indirizzo email valido (deve essere univoco)',
    'col_phone' => 'Telefono',
    'col_phone_desc' => 'Numero di telefono',
    'col_mobile' => 'Cellulare',
    'col_mobile_desc' => 'Numero di cellulare',
    'col_dob' => 'Data di Nascita',
    'col_dob_desc' => 'Formato: AAAA-MM-GG',
    'col_pob' => 'Luogo di Nascita',
    'col_pob_desc' => 'Città o località di nascita',
    'col_country' => 'Paese',
    'col_country_desc' => 'Nome del paese',
    'col_gender' => 'Genere',
    'col_gender_desc' => 'Deve essere: maschio, femmina, o altro',
    'col_cf' => 'CF (Codice Fiscale)',
    'col_cf_desc' => 'Codice fiscale italiano (max 16 caratteri)',
    'col_address' => 'Indirizzo',
    'col_address_desc' => 'Indirizzo completo',
    'col_percentage' => 'Percentuale Azienda',
    'col_percentage_desc' => 'Percentuale di proprietà (1-100)',

    // After import
    'after_import' => 'Dopo l\'Importazione:',
    'after_import_1' => 'Tutti gli utenti avranno la password predefinita: <code>password</code>',
    'after_import_2' => 'Gli utenti dovrebbero cambiare la password al primo accesso',
    'after_import_3' => 'Gli utenti verranno creati con stato <strong>"Parcheggiato"</strong>',
    'after_import_4' => 'Il Manager STA deve approvare gli utenti prima che possano accedere al sistema',
    'after_import_5' => 'A tutti gli utenti verrà assegnato il ruolo <strong>"Utente Finale"</strong>',
];
