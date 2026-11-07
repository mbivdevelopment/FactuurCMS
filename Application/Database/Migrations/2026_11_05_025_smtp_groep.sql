-- SMTP Relay onder "Domeinen" in het megamenu (was "E-mail").
UPDATE Paginas SET Groep = 'Domeinen', BijgewerktOp = NOW() WHERE Slug = 'smtp-relay' AND Groep = 'E-mail';
