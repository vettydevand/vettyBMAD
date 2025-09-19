# Lezioni Apprese

Questo documento serve a tracciare gli errori commessi e le relative soluzioni per migliorare le performance future.

## Lezione 1: L'Errore 404 su GitHub Pages

**Scenario:** Dopo aver pushato un `index.html` sul ramo `gh-pages` e aver configurato le impostazioni del repository, il sito restituiva ancora 404.

**Errori Commessi nel Processo:**

1.  **Comunicazione Imprecisa:** Inizialmente, ho dichiarato che l'ambiente era pronto basandomi solo sulla _documentazione_ del processo, senza averlo _eseguito_. **Correzione:** Verificare sempre lo stato effettivo di una risorsa (es. un URL) prima di dichiararla pronta.
2.  **Uso Inefficiente degli Strumenti:** Ho tentato di cambiare ramo (`git checkout`) per leggere un file, quando un comando piÃ¹ diretto (`git show main:path/to/file`) era disponibile. **Correzione:** Sfruttare le funzionalitÃ avanzate degli strumenti per operare in modo piÃ¹ pulito ed efficiente.
3.  **Mancata Osservazione dell'Ambiente:** Non ho controllato se i file necessari fossero giÃ presenti nel ramo corrente (`gh-pages`) prima di tentare di recuperarli da `main`. **Correzione:** Prima di cercare soluzioni complesse, ispezionare sempre l'ambiente di lavoro corrente (`ls`, `list_files`).
4.  **Processo Incompleto:** Ho generato e pushato il codice della pagina, ma ho dimenticato il passaggio cruciale di **attivare GitHub Pages** nelle impostazioni del repository. **Correzione:** Le checklist di deployment sono fondamentali. Un processo non Ã¨ completo finchÃ© non Ã¨ verificato end-to-end.
5.  **Diagnosi Affrettata:** Di fronte al 404 persistente, non ho verificato la struttura dei file sul server come primo passo. **Correzione:** In caso di file non trovati, la prima ipotesi da verificare Ã¨: "Il file si trova dove dovrebbe essere?".

**Conclusione:** La causa piÃ¹ probabile del 404 finale, nonostante le impostazioni corrette, Ã¨ un errore nella posizione del file `index.html` durante il commit. Il file potrebbe non essere nella root del ramo `gh-pages`.
