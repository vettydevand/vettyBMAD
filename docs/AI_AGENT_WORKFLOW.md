# Workflow Operativo dell'Agente AI - Basato sul Metodo BMAD

Questo documento definisce l'algoritmo iterativo che guiderà ogni mia azione all'interno del progetto. Seguirò questa routine per garantire coerenza, disciplina e aderenza ai principi del BMAD.

## Algoritmo Iterativo Principale

Ad ogni interazione, eseguirò i seguenti passaggi:

**FASE 0: Orientamento e Stato**

1.  **Identifica Fase BMAD Corrente:** Determina in quale delle quattro fasi (Build, Measure, Analyze, Document) mi trovo.
2.  **Analizza Richiesta Utente:** Deconstruisci l'input dell'utente per capire l'obiettivo, il contesto e le istruzioni esplicite.

**FASE 1: Pianificazione (Plan)**

1.  **Consulta Documentazione di Supporto:** Rileggi i file pertinenti per allineare il piano agli obiettivi e alla metodologia del progetto.
2.  **Formula un Piano d'Azione:** Definisci una sequenza chiara di azioni da intraprendere in base alla fase BMAD corrente.
3.  **Presenta Piano (se necessario):** Se il piano è complesso o introduce modifiche significative, presentalo all'utente per approvazione.

**FASE 2: Esecuzione (Execute)**

1.  **Esegui Azioni del Piano:** Utilizza gli strumenti a disposizione per implementare il piano.

**FASE 3: Rapporto e Transizione (Report & Transition)**

1.  **Comunica Risultati:** Riassumi brevemente le azioni completate e il risultato ottenuto.
2.  **Dichiara Transizione di Fase:** Annuncia esplicitamente il passaggio alla fase successiva del ciclo BMAD.

**FASE 4: Sincronizzazione Git (Sync)**

1.  **Valuta Necessità di Sync:** Al termine di un ciclo di lavoro significativo (es. una feature completata, una documentazione importante scritta), valuta se è il momento di sincronizzare il lavoro con il repository remoto.
2.  **Esegui Comandi Git:** Se la sincronizzazione è necessaria:
    - Esegui `git status` per verificare le modifiche.
    - Esegui `git add .` per includere tutte le modifiche.
    - Esegui `git commit -m "messaggio"` con un messaggio chiaro e descrittivo che riassuma il lavoro svolto.
    - Esegui `git pull` per integrare eventuali modifiche remote (buona pratica).
    - Esegui `git push` per caricare le modifiche.
3.  **Conferma Sincronizzazione:** Comunica all'utente che il repository è stato aggiornato.

**FASE 5: Loop**

1.  Ritorna alla **FASE 0**. Il ciclo è continuo.
