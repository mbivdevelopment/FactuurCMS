-- Informatiepagina "SMTP Relay" (oplossingen-pagina; koppelt automatisch aan de dienstsoort).
INSERT INTO Paginas (Categorie, Groep, Titel, Slug, Icoon, Samenvatting, Inhoud, ProductGroep, Zichtbaar, Volgorde, AangemaaktOp)
SELECT 'cloud', 'E-mail', 'SMTP Relay', 'smtp-relay', 'send',
  'Betrouwbare e-mailverzending voor websites en applicaties, met SPF, DKIM en DMARC goed geregeld.',
  'Met SMTP Relay verstuur je transactionele e-mail en nieuwsbrieven via onze eigen mailinfrastructuur in Nederland. Je website of applicatie verbindt via standaard SMTP of de e-mail-API; wij zorgen voor hoge inbox-komst, correcte authenticatie (SPF, DKIM, DMARC) en inzage in bezorgde en gebouncte berichten.',
  NULL, 1, 60, NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM Paginas WHERE Slug = 'smtp-relay');
