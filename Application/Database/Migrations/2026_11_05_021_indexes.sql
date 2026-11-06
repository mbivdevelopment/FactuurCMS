-- Extra indexes voor veelgebruikte filters in beheer en cron.
ALTER TABLE Documenten ADD KEY IxVerval (Soort, Status, VervalDatum);
ALTER TABLE Tickets ADD KEY IxReactie (Status, LaatsteReactie);
