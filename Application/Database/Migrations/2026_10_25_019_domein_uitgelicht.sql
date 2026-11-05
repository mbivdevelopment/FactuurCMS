-- Per dienstsoort precies één uitgelicht pakket; Domeinnamen had er geen (.nl aan).
UPDATE Producten SET Uitgelicht = 1 WHERE Slug = 'nl-domeinnaam';
